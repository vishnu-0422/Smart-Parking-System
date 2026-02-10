@echo off
echo ========================================
echo Smart Parking System - Starting Server
echo ========================================
echo.

REM Check if PHP is installed
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and add it to your system PATH
    echo Download from: https://www.php.net/downloads.php
    pause
    exit /b 1
)

echo PHP found!
echo.

REM Check if database file exists
if not exist "database\smart_parking.sql" (
    echo WARNING: Database file not found!
    echo Please ensure database\smart_parking.sql exists
    echo.
)

echo Starting PHP development server...
echo.
echo Server will be available at:
echo   User Portal: http://localhost:8000/frontend/user/entry.html
echo   Admin Portal: http://localhost:8000/frontend/admin/login.html
echo   Main Entry: http://localhost:8000/index.php
echo.
echo Press Ctrl+C to stop the server
echo ========================================
echo.

php -S localhost:8000

pause

