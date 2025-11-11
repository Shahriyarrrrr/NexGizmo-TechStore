<?php
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/functions/functions.php';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo APP_NAME; ?></title>
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<script src="<?php echo BASE_URL; ?>/assets/js/script.js" defer></script>
</head>

<body>
<header class="header">
  <div class="container nav">
    <div class="logo">
      <a href="<?php echo BASE_URL; ?>"><?php echo APP_NAME; ?></a>
    </div>
    <nav class="links">
      <a href="<?php echo BASE_URL; ?>/products.php">Products</a>
      <a href="<?php echo BASE_URL; ?>/cart.php">🛒 Cart (<?php echo cart_count(); ?>)</a>
      <button id="themeToggle" class="theme-toggle">🌙 Dark Mode</button>
    </nav>
  </div>
</header>

<main class="container">
