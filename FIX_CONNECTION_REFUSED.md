# 🔧 Fix "ERR_CONNECTION_REFUSED" Error

## The Problem
You're seeing: **"This site can't be reached - localhost refused to connect"**

This means **the server is NOT running**.

## ✅ Solution: Start the Server

### Method 1: Double-Click Batch File (Easiest)

1. **Double-click** `start_server_xampp.bat` in your project folder
2. **A new command window will open**
3. **You should see:**
   ```
   Found XAMPP PHP!
   Starting PHP development server...
   Server is starting...
   ```
4. **Keep this window open** (don't close it!)
5. **Now try:** http://localhost:8000/frontend/user/entry.html

---

### Method 2: Manual Start via Command Prompt

1. **Open Command Prompt** (Press Win+R, type `cmd`, press Enter)
2. **Navigate to project:**
   ```cmd
   cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
   ```
3. **Start server:**
   ```cmd
   C:\xampp\php\php.exe -S localhost:8000
   ```
4. **You should see:**
   ```
   PHP 8.x.x Development Server started at ...
   Listening on http://localhost:8000
   ```
5. **Keep this window open**
6. **Open browser:** http://localhost:8000/frontend/user/entry.html

---

### Method 3: Use XAMPP Apache (Alternative)

**If PHP built-in server doesn't work, use XAMPP Apache:**

1. **Open XAMPP Control Panel**
2. **Click "Start" for Apache** (should turn green)
3. **Copy project to:** `C:\xampp\htdocs\smart-parking-system`
   - Rename folder to remove spaces
4. **Access:** http://localhost/smart-parking-system/frontend/user/entry.html

---

## ✅ Verify Server is Running

### Check 1: Look for Command Window
- You should see a command window with PHP server running
- It will show: "Listening on http://localhost:8000"
- **If you don't see this window, server is NOT running**

### Check 2: Test Port
Open new Command Prompt and run:
```cmd
netstat -ano | findstr :8000
```
**If you see output** → Server is running ✅
**If no output** → Server is NOT running ❌

### Check 3: Test URL
Try: http://localhost:8000/test_access.html
- **If page loads** → Server is working ✅
- **If connection refused** → Server is NOT running ❌

---

## 🚨 Common Mistakes

### ❌ Mistake 1: Closing the Server Window
**Problem:** You closed the command window
**Fix:** Keep the window open! Server runs in that window.

### ❌ Mistake 2: Server Never Started
**Problem:** Batch file didn't start server
**Fix:** 
- Check if XAMPP PHP exists: `C:\xampp\php\php.exe`
- Try manual start (Method 2)

### ❌ Mistake 3: Wrong Port
**Problem:** Something else using port 8000
**Fix:** Use different port:
```cmd
C:\xampp\php\php.exe -S localhost:8080
```
Then access: http://localhost:8080/frontend/user/entry.html

---

## 🎯 Step-by-Step: Start Server Now

1. **Open File Explorer**
2. **Navigate to:** `C:\Users\Marellavishnuvardhan\Desktop\smart parking system`
3. **Double-click:** `start_server_xampp.bat`
4. **Wait for command window to open**
5. **Look for:** "Listening on http://localhost:8000"
6. **If you see that message** → Server is running! ✅
7. **Open browser:** http://localhost:8000/frontend/user/entry.html

---

## 🔍 Troubleshooting

### Server Window Closes Immediately
**Problem:** PHP not found or error occurred
**Fix:**
- Check if `C:\xampp\php\php.exe` exists
- Try manual start (Method 2)
- Check for error messages in the window

### Port Already in Use
**Problem:** Another program using port 8000
**Fix:**
- Use different port: `-S localhost:8080`
- Or stop the program using port 8000

### Firewall Blocking
**Problem:** Windows Firewall blocking connection
**Fix:**
- Allow PHP through firewall
- Or temporarily disable firewall for testing

---

## ✅ Quick Test

**After starting server, test this:**
1. http://localhost:8000/test_access.html
2. If this works → Server is running correctly!
3. Then try: http://localhost:8000/frontend/user/entry.html

---

## 📝 Remember

**The server MUST be running for the website to work!**

- ✅ Server running = Website accessible
- ❌ Server stopped = Connection refused error

**Keep the command window open while using the website!**




