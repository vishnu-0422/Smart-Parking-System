# Vehicle Company & Model Selection Feature - Implementation Guide

## Overview
This enhancement adds professional vehicle company and model selection to the Smart Parking System, improving security tracking for luxury vehicles and providing better vehicle identification.

## Features Implemented

### 1. **Vehicle Database (10 Companies × 5 Models)**
Located in: `frontend/assets/js/vehicle-data.js`

#### Companies by Type:
- **Cars (10 companies)**:
  - Maruti Suzuki, Hyundai, Tata Motors, Mahindra, Honda, Toyota, Ford, Kia, Skoda, Volkswagen
  
- **Motorcycles (5 companies)**:
  - Hero MotoCorp, Bajaj Auto, Honda Motorcycles, TVS, Royal Enfield
  
- **Trucks (5 companies)**:
  - Tata Trucks, Ashok Leyland, Mahindra Trucks, Eicher, Volvo Trucks
  
- **Vans (5 companies)**:
  - Toyota, Mahindra, Force Motors, Nissan, Suzuki
  
- **Electric Vehicles (5 companies)**:
  - Tesla, Tata Electric, Mahindra Electric, Hyundai Electric, MG Electric

### 2. **User Interface Enhancements**
The entry.html now includes:
- **Vehicle Type Selection** → Shows appropriate company list
- **Company/Brand Selection** → Shows models for selected company
- **Model Selection** → Auto-populated based on company choice
- **Custom Vehicle Option** → For vehicles not in database with security warning

### 3. **Security Levels**
- **Registered Vehicles** (in database):
  - ✅ Marked with "Registered vehicle - High security" badge
  - Premium security tracking
  - Faster processing
  
- **Custom Vehicles** (not in database):
  - ⚠️ Display security warning
  - Require additional verification
  - Manual company and model entry
  - Standard security level

## Database Updates

### SQL Migration Required
Run this SQL to update your database:

```sql
ALTER TABLE vehicles ADD COLUMN company VARCHAR(100) DEFAULT NULL;
ALTER TABLE vehicles ADD COLUMN model VARCHAR(100) DEFAULT NULL;
ALTER TABLE vehicles ADD COLUMN is_custom_vehicle BOOLEAN DEFAULT 0;

CREATE INDEX idx_vehicle_company_model ON vehicles(company, model);
CREATE INDEX idx_custom_vehicle ON vehicles(is_custom_vehicle);
```

File: `database/add_vehicle_company_model.sql`

## Files Updated

### Frontend Files:
1. **frontend/user/entry.html**
   - Added company and model selection dropdowns
   - Dynamic population based on vehicle type
   - Custom vehicle input for unlisted vehicles
   - Form validation for company and model selection

2. **frontend/assets/js/vehicle-data.js** (NEW)
   - Complete vehicle company and model database
   - Helper functions for data retrieval
   - Security level detection
   - Luxury vehicle tracking

### Backend Files:
1. **backend/models/Vehicle.php**
   - Updated registerVehicle() to store company, model, and custom vehicle flag
   - Proper error logging for vehicle registration

## User Experience Flow

### For Registered Vehicles:
1. User selects "Car" as vehicle type
2. System displays dropdown with 10 car companies
3. User selects "Maruti Suzuki"
4. System shows 5 models (Swift, Alto, Baleno, etc.)
5. User selects "Swift"
6. ✅ Success: "Registered vehicle - High security" badge shown
7. Vehicle registered with premium security

### For Custom Vehicles:
1. User selects vehicle type
2. User clicks "Other/Not in List" option
3. System shows custom input fields:
   - Company Name (text input)
   - Model Name (text input)
4. ⚠️ Security warning displayed
5. User enters details and submits
6. Vehicle registered as custom with standard security

## Data Structure

### Vehicle Data Object (Registered):
```javascript
{
    vehicleType: 'car',
    companyKey: 'maruti',
    companyName: '🇮🇳 Maruti Suzuki',
    model: 'Swift',
    isRegistered: true,
    securityLevel: 'high'
}
```

### Vehicle Data Object (Custom):
```javascript
{
    vehicleType: 'car',
    company: 'Custom Brand',
    model: 'Custom Model',
    isCustomVehicle: true,
    securityLevel: 'standard'
}
```

## Security Benefits

### Enhanced Tracking:
- Luxury vehicle identification and monitoring
- Prevents duplicate registrations of same vehicle
- Quick identification of unauthorized vehicles
- Better audit trails for high-value vehicles

### Data Validation:
- Company and model validation
- Prevention of entry errors
- Standardized vehicle identification
- Improved reporting accuracy

## Admin Portal Enhancements (Optional Future)

The admin can now:
- View registered vs custom vehicles
- Track luxury vehicle registrations
- Generate security reports by company
- Identify vehicles requiring additional inspection

## Testing Checklist

- [ ] User can select vehicle type
- [ ] Company dropdown populates correctly for each type
- [ ] Model dropdown shows correct models for selected company
- [ ] "Other/Not in List" option appears for each type
- [ ] Custom vehicle inputs appear when "Other" is selected
- [ ] Security warning displays for custom vehicles
- [ ] Form validation prevents submission without company selection
- [ ] Form validation prevents submission without model selection
- [ ] Vehicle data is stored correctly in database
- [ ] Existing bookings display company and model information
- [ ] Payment page shows company and model details
- [ ] Exit page shows company and model details

## Browser Compatibility
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Notes
- Vehicle data is stored in client-side JavaScript for fast performance
- Company keys are used internally; company names are displayed to users
- Custom vehicles are marked with a flag for easy identification
- Security level information can be used for parking rate customization in future versions

## Future Enhancements
1. Admin dashboard to add new companies and models
2. Dynamic pricing based on vehicle company (luxury vehicles)
3. SMS notifications for luxury vehicle entries
4. Automated inspection scheduling for custom vehicles
5. Integration with insurance company databases
6. Vehicle theft detection system enhancement
