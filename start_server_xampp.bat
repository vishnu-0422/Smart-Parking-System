@echo off
echo ========================================
echo Smart Parking System - Starting Server
echo ========================================
echo.

REM Check for XAMPP PHP first
if exist "C:\xampp\php\php.exe" (
    echo Found XAMPP PHP!
    set PHP_PATH=C:\xampp\php\php.exe
    goto :start_server
)

REM Check for regular PHP
php --version >nul 2>&1
if %errorlevel% equ 0 (
    echo Found PHP in PATH!
    set PHP_PATH=php
    goto :start_server
)

REM PHP not found
echo.
echo ========================================
echo ERROR: PHP is not installed!
echo ========================================
echo.
echo Please choose one of these options:
echo.
echo Option 1: Install XAMPP (Recommended)
echo   - Download from: https://www.apachefriends.org/
echo   - Includes PHP, MySQL, and Apache
echo   - After installation, run this script again
echo.
echo Option 2: Install PHP separately
echo   - Download from: https://www.php.net/downloads.php
echo   - Extract to C:\php
echo   - Add C:\php to system PATH
echo   - Run this script again
echo.
echo Option 3: Use XAMPP Apache instead
echo   - Install XAMPP
echo   - Copy project to C:\xampp\htdocs\smart-parking-system
echo   - Start Apache from XAMPP Control Panel
echo   - Access: http://localhost/smart-parking-system/
echo.
pause
exit /b 1

:start_server
echo.
echo Using PHP: %PHP_PATH%
echo.
echo Starting PHP development server...
echo.
echo ========================================
echo Server URLs:
echo ========================================
echo   User Portal: http://localhost:8000/frontend/user/entry.html
echo   Admin Portal: http://localhost:8000/frontend/admin/login.html
echo   Main Entry: http://localhost:8000/index.php
echo   Test Page: http://localhost:8000/test_access.html
echo ========================================
echo.
echo Server is starting...
echo Press Ctrl+C to stop the server
echo.
echo ========================================
echo.

REM Change to script directory
cd /d "%~dp0"

REM Start PHP server
%PHP_PATH% -S localhost:8000

pause

