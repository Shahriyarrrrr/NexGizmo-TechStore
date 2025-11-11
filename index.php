<?php require_once __DIR__ . '/header.php';
$products = fetch_products($pdo);
?>
<h1>Welcome to NexGizmo</h1>
<div class="product-grid">
<?php foreach($products as $p): ?>
  <div class="product">
    <img src="<?php echo htmlspecialchars($p['image_url']?:'/NexGizmo/assets/images/placeholder.png'); ?>" style="width:100%;height:140px;object-fit:cover">
    <h3><?php echo htmlspecialchars($p['name']); ?></h3>
    <p>৳ <?php echo number_format($p['price'],0); ?></p>
    <a class="btn" href="/NexGizmo/product-details.php?slug=<?php echo urlencode($p['slug']); ?>">View</a>
  </div>
<?php endforeach; ?>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>