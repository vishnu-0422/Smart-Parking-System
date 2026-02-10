# 🚀 How to Run Smart Parking System

## ⚠️ Current Status
PHP and MySQL are not currently installed on your system.

## 🎯 Easiest Solution: Install XAMPP

### Why XAMPP?
- ✅ Includes PHP, MySQL, and Apache (all in one)
- ✅ Pre-configured and ready to use
- ✅ Includes phpMyAdmin for database management
- ✅ No complex setup required

### Installation Steps:

1. **Download XAMPP**
   - Go to: https://www.apachefriends.org/download.html
   - Download XAMPP for Windows
   - Run installer

2. **Install XAMPP**
   - Choose installation directory (default: `C:\xampp`)
   - Select components: Apache, MySQL, PHP, phpMyAdmin
   - Complete installation

3. **Start Services**
   - Open XAMPP Control Panel
   - Click "Start" for **Apache**
   - Click "Start" for **MySQL**
   - Both should show green "Running" status

4. **Setup Database**
   - Open browser: http://localhost/phpmyadmin
   - Click "New" on left sidebar
   - Database name: `smart_parking`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"
   - Click "Import" tab
   - Choose file: Navigate to your project → `database/smart_parking.sql`
   - Click "Go" at bottom
   - Wait for "Import has been successfully finished" message

5. **Copy Project to XAMPP**
   - Copy entire project folder to: `C:\xampp\htdocs\smart-parking-system`
   - Or keep it on Desktop and create a symbolic link

6. **Update Database Config**
   - Open: `backend/config/db.php`
   - Verify these settings (XAMPP defaults):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'smart_parking');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Empty for XAMPP default
   ```

7. **Access Application**
   - **User Portal**: http://localhost/smart-parking-system/frontend/user/entry.html
   - **Admin Login**: http://localhost/smart-parking-system/frontend/admin/login.html
   - **Admin Credentials**: 
     - Username: `admin`
     - Password: `admin123`

---

## 🔄 Alternative: Manual PHP + MySQL Setup

If you prefer to install PHP and MySQL separately:

### Install PHP:
1. Download: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add to PATH (System Environment Variables)
4. Enable `pdo_mysql` extension in `php.ini`

### Install MySQL:
1. Download: https://dev.mysql.com/downloads/mysql/
2. Install MySQL Server
3. Remember root password

### Setup Database:
```sql
mysql -u root -p
CREATE DATABASE smart_parking;
USE smart_parking;
SOURCE [path-to-project]/database/smart_parking.sql;
```

### Run Server:
```bash
cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
php -S localhost:8000
```

---

## ✅ Quick Verification

After setup, test these URLs:

1. **Main Entry**: http://localhost/smart-parking-system/index.php
2. **User Entry**: http://localhost/smart-parking-system/frontend/user/entry.html
3. **Admin Login**: http://localhost/smart-parking-system/frontend/admin/login.html

---

## 🐛 Troubleshooting

### "Database connection failed"
- ✅ Check MySQL is running (XAMPP Control Panel)
- ✅ Verify database `smart_parking` exists
- ✅ Check credentials in `backend/config/db.php`
- ✅ Test connection in phpMyAdmin

### "404 Not Found"
- ✅ Check Apache is running
- ✅ Verify project is in `htdocs` folder
- ✅ Check URL path is correct

### "PHP not found"
- ✅ Add PHP to system PATH
- ✅ Restart command prompt/terminal
- ✅ Verify with `php --version`

---

## 📋 Project Structure

```
smart-parking-system/
├── index.php              # Main entry point
├── frontend/              # User interface
│   ├── user/             # User pages
│   └── admin/            # Admin pages
├── backend/              # PHP backend
│   ├── api/              # API endpoints
│   ├── controllers/      # Business logic
│   └── models/           # Data models
└── database/             # SQL schema
    └── smart_parking.sql
```

---

## 🎉 Once Running

### Test User Flow:
1. Go to entry page
2. Enter vehicle details or scan RFID
3. Select parking slot
4. Make payment
5. Get QR code ticket

### Test Admin Flow:
1. Login as admin
2. View dashboard statistics
3. Check stolen vehicle alerts
4. Manage parking slots

---

**Need Help?** Check `SETUP.md` for detailed instructions.




