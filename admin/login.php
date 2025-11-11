<?php
require_once __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $u = $_POST['username']; $p = $_POST['password'];
    // default admin credentials - change later in production
    if ($u==='admin' && $p==='admin123') {
        $_SESSION['admin'] = ['username'=>'admin'];
        header('Location: /NexGizmo/admin/index.php'); exit;
    }
    $err = "Invalid";
}
?><!doctype html><html><head><meta charset="utf-8"><title>Admin Login</title></head><body>
<h2>Admin Login</h2>
<?php if(!empty($err)) echo "<p style='color:red'>$err</p>"; ?>
<form method="post"><input name="username" placeholder="username"><br><input name="password" type="password" placeholder="password"><br><button>Login</button></form>
</body></html>
