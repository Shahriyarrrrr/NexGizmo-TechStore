<?php
session_start();
unset($_SESSION['admin']);
header('Location: /NexGizmo/admin/login.php');