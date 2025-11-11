<?php
// place_order.php
require_once __DIR__ . '/functions/functions.php';
require_once __DIR__ . '/config/app.php';

$items = cart_items($pdo);
if (!$items) { header('Location: /NexGizmo/cart.php'); exit; }

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$location = $_POST['location'] ?? 'inside_dhaka';
$couponCode = trim($_POST['coupon'] ?? '');
$pm = $_POST['payment_method'] ?? 'cod';

if ($name === '' || $phone === '' || $address === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($pm, ['cod','online'])) {
  die('Invalid checkout data.');
}
if (!in_array($location, ['inside_dhaka','outside_dhaka'])) $location = 'inside_dhaka';

/* ---- Stock re-check before creating order ---- */
foreach ($items as $it) {
  if (!$it['allow_backorder'] && $it['qty'] > (int)$it['stock']) {
    die('Insufficient stock for ' . htmlspecialchars($it['name']));
  }
}

/* ---- Compute totals (delivery, coupon) ---- */
$tot = compute_order_totals($pdo, $location, $couponCode, $email);

$pdo->beginTransaction();
try {
  $stmt = $pdo->prepare("INSERT INTO orders (user_id, name, email, phone, address, location, payment_method, status, subtotal, delivery_fee, discount_amount, coupon_code, grand_total, currency, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
  $uid = current_user()['id'] ?? null;
  $status = 'pending';
  $stmt->execute([
    $uid, $name, $email, $phone, $address, $location, $pm, $status,
    $tot['subtotal'], $tot['delivery_fee'], $tot['discount'], $tot['coupon_code'], $tot['grand_total'], CURRENCY
  ]);
  $orderId = $pdo->lastInsertId();

  $oi = $pdo->prepare("INSERT INTO order_items (order_id, product_id, qty, price, line_total) VALUES (?,?,?,?,?)");
  foreach ($items as $it) {
    $oi->execute([$orderId, $it['id'], $it['qty'], $it['price'], $it['line_total']]);
  }

  if ($tot['coupon'] && $tot['discount'] > 0) {
    record_coupon_usage($pdo, (int)$tot['coupon']['id'], $email);
  }

  foreach ($items as $it) {
    if (!$it['allow_backorder']) {
      $upd = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id=?");
      $upd->execute([$it['qty'], $it['id']]);
    }
  }

  if ($pm === 'online') {
    $pay = $pdo->prepare("INSERT INTO payments (order_id, method, gateway, status, amount, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $pay->execute([$orderId, 'online', 'sslcommerz', 'initiated', $tot['grand_total']]);

    $pdo->commit();
    $_SESSION['cart'] = [];
    header('Location: ' . BASE_URL . '/payment/initiate.php?order_id=' . $orderId);
    exit;
  } else {
    $pdo->commit();
    $_SESSION['cart'] = [];

    // Generate invoice + email to customer
    require_once __DIR__ . '/lib/Invoice.php';
    require_once __DIR__ . '/lib/Mailer.php';
    try {
      $pdfPath = Invoice::generate($orderId);

      // customer email (order received + invoice)
      ob_start();
      $name = $name;
      $orderIdVar = $orderId;
      $grand = $tot['grand_total'];
      include __DIR__ . '/emails/order_customer.php';
      $emailBody = ob_get_clean();

      $m = new Mailer();
      $m->sendToCustomer($email, "Your NexGizmo Order #$orderId", $emailBody, [$pdfPath]);
      $m->sendToAdmin("New COD Order #$orderId", "A new COD order was placed.", [$pdfPath]);
    } catch (Exception $e) { /* ignore */ }

    header('Location: ' . BASE_URL . '/admin/order-details.php?id=' . $orderId);
    exit;
  }

} catch (Exception $e) {
  $pdo->rollBack();
  die('Order failed: ' . $e->getMessage());
}
