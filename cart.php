<?php require_once __DIR__ . '/header.php';
$items = cart_items($pdo);
if (!$items) echo "<p>Your cart is empty</p>";
else {
  echo "<h2>Your Cart</h2><ul>";
  foreach($items as $it) echo "<li>".$it['name']." x ".$it['qty']." = ৳ ".number_format($it['line_total'],2)."</li>";
  echo "</ul><p>Subtotal: ৳ ".number_format(cart_subtotal($pdo),2)."</p>";
  echo "<p><a href='/NexGizmo/checkout.php' class='btn'>Checkout</a></p>";
}
require_once __DIR__ . '/footer.php';