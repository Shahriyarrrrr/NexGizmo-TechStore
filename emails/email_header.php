<?php
$logo = BASE_URL . '/assets/images/logo.png';
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo APP_NAME; ?></title>
<style>
  body { font-family: Arial, sans-serif; background: #f7f7f7; margin: 0; padding: 0; }
  .email-container { background: #fff; margin: auto; width: 90%; max-width: 600px; border-radius: 8px; overflow: hidden; }
  .email-header { background: #111827; color: white; padding: 16px; text-align: center; }
  .email-content { padding: 20px; color: #333; }
  .email-footer { background: #111827; color: white; padding: 16px; text-align: center; font-size: 12px; }
  a { color: #3b82f6; }
</style>
</head>
<body>
<div class="email-container">
<div class="email-header">
  <h2><?php echo APP_NAME; ?></h2>
</div>
<div class="email-content">
