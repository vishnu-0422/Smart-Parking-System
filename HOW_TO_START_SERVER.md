# 🚀 Where and How to Start the Server

## ✅ Easiest Method: Double-Click the Batch File

### Step-by-Step:

1. **Open File Explorer** (Windows Explorer)
   - Press `Windows Key + E`
   - Or click the folder icon on taskbar

2. **Navigate to your project folder:**
   - Go to: `Desktop`
   - Open folder: `smart parking system`

3. **Find the file:**
   - Look for: `start_server_xampp.bat`
   - It should have a gear icon or look like a script file

4. **Double-click it:**
   - Just double-click `start_server_xampp.bat`
   - A black command window will open

5. **What you should see:**
   ```
   ========================================
   Smart Parking System - Starting Server
   ========================================
   
   Found XAMPP PHP!
   Starting PHP development server...
   
   PHP 8.x.x Development Server started at ...
   Listening on http://localhost:8000
   ```

6. **Keep the window open!**
   - Don't close this black window
   - Server runs in this window

7. **Open your browser:**
   - Go to: http://localhost:8000/frontend/user/entry.html

---

## 🔧 Alternative: Use Command Prompt

### Step-by-Step:

1. **Open Command Prompt:**
   - Press `Windows Key + R`
   - Type: `cmd`
   - Press Enter
   - OR search "Command Prompt" in Start menu

2. **Navigate to project folder:**
   - Type this command (copy and paste):
   ```cmd
   cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
   ```
   - Press Enter

3. **Start the server:**
   - Type this command:
   ```cmd
   C:\xampp\php\php.exe -S localhost:8000
   ```
   - Press Enter

4. **You should see:**
   ```
   PHP 8.x.x Development Server started at ...
   Listening on http://localhost:8000
   ```

5. **Keep this window open!**

6. **Open browser:**
   - http://localhost:8000/frontend/user/entry.html

---

## 📸 Visual Guide

### Method 1: Double-Click (Easiest)

```
File Explorer
└── Desktop
    └── smart parking system
        ├── start_server_xampp.bat  ← Double-click this!
        ├── index.php
        ├── frontend/
        └── backend/
```

**Just double-click the .bat file!**

---

### Method 2: Command Prompt

```
1. Open Command Prompt (cmd)
2. Type: cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
3. Press Enter
4. Type: C:\xampp\php\php.exe -S localhost:8000
5. Press Enter
6. Keep window open
```

---

## 🎯 Quick Start (3 Steps)

### Option A: Batch File (Recommended)
1. **Open File Explorer** → Go to project folder
2. **Double-click** `start_server_xampp.bat`
3. **Keep window open** → Open browser

### Option B: Command Prompt
1. **Open Command Prompt** (Win+R, type `cmd`)
2. **Run these commands:**
   ```cmd
   cd "C:\Users\Marellavishnuvardhan\Desktop\smart parking system"
   C:\xampp\php\php.exe -S localhost:8000
   ```
3. **Keep window open** → Open browser

---

## ⚠️ Important Notes

### ✅ DO:
- Keep the command window open
- Wait for "Listening on http://localhost:8000" message
- Use the URLs shown in the window

### ❌ DON'T:
- Close the command window (server will stop)
- Close the batch file window
- Run multiple servers on same port

---

## 🔍 How to Know Server is Running

**Look for this in the command window:**
```
Listening on http://localhost:8000
```

**If you see this** → Server is running! ✅

**If you don't see this** → Server didn't start ❌

---

## 🆘 Still Confused?

### Simplest Way:
1. Open File Explorer
2. Go to: `C:\Users\Marellavishnuvardhan\Desktop\smart parking system`
3. Find file: `start_server_xampp.bat`
4. **Right-click** → **Run as administrator** (or just double-click)
5. Wait for window to open
6. Don't close it!
7. Open browser and go to: http://localhost:8000/frontend/user/entry.html

---

## 📝 Summary

**Where to run:**
- ✅ File Explorer → Double-click batch file (EASIEST)
- ✅ Command Prompt → Type commands manually

**What to do:**
1. Start server (batch file or command)
2. Keep window open
3. Open browser
4. Access URLs

**That's it!**




