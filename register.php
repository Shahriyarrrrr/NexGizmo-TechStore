<?php
require_once __DIR__ . '/config/db.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $name=$_POST['name']; $email=$_POST['email']; $pass=password_hash($_POST['password'], PASSWORD_DEFAULT);
  $stmt = $pdo->prepare("INSERT INTO users (name,email,password,created_at) VALUES (?,?,?,NOW())");
  $stmt->execute([$name,$email,$pass]);
  header('Location: /NexGizmo/login.php');
  exit;
}
?><!doctype html><html><body><h2>Register</h2><form method="post"><input name="name"><input name="email"><input name="password" type="password"><button>Register</button></form></body></html>