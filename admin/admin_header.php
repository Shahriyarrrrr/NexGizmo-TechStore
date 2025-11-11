<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../functions/functions.php';
if (!isset($_SESSION['admin'])) { header('Location: /NexGizmo/admin/login.php'); exit; }
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width"><title>Admin - NexGizmo</title><link rel="stylesheet" href="/NexGizmo/assets/css/admin.css"></head><body>
<header style="padding:12px;background:#111;color:#fff"><a href="/NexGizmo/admin/index.php" style="color:#fff">Admin</a> | <a href="/NexGizmo/admin/logout.php" style="color:#fff">Logout</a></header><main style="padding:16px">
