<?php
// config/app.php
define('APP_NAME', 'NexGizmo');
define('BASE_URL', 'http://localhost/NexGizmo'); // change if different
define('CURRENCY', 'BDT');
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

ini_set('display_errors', 1);
error_reporting(E_ALL);
