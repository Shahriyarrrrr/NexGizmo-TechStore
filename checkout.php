<?php
require_once __DIR__ . '/header.php';
$items = cart_items($pdo);
if (!$items) {
  echo "<section class='auth'><h3>Checkout</h3><p>Your cart is empty.</p></section>";
  require_once __DIR__ . '/footer.php'; exit;
}
$subtotal = cart_subtotal($pdo);
$user = current_user();
?>
<section class="auth">
  <h3>Checkout</h3>
  <form method="post" action="/NexGizmo/place_order.php" class="auth-form">
    <label>Name</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>

    <label>Phone</label>
    <input type="text" name="phone" placeholder="01XXXXXXXXX" required>

    <label>Address</label>
    <textarea name="address" rows="3" required></textarea>

    <label>Delivery Location</label>
    <select name="location" required>
      <option value="inside_dhaka">Inside Dhaka</option>
      <option value="outside_dhaka">Outside Dhaka</option>
    </select>

    <label>Coupon Code (optional)</label>
    <input type="text" name="coupon" placeholder="e.g., SAVE100">

    <label>Payment Method</label>
    <select name="payment_method" required>
      <option value="cod">Cash on Delivery</option>
      <option value="online">Online Payment (SSLCommerz)</option>
    </select>

    <div class="cart-summary" style="margin-top:10px">
      <div class="total">Subtotal: <b>৳ <?php echo number_format($subtotal,0); ?></b></div>
      <button class="btn primary" type="submit">Place Order</button>
    </div>
  </form>
</section>
<?php require_once __DIR__ . '/footer.php'; ?>
