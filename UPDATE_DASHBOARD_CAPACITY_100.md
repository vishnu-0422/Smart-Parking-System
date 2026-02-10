# Update Dashboard to Show 100 Total Capacity

## Problem
Both admin and user dashboards show "Total Slots: 50" instead of "Total Slots: 100"

## Solution
The dashboards are correctly configured to pull the total slot count dynamically from the database. They will automatically display 100 once you execute the database migration.

## Step 1: Update Your Database

### Option A: If you have the complete database (Fresh Install)
Use the file: `database/smart_parking.sql`
- It already contains 100 parking slots
- Simply execute this entire SQL file in your MySQL client

### Option B: If you have existing data (50 slots)
Use the file: `database/UPDATE_SLOTS_TO_100.sql`
- This will replace your 50 slots with 100 new slots
- Execute this SQL update directly

**How to Run the SQL:**

#### Using phpMyAdmin:
1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on your `smart_parking` database
3. Click the "SQL" tab
4. Copy and paste the SQL content
5. Click "Go" to execute

#### Using MySQL Command Line:
```bash
mysql -u root -p smart_parking < database/UPDATE_SLOTS_TO_100.sql
```

#### Using MySQL Workbench:
1. Open MySQL Workbench
2. Connect to your database
3. File → Open SQL Script
4. Select the SQL file
5. Execute (Ctrl + Shift + Enter)

## What Gets Updated
After running the SQL, your database will have:

```
Total Slots: 100
├── Car Slots: 40
│   ├── Level A (A-101 to A-110): 10 slots @ ₹5.00/hour
│   ├── Level B (B-201 to B-210): 10 slots @ ₹6.00/hour
│   └── Level C (C-301 to C-320): 20 slots @ ₹7.00/hour
├── Motorcycle Slots: 15
│   └── M-401 to M-415: 15 slots @ ₹2.00/hour
├── Truck Slots: 10
│   └── T-501 to T-510: 10 slots @ ₹10.00/hour
├── Van Slots: 15
│   └── V-601 to V-615: 15 slots @ ₹7.00/hour
└── Electric Vehicle Charging: 15
    └── EV-701 to EV-715: 15 slots @ ₹8.00/hour
```

## Step 2: Verify the Update

After running the SQL, verify in MySQL:
```sql
SELECT COUNT(*) as total_slots FROM slots;
```
This should return: **100**

## Step 3: Check Dashboard Display

1. **Admin Dashboard**: `http://localhost/frontend/admin/dashboard.html`
   - Should show "Total Slots: 100"
   - Available/Occupied counts should also update

2. **User Dashboard**: `http://localhost/frontend/user/user_dashboard.html`
   - Should show "Total Slots: 100"
   - Parking lot overview should display all 100 slots

## How It Works

**Backend (No code changes needed):**
- Admin API: `backend/api/admin.php?action=dashboard`
- Queries: `SELECT COUNT(*) as total FROM slots`
- Returns JSON with total_slots count

**Frontend (No code changes needed):**
- Admin dashboard: Displays `stats.total_slots` from API response
- User dashboard: Displays total slot count from API response
- Both automatically refresh data when you reload the page

## Troubleshooting

### Dashboard still shows 50 after SQL execution?

1. **Clear Browser Cache:**
   - Press Ctrl + F5 (hard refresh)
   - Or clear browser cache and reload

2. **Verify Database Update:**
   ```sql
   SELECT COUNT(*) FROM slots;  -- Should return 100
   SELECT COUNT(*) FROM slots WHERE status = 'available';  -- Should return 100
   ```

3. **Check API Response:**
   - Open browser DevTools (F12)
   - Go to Network tab
   - Reload dashboard
   - Check request to `admin.php?action=dashboard`
   - Response should show `"total_slots": 100`

4. **Restart Server (if applicable):**
   - If using XAMPP, restart Apache and MySQL
   - If using Docker, restart containers

## Files Involved

| File | Purpose | Status |
|------|---------|--------|
| `database/UPDATE_SLOTS_TO_100.sql` | Quick update script for existing DB | ✅ Ready |
| `database/smart_parking.sql` | Complete DB schema with 100 slots | ✅ Ready |
| `frontend/admin/dashboard.html` | Admin dashboard | ✅ No changes needed |
| `frontend/user/user_dashboard.html` | User dashboard | ✅ No changes needed |
| `backend/api/admin.php` | Stats API | ✅ No changes needed |
| `backend/api/slot.php` | Slot management API | ✅ No changes needed |

## Summary

- **What changed:** Database now has 100 parking slots (40 cars, 15 motorcycles, 10 trucks, 15 vans, 15 EV charging)
- **Frontend code:** No changes needed (already pulls dynamically)
- **Backend code:** No changes needed (already queries database)
- **Result:** After SQL execution, both dashboards automatically display 100 total capacity
