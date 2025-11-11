# ⚡ NexGizmo TechStore

A modern eCommerce platform for selling premium tech gadgets — built using **HTML5, CSS3, JavaScript, PHP, and MySQL**.  
NexGizmo delivers a futuristic shopping experience featuring a **dark neon tech-store theme**, dynamic product management,  
and a fully functional **cart, checkout, and admin dashboard** system.

---

## 🛒 Features

- 🌓 **Dark/Light Theme Switcher**
- 💼 **Admin Dashboard** with stats and chart analytics  
- 📦 **Product & Category Management** (CRUD)
- 🛍️ **Cart and Checkout** with validation  
- ✉️ **Email Notifications** using PHPMailer  
- 🧾 **Invoice Generation** using TCPDF  
- 🔐 **Login & Registration System**
- 💳 **SSLCommerz-ready Payment Integration (Sandbox)**
- 🧠 Built for scalability and real deployment with XAMPP or live server

---

## 🧰 Tech Stack

| Layer | Technology |
|-------|-------------|
| **Frontend** | HTML5, CSS3, JavaScript (Vanilla) |
| **Backend** | PHP 8 |
| **Database** | MySQL |
| **Libraries** | PHPMailer, TCPDF, Chart.js |
| **Server** | Apache (via XAMPP) |

---

## 🚀 Setup Instructions

### 🔹 Prerequisites
- [XAMPP](https://www.apachefriends.org/download.html)
- PHP 8+
- MySQL enabled
- Git (optional)

### 🔹 Installation

```bash
# Step 1: Clone the repository
git clone https://github.com/Shahriyarrrrr/NexGizmo-TechStore.git

# Step 2: Move the project into your XAMPP htdocs folder
cd C:\xampp\htdocs\

# Step 3: Import the database
# Open phpMyAdmin → Create new DB named 'nexgizmo' → Import 'nexgizmo.sql'

# Step 4: Start Apache & MySQL in XAMPP Control Panel

# Step 5: Visit the site
http://localhost/NexGizmo/

---

🖥️ Folder Structure
NexGizmo/
│
├── admin/           # Admin dashboard pages
├── assets/          # CSS, JS, and image files
├── config/          # Database & app configuration
├── emails/          # Email templates
├── functions/       # Core functions and logic
├── invoices/        # Auto-generated PDF invoices
├── lib/             # PHPMailer & TCPDF libraries
├── pages/           # About, Contact, etc.
├── payment/         # SSLCommerz integration
├── uploads/         # Product uploads
├── index.php        # Home page
├── cart.php         # Shopping cart
├── checkout.php     # Checkout form
├── product-details.php
├── login.php / register.php / logout.php
└── nexgizmo.sql     # Database file



🧠 Admin Panel

URL: http://localhost/NexGizmo/admin/

Default Login: admin@example.com / admin123

Manage products, categories, orders, users, and coupons from one clean dashboard.

🌈 UI Design Highlights

⚫ Dark Tech-Store Lifestyle Theme (neon & glossy)

🩶 Apple-style minimal layout for premium aesthetic

💡 Smooth transitions, subtle shadows, and glowing buttons

📱 Fully responsive design for all devices

