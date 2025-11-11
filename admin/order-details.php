<?php require_once __DIR__ . '/admin_header.php';

$id = (int)($_GET['id'] ?? 0);
$ord = $pdo->prepare("SELECT * FROM orders WHERE id=?");
$ord->execute([$id]);
$order = $ord->fetch(PDO::FETCH_ASSOC);
if (!$order) { echo "<p>Order not found.</p></main></body></html>"; exit; }

$items = $pdo->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?");
$items->execute([$id]);
$rows = $items->fetchAll(PDO::FETCH_ASSOC);

$pay = $pdo->prepare("SELECT * FROM payments WHERE order_id=? ORDER BY id DESC");
$pay->execute([$id]);
$payments = $pay->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Order #<?php echo $order['id']; ?></h2>
<div class="row cards" style="margin-bottom:16px">
  <div class="card"><b>Customer:</b><br><?php echo htmlspecialchars($order['name']); ?><br><?php echo htmlspecialchars($order['phone']); ?><br><?php echo htmlspecialchars($order['email']); ?></div>
  <div class="card"><b>Address:</b><br><?php echo nl2br(htmlspecialchars($order['address'])); ?><br><small>Location: <?php echo htmlspecialchars($order['location']); ?></small></div>
  <div class="card"><b>Payment:</b><br><?php echo strtoupper($order['payment_method']); ?><br>Status: <?php echo htmlspecialchars($order['status']); ?></div>
  <div class="card">
    <b>Totals</b><br>
    Subtotal: ৳ <?php echo number_format($order['subtotal'],2); ?><br>
    Delivery: ৳ <?php echo number_format($order['delivery_fee'],2); ?><br>
    Discount: ৳ <?php echo number_format($order['discount_amount'],2); ?> <?php if($order['coupon_code']) echo ' ('.htmlspecialchars($order['coupon_code']).')'; ?><br>
    <b>Grand Total: ৳ <?php echo number_format($order['grand_total'],2); ?></b>
  </div>
</div>

<?php
$invPath = __DIR__ . "/../invoices/INV-{$order['id']}.pdf";
$invUrl  = '/NexGizmo/invoices/INV-'.$order['id'].'.pdf';
?>
<div style="margin-bottom:12px">
  <?php if (is_file($invPath)): ?>
    <a class="btn" href="<?php echo $invUrl; ?>" target="_blank">Download Invoice PDF</a>
  <?php else: ?>
    <a class="btn" href="/NexGizmo/admin/generate-invoice.php?id=<?php echo $order['id']; ?>">Generate Invoice</a>
  <?php endif; ?>
</div>

<h3>Items</h3>
<table class="table">
<thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
  <td><?php echo htmlspecialchars($r['name']); ?></td>
  <td><?php echo $r['qty']; ?></td>
  <td>৳ <?php echo number_format($r['price'],2); ?></td>
  <td>৳ <?php echo number_format($r['line_total'],2); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<h3>Payments</h3>
<table class="table">
<thead><tr><th>Method</th><th>Gateway</th><th>Status</th><th>Amount</th><th>Txn ID</th><th>Date</th></tr></thead>
<tbody>
<?php foreach($payments as $p): ?>
<tr>
  <td><?php echo htmlspecialchars($p['method']); ?></td>
  <td><?php echo htmlspecialchars($p['gateway']); ?></td>
  <td><?php echo htmlspecialchars($p['status']); ?></td>
  <td>৳ <?php echo number_format($p['amount'],2); ?></td>
  <td><?php echo htmlspecialchars($p['transaction_id'] ?? ''); ?></td>
  <td><?php echo $p['created_at']; ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
