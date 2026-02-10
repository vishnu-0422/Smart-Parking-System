# 🚀 How to Start the Server

## Quick Start Options

### Option 1: Use the Batch File (If PHP is Installed)

1. **Double-click** `start_server.bat` in the project folder
2. A command window will open
3. If PHP is installed, server will start automatically
4. Access: http://localhost:8000/frontend/user/entry.html

---

### Option 2: Manual Start (If PHP is Installed)

1. **Open Command Prompt** or **PowerShell**
2. **Navigate to project folder:**
   ```cmd
   cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
   ```
3. **Start server:**
   ```cmd
   php -S localhost:8000
   ```
4. **Keep the window open** (server runs in this window)
5. **Access:** http://localhost:8000/frontend/user/entry.html

---

### Option 3: Use XAMPP (Recommended - Easiest)

**If PHP is NOT installed, use XAMPP:**

1. **Download XAMPP:**
   - Go to: https://www.apachefriends.org/download.html
   - Download XAMPP for Windows
   - Install it

2. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Click "Start" for **Apache**
   - Click "Start" for **MySQL**

3. **Copy Project:**
   - Copy project folder to: `C:\xampp\htdocs\smart-parking-system`
   - (Rename to remove spaces: `smart-parking-system`)

4. **Access:**
   - http://localhost/smart-parking-system/frontend/user/entry.html
   - http://localhost/smart-parking-system/frontend/admin/login.html

---

## ✅ Check if PHP is Installed

**Run this in Command Prompt:**
```cmd
php --version
```

**If you see version info:** PHP is installed ✅
**If you see "not recognized":** PHP is NOT installed ❌

---

## 🔧 If PHP is NOT Installed

### Install PHP (Option A):

1. Download PHP: https://windows.php.net/download/
2. Extract to `C:\php`
3. Add to PATH:
   - Right-click "This PC" → Properties
   - Advanced System Settings → Environment Variables
   - System Variables → Path → Edit
   - Add: `C:\php`
4. Restart Command Prompt
5. Test: `php --version`

### Use XAMPP (Option B - Easier):

1. Install XAMPP (includes PHP + MySQL + Apache)
2. Start Apache from XAMPP Control Panel
3. Copy project to `C:\xampp\htdocs\`
4. Access via browser

---

## 🎯 Recommended: Use XAMPP

**Why XAMPP?**
- ✅ Includes PHP, MySQL, Apache (all in one)
- ✅ Pre-configured
- ✅ No manual setup needed
- ✅ Includes phpMyAdmin for database

**Steps:**
1. Install XAMPP
2. Start Apache and MySQL
3. Copy project to `htdocs`
4. Import database
5. Access via browser

---

## 📝 Current Status

**To check if server can run:**
1. Open Command Prompt
2. Type: `php --version`
3. If error → Install PHP or use XAMPP
4. If version shown → You can run the server!

---

## 🚀 Start Server Now

**If PHP is installed, run:**
```cmd
cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
php -S localhost:8000
```

**Or double-click:** `start_server.bat`

**Then open browser:** http://localhost:8000/frontend/user/entry.html




