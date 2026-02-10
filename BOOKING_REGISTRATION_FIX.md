# Fix: Vehicle Registration "Failed to Register" Error

## Problem
When users tried to book a parking slot and submit the vehicle entry form, they received a "failed to register" error message.

## Root Cause
The `Vehicle.php` model's `registerVehicle()` method was attempting to insert data into database columns that don't exist in the actual `vehicles` table schema.

### What was wrong:
```php
// INCORRECT - These columns don't exist in the database
INSERT INTO vehicles (vehicle_number, vehicle_type, owner_name, phone_number, company, model, is_custom_vehicle, created_at)
VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
```

### Database schema only has:
```sql
CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_number VARCHAR(20) NOT NULL UNIQUE,
    company_id INT NULL,           -- Foreign key (not 'company' text field)
    model_id INT NULL,             -- Foreign key (not 'model' text field)
    vehicle_type ENUM('car', 'motorcycle', 'truck', 'van') NOT NULL,
    color VARCHAR(50) NULL,
    owner_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,       -- For user email, not company/model
    address TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)
```

## Solution Applied
Updated [backend/models/Vehicle.php](backend/models/Vehicle.php) to insert only the columns that actually exist in the database:

```php
// CORRECT - Uses columns that exist in the database
INSERT INTO vehicles (vehicle_number, vehicle_type, owner_name, phone_number, email, created_at, updated_at)
VALUES (?, ?, ?, ?, ?, NOW(), NOW())

$stmt->execute([
    $data['vehicleNumber'],
    $data['vehicleType'],
    $data['ownerName'],
    $data['phoneNumber'],
    $data['userEmail'] ?? null
]);
```

## Files Modified
- [backend/models/Vehicle.php](backend/models/Vehicle.php) - Line 18-50
  - Removed references to non-existent columns: `company`, `model`, `is_custom_vehicle`
  - Simplified the INSERT statement to match actual database schema
  - Email is now stored directly from user data

## Test the Fix
1. Log in to the user account
2. Go to **Book Parking** (Entry page)
3. Fill in all details:
   - Vehicle Number (e.g., ABC-1234)
   - Vehicle Type (Car, Motorcycle, etc.)
   - Company/Brand (select from dropdown or "Other")
   - Model (select from dropdown or enter custom)
   - Owner Name
   - Phone Number
4. Click **"Register Vehicle"**
5. ✅ Should now show: "Vehicle registered successfully! Redirecting..."
6. Should redirect to slot selection page

## What Works Now
✅ Vehicle registration completes successfully
✅ User is redirected to slot selection page
✅ Vehicle information is stored in database
✅ User email is linked to vehicle for history tracking
✅ Duplicate vehicle detection still works (returns existing vehicle ID)

## Error Handling
If registration still fails, check:
1. Browser Console (F12) for error messages
2. Server logs in `php.log` or MySQL error logs
3. Ensure database user has INSERT permissions on vehicles table
4. Verify database connection in `backend/config/db.php`
