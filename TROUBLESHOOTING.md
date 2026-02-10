# 🔧 Troubleshooting "404 Not Found" Error

## Problem: Getting "404 Not Found" when accessing HTML pages

### Solution 1: Check Your URL Path

Make sure you're using the **correct URL** based on where your project is located:

#### If using XAMPP (project in htdocs):
```
✅ CORRECT:
http://localhost/smart-parking-system/frontend/user/entry.html
http://localhost/smart-parking-system/frontend/admin/login.html

❌ WRONG:
http://localhost/frontend/user/entry.html
http://localhost/user/entry.html
```

#### If using PHP built-in server (from project root):
```
✅ CORRECT:
http://localhost:8000/frontend/user/entry.html
http://localhost:8000/frontend/admin/login.html

❌ WRONG:
http://localhost:8000/user/entry.html
http://localhost:8000/admin/login.html
```

---

### Solution 2: Verify Project Location

**For XAMPP:**
1. Check if project is in: `C:\xampp\htdocs\smart-parking-system\`
2. If project is on Desktop, you need to either:
   - **Option A**: Copy entire folder to `C:\xampp\htdocs\`
   - **Option B**: Create a symbolic link
   - **Option C**: Change XAMPP document root

**For PHP Built-in Server:**
1. Make sure you're running the server from the project root directory
2. The command should be: `php -S localhost:8000`
3. Run it from: `C:\Users\Marellavishnuvardhan\Desktop\smart parking system`

---

### Solution 3: Check File Structure

Verify your project has this structure:
```
smart-parking-system/
├── index.php
├── frontend/
│   ├── user/
│   │   ├── entry.html
│   │   ├── slot.html
│   │   └── ...
│   ├── admin/
│   │   ├── login.html
│   │   ├── dashboard.html
│   │   └── ...
│   └── assets/
└── backend/
```

---

### Solution 4: Test Direct File Access

Try accessing files directly to verify they exist:

1. **Test index.php:**
   - http://localhost/smart-parking-system/index.php
   - Should redirect to entry page

2. **Test entry.html directly:**
   - http://localhost/smart-parking-system/frontend/user/entry.html
   - Should show the entry form

3. **Test admin login:**
   - http://localhost/smart-parking-system/frontend/admin/login.html
   - Should show login form

---

### Solution 5: Fix XAMPP Document Root (If Needed)

If your project is on Desktop and you want to keep it there:

1. Open: `C:\xampp\apache\conf\httpd.conf`
2. Find: `DocumentRoot "C:/xampp/htdocs"`
3. Change to: `DocumentRoot "C:/Users/Marellavishnuvardhan/Desktop/smart parking system"`
4. Find: `<Directory "C:/xampp/htdocs">`
5. Change to: `<Directory "C:/Users/Marellavishnuvardhan/Desktop/smart parking system">`
6. Restart Apache

**Then access:**
- http://localhost/frontend/user/entry.html
- http://localhost/frontend/admin/login.html

---

### Solution 6: Use PHP Built-in Server (Alternative)

If XAMPP is causing issues, use PHP's built-in server:

1. Open Command Prompt or PowerShell
2. Navigate to project:
   ```bash
   cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
   ```
3. Start server:
   ```bash
   php -S localhost:8000
   ```
4. Access:
   - http://localhost:8000/frontend/user/entry.html
   - http://localhost:8000/frontend/admin/login.html

---

### Solution 7: Check Apache Error Logs

If still not working, check Apache error logs:

1. Open: `C:\xampp\apache\logs\error.log`
2. Look for specific error messages
3. Common issues:
   - File permissions
   - Path issues
   - Module not enabled

---

### Solution 8: Verify Apache is Running

1. Open XAMPP Control Panel
2. Check Apache shows "Running" (green)
3. If not running, click "Start"
4. Check for port conflicts (port 80 or 443)

---

## ✅ Quick Test Checklist

- [ ] Apache/Server is running
- [ ] Project is in correct location
- [ ] Using correct URL format
- [ ] Files exist in expected locations
- [ ] No typos in URL
- [ ] Browser cache cleared (Ctrl+F5)

---

## 🎯 Recommended Fix

**Easiest solution:** Copy project to XAMPP htdocs:

1. Copy entire folder: `smart parking system`
2. Paste to: `C:\xampp\htdocs\`
3. Rename to: `smart-parking-system` (remove spaces)
4. Access: http://localhost/smart-parking-system/frontend/user/entry.html

**Why?** Spaces in folder names can cause URL issues. Use hyphens instead.

---

## 📞 Still Not Working?

1. Check browser console (F12) for errors
2. Check Apache error logs
3. Verify file permissions
4. Try accessing a simple test file first




