<?php
// update_cart.php
require_once __DIR__ . '/functions/functions.php';
header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);
if ($id <= 0) { http_response_code(400); echo json_encode(['ok'=>false]); exit; }

$stmt = $pdo->prepare("SELECT stock, allow_backorder, price FROM products WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); echo json_encode(['ok'=>false]); exit; }

if (!$row['allow_backorder'] && $qty > (int)$row['stock']) {
  echo json_encode(['ok'=>false, 'msg'=>'Not enough stock']); exit;
}

update_cart_qty($id, max(1,$qty));
echo json_encode(['ok'=>true, 'count'=>cart_count(), 'subtotal'=>cart_subtotal($pdo)]);
