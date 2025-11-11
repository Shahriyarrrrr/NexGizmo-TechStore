<?php
// add_to_cart.php
require_once __DIR__ . '/functions/functions.php';
header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
if ($id <= 0) { http_response_code(400); echo json_encode(['ok'=>false]); exit; }

$stmt = $pdo->prepare("SELECT stock, allow_backorder FROM products WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); echo json_encode(['ok'=>false]); exit; }

$inCart = $_SESSION['cart'][$id] ?? 0;
$requested = max(1, $qty) + $inCart;

if (!$row['allow_backorder'] && $requested > (int)$row['stock']) {
  echo json_encode(['ok'=>false, 'msg'=>'Not enough stock']); exit;
}

add_to_cart($id, max(1,$qty));
echo json_encode(['ok'=>true, 'count'=>cart_count()]);
