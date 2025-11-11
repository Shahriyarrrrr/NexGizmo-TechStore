<?php
require_once __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = $_POST['email']; $pass = $_POST['password'];
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
  $stmt->execute([$email]); $u = $stmt->fetch();
  if ($u && password_verify($pass,$u['password'])) { $_SESSION['user']=$u; header('Location: /NexGizmo/'); exit; }
  $err = "Invalid credentials";
}
?><!doctype html><html><body><h2>Login</h2><?php if(!empty($err)) echo "<p style='color:red'>$err</p>"; ?><form method="post"><input name="email"><input name="password" type="password"><button>Login</button></form></body></html>