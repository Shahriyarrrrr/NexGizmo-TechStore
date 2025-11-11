-- minimal tables
CREATE DATABASE IF NOT EXISTS nexgizmo DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE nexgizmo;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200),
  slug VARCHAR(200),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT DEFAULT 1,
  name VARCHAR(255),
  slug VARCHAR(255),
  price DECIMAL(10,2) DEFAULT 0,
  old_price DECIMAL(10,2) DEFAULT 0,
  short_desc TEXT,
  description TEXT,
  image_url VARCHAR(1024),
  stock INT DEFAULT 10,
  allow_backorder TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150),
  email VARCHAR(150) UNIQUE,
  password VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  name VARCHAR(150),
  email VARCHAR(150),
  phone VARCHAR(80),
  address TEXT,
  location ENUM('inside_dhaka','outside_dhaka') DEFAULT 'inside_dhaka',
  payment_method VARCHAR(50),
  status VARCHAR(50) DEFAULT 'pending',
  subtotal DECIMAL(10,2) DEFAULT 0,
  delivery_fee DECIMAL(10,2) DEFAULT 0,
  discount_amount DECIMAL(10,2) DEFAULT 0,
  coupon_code VARCHAR(40) DEFAULT NULL,
  grand_total DECIMAL(10,2) DEFAULT 0,
  currency CHAR(3) DEFAULT 'BDT',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  product_id INT,
  qty INT,
  price DECIMAL(10,2),
  line_total DECIMAL(10,2)
);

CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT,
  method VARCHAR(50),
  gateway VARCHAR(80),
  status VARCHAR(50),
  amount DECIMAL(10,2),
  transaction_id VARCHAR(200),
  raw_response TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  `key` VARCHAR(100) NOT NULL UNIQUE,
  `value` VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS coupons (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  type ENUM('flat','percent') NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  min_cart DECIMAL(10,2) NOT NULL DEFAULT 0,
  max_discount DECIMAL(10,2) DEFAULT NULL,
  start_at DATETIME NOT NULL,
  end_at DATETIME NOT NULL,
  max_uses INT DEFAULT NULL,
  per_user_limit INT DEFAULT 1,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS coupon_usages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  coupon_id INT NOT NULL,
  user_email VARCHAR(150) NOT NULL,
  used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (coupon_id) REFERENCES coupons(id) ON DELETE CASCADE
);

-- sample data
INSERT INTO categories (name,slug) VALUES ('Phones','phones'), ('Accessories','accessories');

INSERT INTO products (category_id,name,slug,price,short_desc,image_url,stock) VALUES
(1,'Example Phone','example-phone',18000,'A great phone','/NexGizmo/assets/images/placeholder.png',10),
(2,'Wireless Earbuds','earbuds',2500,'Comfortable earbuds','/NexGizmo/assets/images/placeholder.png',20);

INSERT INTO settings (`key`,`value`) VALUES ('delivery_inside_dhaka','80'),('delivery_outside_dhaka','150');
