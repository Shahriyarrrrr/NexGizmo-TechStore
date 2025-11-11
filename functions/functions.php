<?php
// functions/functions.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/app.php';

function current_user() {
    return $_SESSION['user'] ?? null;
}
function is_logged_in() {
    return isset($_SESSION['user']);
}

/* CSRF */
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_check($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token ?? '');
}

/* Settings retrieval */
function get_setting(PDO $pdo, $key, $default = null) {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    try {
        $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key`=?");
        $stmt->execute([$key]);
        $val = $stmt->fetchColumn();
    } catch (Exception $e) {
        $val = null;
    }
    if ($val === false || $val === null) $val = $default;
    $cache[$key] = $val;
    return $val;
}

/* Fetch categories/products */
function fetch_categories(PDO $pdo) {
    $stmt = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function fetch_products(PDO $pdo, $opts = []) {
    $params = [];
    $where = [];
    if (!empty($opts['category_slug'])) {
        $where[] = "c.slug = :cslug";
        $params[':cslug'] = $opts['category_slug'];
    }
    if (!empty($opts['q'])) {
        $where[] = "(p.name LIKE :q OR p.short_desc LIKE :q)";
        $params[':q'] = '%' . $opts['q'] . '%';
    }
    $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
            FROM products p
            LEFT JOIN categories c ON c.id = p.category_id";
    if ($where) $sql .= " WHERE " . implode(" AND ", $where);
    $sql .= " ORDER BY p.created_at DESC";
    if (!empty($opts['limit'])) $sql .= " LIMIT " . (int)$opts['limit'];
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function fetch_product_by_slug(PDO $pdo, $slug) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug
                           FROM products p LEFT JOIN categories c ON c.id = p.category_id
                           WHERE p.slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetch_product_by_id(PDO $pdo, $id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ---------- Cart ---------- */
function add_to_cart($productId, $qty = 1) {
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (!isset($_SESSION['cart'][$productId])) $_SESSION['cart'][$productId] = 0;
    $_SESSION['cart'][$productId] += max(1, (int)$qty);
}
function remove_from_cart($productId) {
    if (isset($_SESSION['cart'][$productId])) unset($_SESSION['cart'][$productId]);
}
function update_cart_qty($productId, $qty) {
    if ($qty <= 0) remove_from_cart($productId);
    else $_SESSION['cart'][$productId] = (int)$qty;
}
function cart_items(PDO $pdo) {
    $items = [];
    if (empty($_SESSION['cart'])) return $items;
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $qty = $_SESSION['cart'][$row['id']];
        $row['qty'] = $qty;
        $row['line_total'] = $qty * (float)$row['price'];
        $items[] = $row;
    }
    return $items;
}
function cart_count() {
    if (empty($_SESSION['cart'])) return 0;
    return array_sum($_SESSION['cart']);
}
function cart_subtotal(PDO $pdo) {
    $sum = 0;
    foreach (cart_items($pdo) as $it) $sum += $it['line_total'];
    return $sum;
}

/* ---------- Coupons ---------- */
function get_coupon(PDO $pdo, string $code) {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code=? AND is_active=1");
    $stmt->execute([$code]);
    $c = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$c) return null;
    $now = date('Y-m-d H:i:s');
    if ($now < $c['start_at'] || $now > $c['end_at']) return null;
    if (!is_null($c['max_uses'])) {
        $s = $pdo->prepare("SELECT COUNT(*) FROM coupon_usages WHERE coupon_id=?");
        $s->execute([$c['id']]);
        $used = (int)$s->fetchColumn();
        if ($used >= (int)$c['max_uses']) return null;
    }
    return $c;
}
function coupon_discount_amount(PDO $pdo, array $coupon, float $subtotal, string $userEmail) {
    if ($subtotal < (float)$coupon['min_cart']) return 0.0;
    $s = $pdo->prepare("SELECT COUNT(*) FROM coupon_usages WHERE coupon_id=? AND user_email=?");
    $s->execute([$coupon['id'], $userEmail]);
    $userUsed = (int)$s->fetchColumn();
    if (!is_null($coupon['per_user_limit']) && $userUsed >= (int)$coupon['per_user_limit']) return 0.0;
    $discount = 0.0;
    if ($coupon['type'] === 'flat') {
        $discount = (float)$coupon['amount'];
    } else {
        $discount = $subtotal * ((float)$coupon['amount'] / 100.0);
        if (!is_null($coupon['max_discount'])) {
            $discount = min($discount, (float)$coupon['max_discount']);
        }
    }
    $discount = min($discount, $subtotal);
    return round($discount, 2);
}
function record_coupon_usage(PDO $pdo, int $couponId, string $email) {
    $stmt = $pdo->prepare("INSERT INTO coupon_usages (coupon_id, user_email) VALUES (?, ?)");
    $stmt->execute([$couponId, $email]);
}

/* ---------- Delivery fee ---------- */
function delivery_fee(PDO $pdo, string $location) {
    $inside = (float)get_setting($pdo, 'delivery_inside_dhaka', 80);
    $outside = (float)get_setting($pdo, 'delivery_outside_dhaka', 150);
    return $location === 'outside_dhaka' ? $outside : $inside;
}

/* ---------- Order totals ---------- */
function compute_order_totals(PDO $pdo, string $location, ?string $couponCode, string $email) {
    $subtotal = cart_subtotal($pdo);
    $coupon = null;
    $discount = 0.0;
    $code = null;
    if ($couponCode) {
        $c = get_coupon($pdo, strtoupper(trim($couponCode)));
        if ($c) {
            $code = $c['code'];
            $discount = coupon_discount_amount($pdo, $c, $subtotal, $email);
            if ($discount > 0) $coupon = $c;
        }
    }
    $ship = delivery_fee($pdo, $location);
    $grand = max(0, $subtotal + $ship - $discount);
    return [
        'subtotal' => $subtotal,
        'delivery_fee' => $ship,
        'discount' => $discount,
        'coupon' => $coupon,
        'coupon_code' => $code,
        'grand_total' => $grand
    ];
}
