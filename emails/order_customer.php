<?php include __DIR__ . '/email_header.php'; ?>
<p>Hi <b><?php echo htmlspecialchars($name); ?></b>,</p>
<p>Thank you for your order <b>#<?php echo (int)$orderIdVar; ?></b> at <?php echo APP_NAME; ?>.</p>
<p><b>Total Payable:</b> ৳<?php echo number_format((float)$grand, 2); ?></p>
<p>The PDF invoice is attached. You can also download it anytime from your order page.</p>
<?php include __DIR__ . '/email_footer.php'; ?>
