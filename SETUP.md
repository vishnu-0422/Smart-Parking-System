# Smart Parking System - Setup Guide

## Quick Start Guide

### Prerequisites
1. **PHP 7.4 or higher** - [Download PHP](https://www.php.net/downloads.php)
2. **MySQL 5.7 or higher** - [Download MySQL](https://dev.mysql.com/downloads/mysql/)
3. **Web Server** (Optional - PHP built-in server can be used)

### Step 1: Install PHP

#### Windows:
1. Download PHP from https://windows.php.net/download/
2. Extract to `C:\php`
3. Add `C:\php` to your system PATH
4. Copy `php.ini-development` to `php.ini`
5. Enable MySQL extension in `php.ini`:
   - Uncomment: `extension=pdo_mysql`
   - Uncomment: `extension=mysqli`

#### Verify PHP Installation:
```bash
php --version
```

### Step 2: Install MySQL

1. Download MySQL from https://dev.mysql.com/downloads/mysql/
2. Install MySQL Server
3. Remember your root password
4. Start MySQL service

#### Verify MySQL Installation:
```bash
mysql --version
```

### Step 3: Setup Database

1. Open MySQL command line or MySQL Workbench
2. Create database and import schema:

```sql
-- Option 1: Using MySQL command line
mysql -u root -p
source database/smart_parking.sql

-- Option 2: Using MySQL Workbench
-- Open database/smart_parking.sql and execute it
```

3. Update database credentials in `backend/config/db.php`:
   - `DB_HOST`: Usually 'localhost'
   - `DB_NAME`: 'smart_parking'
   - `DB_USER`: Your MySQL username (default: 'root')
   - `DB_PASS`: Your MySQL password

### Step 4: Run the Project

#### Option A: Using PHP Built-in Server (Recommended for Development)

1. Open terminal/command prompt in the project root directory
2. Run:
```bash
php -S localhost:8000
```

3. Open browser and navigate to:
   - User Portal: http://localhost:8000/frontend/user/entry.html
   - Admin Portal: http://localhost:8000/frontend/admin/login.html

#### Option B: Using XAMPP/WAMP

1. Install XAMPP from https://www.apachefriends.org/
2. Copy project folder to `C:\xampp\htdocs\smart-parking-system`
3. Start Apache and MySQL from XAMPP Control Panel
4. Import database using phpMyAdmin (http://localhost/phpmyadmin)
5. Access: http://localhost/smart-parking-system/

#### Option C: Using WAMP

1. Install WAMP from https://www.wampserver.com/
2. Copy project to `C:\wamp64\www\smart-parking-system`
3. Start WAMP services
4. Import database using phpMyAdmin
5. Access: http://localhost/smart-parking-system/

### Step 5: Access the Application

#### User Portal:
- Entry Page: http://localhost:8000/frontend/user/entry.html
- Main Entry: http://localhost:8000/index.php

#### Admin Portal:
- Login: http://localhost:8000/frontend/admin/login.html
- Default Credentials:
  - Username: `admin`
  - Password: `admin123`

### Troubleshooting

#### Database Connection Error:
- Check MySQL service is running
- Verify credentials in `backend/config/db.php`
- Ensure database `smart_parking` exists
- Check MySQL user has proper permissions

#### PHP Not Found:
- Add PHP to system PATH
- Restart terminal/command prompt
- Verify with `php --version`

#### Port Already in Use:
- Change port: `php -S localhost:8080`
- Or stop the service using port 8000

#### CORS Errors:
- Ensure you're accessing via `localhost` not `127.0.0.1`
- Check API endpoints are correct

### Testing the System

1. **Test User Flow:**
   - Go to entry page
   - Register a vehicle
   - Select a slot
   - Make payment
   - Generate QR code

2. **Test Admin Flow:**
   - Login as admin
   - View dashboard
   - Check alerts
   - Manage slots

3. **Test RFID Entry:**
   - Use RFID input on entry page
   - Test with vehicle number from database

### Default Data

The database includes:
- 10 vehicle companies
- 50 vehicle models
- 50 parking slots
- Sample vehicles
- Sample stolen vehicle alerts
- Admin user (admin/admin123)

### Next Steps

1. Customize database credentials
2. Update UPI payment details in payment API
3. Configure RFID mapping if needed
4. Set up production server (Apache/Nginx)




