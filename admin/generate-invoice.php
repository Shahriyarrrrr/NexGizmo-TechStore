<?php
require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../lib/Invoice.php';

$id = (int)($_GET['id'] ?? 0);
try {
  $path = Invoice::generate($id);
  header('Location: /NexGizmo/admin/order-details.php?id='.$id);
} catch (Exception $e) {
  echo "<p>Failed to generate: ".htmlspecialchars($e->getMessage())."</p>";
}
