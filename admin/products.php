<?php require_once __DIR__ . '/admin_header.php';
require_once __DIR__ . '/../functions/functions.php';

$cats = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

function save_upload($file) {
  if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) return [false, 'No file uploaded'];
  $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/svg+xml'=>'.svg'];
  $type = mime_content_type($file['tmp_name']);
  if (!isset($allowed[$type])) return [false, 'Invalid file type'];
  if ($file['size'] > 5*1024*1024) return [false, 'File too large (max 5MB)'];
  if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
  $name = bin2hex(random_bytes(8)) . $allowed[$type];
  $dest = rtrim(UPLOAD_DIR,'/') . '/' . $name;
  if (!move_uploaded_file($file['tmp_name'], $dest)) return [false, 'Failed to move file'];
  $url = rtrim(UPLOAD_URL,'/') . '/' . $name;
  return [true, $url];
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
  if (!csrf_check($_POST['csrf'] ?? '')) die('Invalid CSRF token');

  if (isset($_POST['create'])) {
    $img = trim($_POST['image_url'] ?? '');
    if (!empty($_FILES['image_file']['name'])) {
      [$ok, $res] = save_upload($_FILES['image_file']);
      if ($ok) $img = $res; else $error = $res;
    }
    if (empty($error)) {
      $s=$pdo->prepare("INSERT INTO products (category_id,name,slug,price,old_price,short_desc,description,image_url,stock,allow_backorder,created_at)
                        VALUES (?,?,?,?,?,?,?,?,?,?,NOW())");
      $s->execute([
        (int)$_POST['category_id'], trim($_POST['name']), trim($_POST['slug']),
        (float)$_POST['price'], ($_POST['old_price']===''?null:(float)$_POST['old_price']),
        trim($_POST['short_desc']), trim($_POST['description']), $img,
        (int)$_POST['stock'], isset($_POST['allow_backorder'])?1:0
      ]);
    }
  } elseif (isset($_POST['update'])) {
    $img = trim($_POST['image_url'] ?? '');
    if (!empty($_FILES['image_file']['name'])) {
      [$ok, $res] = save_upload($_FILES['image_file']);
      if ($ok) $img = $res; else $error = $res;
    }
    if (empty($error)) {
      $s=$pdo->prepare("UPDATE products SET category_id=?, name=?, slug=?, price=?, old_price=?, short_desc=?, description=?, image_url=?, stock=?, allow_backorder=? WHERE id=?");
      $s->execute([
        (int)$_POST['category_id'], trim($_POST['name']), trim($_POST['slug']),
        (float)$_POST['price'], ($_POST['old_price']===''?null:(float)$_POST['old_price']),
        trim($_POST['short_desc']), trim($_POST['description']), $img,
        (int)$_POST['stock'], isset($_POST['allow_backorder'])?1:0,
        (int)$_POST['id']
      ]);
    }
  } elseif (isset($_POST['delete'])) {
    $pdo->prepare("DELETE FROM products WHERE id=?")->execute([(int)$_POST['id']]);
  }
  header('Location: /NexGizmo/admin/products.php'); exit;
}

$prods = $pdo->query("SELECT p.*, c.name AS category FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Products</h2>
<?php if (!empty($error)): ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

<div class="card" style="margin-bottom:16px">
  <form method="post" class="form-grid" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
    <div>
      <label>Category</label>
      <select name="category_id" required>
        <?php foreach($cats as $c): ?>
          <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div><label>Name</label><input name="name" required></div>
    <div><label>Slug</label><input name="slug" required></div>
    <div><label>Price</label><input name="price" type="number" step="0.01" required></div>
    <div><label>Old Price</label><input name="old_price" type="number" step="0.01"></div>
    <div><label>Short Desc</label><input name="short_desc"></div>
    <div style="grid-column:1/-1"><label>Description</label><textarea name="description" rows="3"></textarea></div>
    <div><label>Stock</label><input name="stock" type="number" min="0" value="10" required></div>
    <div><label><input type="checkbox" name="allow_backorder"> Allow Pre-order (backorder)</label></div>
    <div><label>Image URL</label><input name="image_url" placeholder="(optional) http(s):// or data:image/..."></div>
    <div><label>Or Upload Image</label><input type="file" name="image_file" accept="image/*"></div>
    <div><button class="btn primary" name="create">Add Product</button></div>
  </form>
</div>

<table class="table">
<thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Backorder</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($prods as $p): ?>
<tr>
  <td><?php echo $p['id']; ?></td>
  <td><img src="<?php echo $p['image_url']; ?>" style="width:70px;height:50px;border-radius:8px;border:1px solid #23232a;object-fit:cover"></td>
  <td><?php echo htmlspecialchars($p['name']); ?></td>
  <td><?php echo htmlspecialchars($p['category']); ?></td>
  <td>৳ <?php echo number_format($p['price'],0); ?></td>
  <td><?php echo (int)$p['stock']; ?></td>
  <td><?php echo $p['allow_backorder'] ? 'Yes' : 'No'; ?></td>
  <td>
    <details>
      <summary class="btn">Edit</summary>
      <form method="post" class="form-grid" style="margin-top:10px" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
        <div>
          <label>Category</label>
          <select name="category_id">
            <?php foreach($cats as $c): ?>
              <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$p['category_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div><label>Name</label><input name="name" value="<?php echo htmlspecialchars($p['name']); ?>"></div>
        <div><label>Slug</label><input name="slug" value="<?php echo htmlspecialchars($p['slug']); ?>"></div>
        <div><label>Price</label><input name="price" type="number" step="0.01" value="<?php echo $p['price']; ?>"></div>
        <div><label>Old Price</label><input name="old_price" type="number" step="0.01" value="<?php echo $p['old_price']; ?>"></div>
        <div><label>Short Desc</label><input name="short_desc" value="<?php echo htmlspecialchars($p['short_desc']); ?>"></div>
        <div style="grid-column:1/-1"><label>Description</label><textarea name="description" rows="3"><?php echo htmlspecialchars($p['description']); ?></textarea></div>
        <div><label>Stock</label><input name="stock" type="number" min="0" value="<?php echo (int)$p['stock']; ?>"></div>
        <div><label><input type="checkbox" name="allow_backorder" <?php if($p['allow_backorder']) echo 'checked'; ?>> Allow Pre-order</label></div>
        <div style="grid-column:1/-1"><label>Image URL</label><input name="image_url" value="<?php echo htmlspecialchars($p['image_url']); ?>"></div>
        <div><label>Or Upload</label><input type="file" name="image_file" accept="image/*"></div>
        <div>
          <button class="btn" name="update">Save</button>
          <button class="btn danger" name="delete" onclick="return confirm('Delete product?')">Delete</button>
        </div>
      </form>
    </details>
  </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
