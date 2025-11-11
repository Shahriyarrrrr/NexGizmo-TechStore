<?php require_once __DIR__ . '/header.php';
$catSlug = $_GET['category'] ?? null;
$query = $_GET['q'] ?? null;

$title = 'All Products';
if ($catSlug) $title = 'Category: ' . htmlspecialchars($catSlug);
if ($query) $title = 'Search: ' . htmlspecialchars($query);

$products = fetch_products($pdo, [
  'category_slug' => $catSlug,
  'q' => $query
]);
?>
<section class="products-page">
  <h3><?php echo $title; ?></h3>
  <div class="products-grid" style="display:flex;gap:16px;flex-wrap:wrap">
    <?php if (!$products): ?>
      <p>No products found.</p>
    <?php else: foreach ($products as $p):
      $out = (!$p['allow_backorder'] && (int)$p['stock']<=0);
    ?>
      <div class="product" style="border:1px solid #eee;padding:12px;width:220px;border-radius:8px">
        <a href="/NexGizmo/product-details.php?slug=<?php echo urlencode($p['slug']); ?>">
          <img src="<?php echo htmlspecialchars($p['image_url']?:'/NexGizmo/assets/images/placeholder.png'); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="width:100%;height:140px;object-fit:cover">
          <h4><?php echo htmlspecialchars($p['name']); ?></h4>
        </a>
        <div class="prices">
          <span class="price">৳ <?php echo number_format($p['price'],0); ?></span>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center">
          <small><?php echo $out ? 'Out of stock' : ('Stock: '.(int)$p['stock']); ?></small>
          <?php if ($out): ?>
            <button class="btn" disabled>Unavailable</button>
          <?php else: ?>
            <button class="btn" onclick="addToCart(<?php echo $p['id']; ?>,1)">Add to Cart</button>
            <a class="btn" href="/NexGizmo/product-details.php?slug=<?php echo urlencode($p['slug']); ?>">View</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endforeach; endif; ?>
  </div>
</section>
<?php require_once __DIR__ . '/footer.php'; ?>
