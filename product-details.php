<?php
// product-details.php
require_once __DIR__ . '/header.php';
$slug = $_GET['slug'] ?? '';
$product = fetch_product_by_slug($pdo, $slug);
if (!$product) {
  echo "<section class='container'><h3>Product not found.</h3></section>";
  require_once __DIR__ . '/footer.php';
  exit;
}
$out = (!$product['allow_backorder'] && (int)$product['stock']<=0);
?>
<section class="product-detail">
  <div class="left" style="float:left;width:50%">
    <img src="<?php echo htmlspecialchars($product['image_url']?:'/NexGizmo/assets/images/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%;max-width:420px;">
  </div>
  <div class="right" style="float:right;width:45%">
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <p class="short"><?php echo htmlspecialchars($product['short_desc']); ?></p>
    <div class="prices">
      <span class="price">৳ <?php echo number_format($product['price'],0); ?></span>
      <?php if ($product['old_price']): ?>
        <span class="old">৳ <?php echo number_format($product['old_price'],0); ?></span>
      <?php endif; ?>
    </div>
    <p><small><?php echo $out ? 'Out of stock' : ('In stock: '.(int)$product['stock']); ?><?php if($product['allow_backorder']) echo ' (Pre-order allowed)'; ?></small></p>
    <form class="add-form" method="post" onsubmit="return false;">
      <label>Qty:</label>
      <input type="number" min="1" value="1" id="qty" <?php echo $out?'disabled':''; ?>>
      <button type="button" class="btn add" data-id="<?php echo $product['id']; ?>" onclick="addToCart(<?php echo $product['id']; ?>, parseInt(document.getElementById('qty').value || 1))" <?php echo $out?'disabled':''; ?>>
        <?php echo $out ? 'Unavailable' : 'Add to Cart'; ?>
      </button>
    </form>
    <p class="desc"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
  </div>
  <div style="clear:both"></div>
</section>
<?php require_once __DIR__ . '/footer.php'; ?>
