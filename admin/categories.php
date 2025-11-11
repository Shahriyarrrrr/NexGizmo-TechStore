<?php require_once __DIR__ . '/admin_header.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $name=$_POST['name']; $slug=$_POST['slug'];
    $pdo->prepare("INSERT INTO categories (name,slug,created_at) VALUES (?,?,NOW())")->execute([$name,$slug]);
    header('Location: /NexGizmo/admin/categories.php'); exit;
}
$cats = $pdo->query("SELECT * FROM categories")->fetchAll();
?>
<h2>Categories</h2>
<form method="post"><input name="name" placeholder="Name"><input name="slug" placeholder="Slug"><button>Add</button></form>
<ul><?php foreach($cats as $c) echo "<li>".htmlspecialchars($c['name'])."</li>"; ?></ul>
<?php require_once __DIR__ . '/admin_footer.php'; ?>