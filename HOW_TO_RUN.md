# 🚀 How to Run the Server - Step by Step

## ✅ Quick Start (3 Methods)

### Method 1: Double-Click Batch File (Easiest)

1. **Double-click** `start_server_xampp.bat`
2. A command window will open
3. Server will start automatically
4. **Keep the window open** (don't close it!)
5. Open browser: http://localhost:8000/frontend/user/entry.html

---

### Method 2: Use XAMPP Apache (Recommended)

**If you have XAMPP installed:**

1. **Open XAMPP Control Panel**
2. **Click "Start" for Apache** (should turn green)
3. **Click "Start" for MySQL** (should turn green)
4. **Copy project to:** `C:\xampp\htdocs\smart-parking-system`
   - (Rename folder to remove spaces)
5. **Access:**
   - http://localhost/smart-parking-system/frontend/user/entry.html
   - http://localhost/smart-parking-system/frontend/admin/login.html

**No command line needed!**

---

### Method 3: Manual Command Line

1. **Open Command Prompt** (cmd) or **PowerShell**
2. **Navigate to project:**
   ```cmd
   cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
   ```
3. **Start server:**
   
   **If using XAMPP PHP:**
   ```cmd
   C:\xampp\php\php.exe -S localhost:8000
   ```
   
   **If PHP is in PATH:**
   ```cmd
   php -S localhost:8000
   ```
4. **Keep window open** (server runs here)
5. **Open browser:** http://localhost:8000/frontend/user/entry.html

---

## 🔍 Check What You Have

### Check if XAMPP is installed:
- Look for folder: `C:\xampp`
- If exists → Use Method 2 (XAMPP Apache)

### Check if PHP is installed:
- Open Command Prompt
- Type: `php --version`
- If shows version → Use Method 1 or 3
- If error → Install PHP or XAMPP

---

## 🎯 Recommended: Use XAMPP

**Why?**
- ✅ Easiest setup
- ✅ No command line needed
- ✅ Includes database (MySQL)
- ✅ Visual control panel

**Steps:**
1. Download XAMPP: https://www.apachefriends.org/
2. Install XAMPP
3. Open XAMPP Control Panel
4. Start Apache and MySQL
5. Copy project to `C:\xampp\htdocs\`
6. Access via browser

---

## ⚠️ Common Issues

### "Port 8000 already in use"
**Fix:** Use different port:
```cmd
php -S localhost:8080
```
Then access: http://localhost:8080/frontend/user/entry.html

### "PHP not found"
**Fix:** 
- Install XAMPP, OR
- Install PHP and add to PATH

### "Server starts but pages show 404"
**Fix:**
- Make sure you're in the project root folder
- Check URL path matches folder structure
- Try: http://localhost:8000/test_access.html first

---

## ✅ Verify Server is Running

**Test these URLs:**

1. **Test Page:**
   - http://localhost:8000/test_access.html
   - Should show "Server is working" message

2. **User Entry:**
   - http://localhost:8000/frontend/user/entry.html
   - Should show vehicle entry form

3. **Admin Login:**
   - http://localhost:8000/frontend/admin/login.html
   - Should show login form

---

## 📝 Quick Reference

**To Start Server:**
- Double-click: `start_server_xampp.bat`
- OR use XAMPP Control Panel
- OR run: `php -S localhost:8000`

**To Stop Server:**
- Press `Ctrl+C` in the command window
- OR close the command window
- OR stop Apache in XAMPP

**Access URLs:**
- User: http://localhost:8000/frontend/user/entry.html
- Admin: http://localhost:8000/frontend/admin/login.html

---

## 🆘 Still Not Working?

1. **Check if port 8000 is free:**
   ```cmd
   netstat -ano | findstr :8000
   ```

2. **Try different port:**
   ```cmd
   php -S localhost:8080
   ```

3. **Check firewall** - Allow PHP/port 8000

4. **Use XAMPP** - Most reliable option




