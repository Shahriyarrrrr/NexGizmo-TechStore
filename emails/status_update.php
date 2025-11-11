<?php include __DIR__ . '/email_header.php'; ?>
<p>Your order #<?php echo (int)$orderId; ?> status changed to <?php echo htmlspecialchars($status); ?></p>
<?php include __DIR__ . '/email_footer.php'; ?>
