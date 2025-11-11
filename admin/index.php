<?php require_once __DIR__ . '/admin_header.php';
$totalSales = $pdo->query("SELECT IFNULL(SUM(grand_total),0) FROM orders")->fetchColumn();
$ordersCount = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$productsCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
?>
<h2>Dashboard</h2>
<div style="display:flex;gap:16px">
  <div style="padding:12px;border:1px solid #eee">Total Sales: ৳ <?php echo number_format($totalSales,2); ?></div>
  <div style="padding:12px;border:1px solid #eee">Orders: <?php echo $ordersCount; ?></div>
  <div style="padding:12px;border:1px solid #eee">Products: <?php echo $productsCount; ?></div>
</div>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
