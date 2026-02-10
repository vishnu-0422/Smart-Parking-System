# Quick Start - Smart Parking System

## 🚀 Fastest Way to Run (Using XAMPP)

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Install XAMPP (includes PHP, MySQL, Apache)
3. Start XAMPP Control Panel
4. Start **Apache** and **MySQL** services

### Step 2: Setup Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a database
3. Name it: `smart_parking`
4. Click "Import" tab
5. Choose file: `database/smart_parking.sql`
6. Click "Go" to import

### Step 3: Copy Project
1. Copy entire project folder to: `C:\xampp\htdocs\smart-parking-system`
   (Or any folder inside htdocs)

### Step 4: Update Database Config
Edit: `backend/config/db.php`
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_parking');
define('DB_USER', 'root');
define('DB_PASS', '');  // XAMPP default is empty
```

### Step 5: Access Application
- **User Portal**: http://localhost/smart-parking-system/frontend/user/entry.html
- **Admin Login**: http://localhost/smart-parking-system/frontend/admin/login.html
- **Default Admin**: username: `admin`, password: `admin123`

---

## 🛠️ Alternative: Using PHP Built-in Server

### Step 1: Install PHP
1. Download PHP: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add to PATH:
   - Right-click "This PC" → Properties → Advanced System Settings
   - Environment Variables → System Variables → Path → Edit
   - Add: `C:\php`
4. Enable extensions in `C:\php\php.ini`:
   - Uncomment: `extension=pdo_mysql`
   - Uncomment: `extension=mysqli`

### Step 2: Install MySQL
1. Download MySQL: https://dev.mysql.com/downloads/mysql/
2. Install MySQL Server
3. Remember root password

### Step 3: Setup Database
```bash
# Open MySQL command line
mysql -u root -p

# Create and import database
CREATE DATABASE smart_parking;
USE smart_parking;
SOURCE C:/Users/Marellavishnuvardhan/Desktop/smart parking system/database/smart_parking.sql;
```

### Step 4: Update Config
Edit `backend/config/db.php` with your MySQL password

### Step 5: Run Server
```bash
# Navigate to project folder
cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"

# Start PHP server
php -S localhost:8000
```

### Step 6: Access
- http://localhost:8000/frontend/user/entry.html
- http://localhost:8000/frontend/admin/login.html

---

## ✅ Verify Installation

### Check PHP:
```bash
php --version
```

### Check MySQL:
```bash
mysql --version
```

### Test Database Connection:
Create `test_db.php` in project root:
```php
<?php
require_once 'backend/config/db.php';
$pdo = getDBConnection();
if ($pdo) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed!";
}
?>
```
Access: http://localhost:8000/test_db.php

---

## 🎯 Recommended: Use XAMPP (Easiest)

XAMPP includes everything you need:
- ✅ PHP (pre-configured)
- ✅ MySQL (pre-configured)
- ✅ Apache (web server)
- ✅ phpMyAdmin (database management)

Just install, start services, import database, and go!

---

## 📝 Notes

- Default admin credentials: `admin` / `admin123`
- Database includes sample data (vehicles, slots, alerts)
- All API endpoints are in `backend/api/`
- Frontend uses vanilla JavaScript (no build step needed)




