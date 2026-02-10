#!/bin/bash

echo "========================================"
echo "Smart Parking System - Starting Server"
echo "========================================"
echo ""

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "ERROR: PHP is not installed or not in PATH"
    echo "Please install PHP and add it to your system PATH"
    echo "Download from: https://www.php.net/downloads.php"
    exit 1
fi

echo "PHP found!"
echo ""

# Check if database file exists
if [ ! -f "database/smart_parking.sql" ]; then
    echo "WARNING: Database file not found!"
    echo "Please ensure database/smart_parking.sql exists"
    echo ""
fi

echo "Starting PHP development server..."
echo ""
echo "Server will be available at:"
echo "  User Portal: http://localhost:8000/frontend/user/entry.html"
echo "  Admin Portal: http://localhost:8000/frontend/admin/login.html"
echo "  Main Entry: http://localhost:8000/index.php"
echo ""
echo "Press Ctrl+C to stop the server"
echo "========================================"
echo ""

php -S localhost:8000




