<?php require_once __DIR__ . '/admin_header.php';
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>
<h2>Orders</h2>
<table border="1" style="width:100%;border-collapse:collapse">
<thead><tr><th>ID</th><th>Name</th><th>Total</th><th>Status</th><th>Created</th><th>Action</th></tr></thead>
<tbody>
<?php foreach($orders as $o): ?>
<tr>
  <td><?php echo $o['id']; ?></td>
  <td><?php echo htmlspecialchars($o['name']); ?></td>
  <td>৳<?php echo number_format($o['grand_total'],2); ?></td>
  <td><?php echo htmlspecialchars($o['status']); ?></td>
  <td><?php echo $o['created_at']; ?></td>
  <td><a href="/NexGizmo/admin/order-details.php?id=<?php echo $o['id']; ?>">View</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php require_once __DIR__ . '/admin_footer.php'; ?>
