<?php include __DIR__ . '/email_header.php'; ?>
<p>New order received: <strong>#<?php echo (int)$orderIdVar; ?></strong></p>
<p>Customer: <?php echo htmlspecialchars($name); ?> — Total: ৳<?php echo number_format((float)$grand,2); ?></p>
<?php include __DIR__ . '/email_footer.php'; ?>
