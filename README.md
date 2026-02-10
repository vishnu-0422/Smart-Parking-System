# Smart Parking System

A comprehensive web-based parking management system built with PHP backend and HTML/CSS/JavaScript frontend.

## Features

### User Features
- Vehicle entry and registration
- Slot selection and booking
- Payment processing
- QR code generation for parking tickets
- Exit management
- Parking time extension

### Admin Features
- Dashboard with system overview
- Slot management
- Alert system for stolen vehicles
- User management

## Project Structure

```
smart-parking-system/
├── index.php                 # Main entry point
├── README.md                 # Project documentation
├── frontend/
│   ├── user/                # User-facing pages
│   │   ├── entry.html
│   │   ├── slot.html
│   │   ├── payment.html
│   │   ├── qr.html
│   │   ├── exit.html
│   │   └── extend.html
│   ├── admin/               # Admin pages
│   │   ├── login.html
│   │   ├── dashboard.html
│   │   ├── alerts.html
│   │   └── slots.html
│   └── assets/
│       ├── css/
│       │   └── style.css
│       └── js/
│           └── main.js
├── backend/
│   ├── config/
│   │   └── db.php           # Database configuration
│   ├── api/                 # API endpoints
│   │   ├── entry.php
│   │   ├── slot.php
│   │   ├── payment.php
│   │   └── exit.php
│   ├── controllers/         # Business logic
│   │   ├── EntryController.php
│   │   ├── SlotController.php
│   │   └── PaymentController.php
│   └── models/              # Data models
│       ├── Vehicle.php
│       ├── StolenVehicle.php
│       ├── Slot.php
│       └── Alert.php
└── database/
    └── smart_parking.sql    # Database schema
```

## Setup Instructions

1. **Database Setup**
   - Import `database/smart_parking.sql` into your MySQL database
   - Update database credentials in `backend/config/db.php`

2. **Server Configuration**
   - Ensure PHP 7.4+ is installed
   - Configure web server (Apache/Nginx) to point to project root
   - Enable mod_rewrite if using Apache

3. **Dependencies**
   - PHP PDO extension for MySQL
   - Web server (Apache/Nginx)

## Usage

1. Access the system through `index.php`
2. Users can enter vehicles and select parking slots
3. Admins can manage the system through the admin dashboard

## License

MIT License






"# Smart-Parking-System" 
"# Smart-Parking-System" 
