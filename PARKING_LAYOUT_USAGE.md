# Parking Layout - Usage Examples & Implementation

## Admin Dashboard - Parking Layout Display

### Location
Dashboard → Slots page (Top section before the table)

### What You'll See

```
┌─────────────────────────────────────────────────┐
│           Parking Slot Management               │
├─────────────────────────────────────────────────┤
│                                                 │
│  Legend:                                        │
│  🟢 Available | 🔴 Occupied | 🟡 Maintenance   │
│                                                 │
│  📊 Statistics:                                 │
│  Total: 30 | Occupied: 8 | Available: 22      │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│           🟣 ENTRY → 🟣                         │
│                                                 │
│  Front Row (Quick Access)                      │
│  [🟢] [🔴] [🟢] [🟢] [🟡] [🟢] [🟢] [🔴]       │
│  [🟢] [🟢]                                      │
│  Front: 2/10                                    │
│                                                 │
│  Middle Row (Standard)                         │
│  [🟢] [🟢] [🔴] [🟢] [🟢] [🟡] [🟢] [🔴]       │
│  [🟢] [🟢]                                      │
│  Middle: 2/10                                   │
│                                                 │
│  Last Row (Extended)                           │
│  [🟢] [🟢] [🟢] [🟢] [🔴] [🟢] [🟢] [🟢]       │
│  [🟢] [🟢]                                      │
│  Last: 4/10                                     │
│                                                 │
│           🟣 ← EXIT 🟣                          │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│  Below: Traditional table view for management  │
│  [Add New Slot] [Filters]                      │
│                                                 │
│  Slot | Type | Status | Price | Vehicle | ... │
│  A-101| Car  | AvailableAvailable ₹50 | -   │
│                                                 │
└─────────────────────────────────────────────────┘
```

## User Dashboard - Parking Lot Overview

### Location
User Dashboard (After stats cards, before parking history)

### What You'll See

```
┌─────────────────────────────────────────────────┐
│      🅿️ Parking Lot Overview                    │
├─────────────────────────────────────────────────┤
│                                                 │
│ Real-time view of parking availability -       │
│ Green slots are available, Red slots are       │
│ occupied.                                       │
│                                                 │
│ Legend:                                        │
│ 🟢 Available | 🔴 Occupied | 🟡 Maintenance   │
│                                                 │
│ 📊 Available: 22 slots                         │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│           🟣 ENTRY → 🟣                         │
│                                                 │
│  Front Row (Quick Access)                      │
│  [🟢] [🔴] [🟢] [🟢] [🟡] [🟢] [🟢] [🔴]       │
│  [🟢] [🟢]                                      │
│  Front: 2/10                                    │
│                                                 │
│  Middle Row (Standard)                         │
│  [🟢] [🟢] [🔴] [🟢] [🟢] [🟡] [🟢] [🔴]       │
│  [🟢] [🟢]                                      │
│  Middle: 2/10                                   │
│                                                 │
│  Last Row (Extended)                           │
│  [🟢] [🟢] [🟢] [🟢] [🔴] [🟢] [🟢] [🟢]       │
│  [🟢] [🟢]                                      │
│  Last: 4/10                                     │
│                                                 │
│           🟣 ← EXIT 🟣                          │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│ Below: Your Parking History Table              │
│                                                 │
│ Date | Vehicle | Type | Slot | Entry | Exit.. │
│                                                 │
└─────────────────────────────────────────────────┘
```

## Detailed Slot Information Examples

### Example 1: Available Slot (Front Row)
```
Slot Display: [🟢 A-101]

Hover Information:
├─ Slot Number: A-101
├─ Type: Car
├─ Status: Available
├─ Price: ₹50/hour
└─ Vehicle: Empty

Actions Available: Book Now (from entry page)
```

### Example 2: Occupied Slot (Middle Row)
```
Slot Display: [🔴 B-106]

Hover Information:
├─ Slot Number: B-106
├─ Type: Car
├─ Status: Occupied
├─ Price: ₹50/hour
└─ Vehicle: KA-01-AB-1234

Actions Available: View (admin), None (user)
```

### Example 3: Maintenance Slot (Last Row)
```
Slot Display: [🟡 C-215]

Hover Information:
├─ Slot Number: C-215
├─ Type: Truck
├─ Status: Maintenance
├─ Price: ₹100/hour
└─ Vehicle: N/A

Actions Available: Edit/Resume (admin only)
```

## Responsive Design Examples

### Desktop View (Full Screen)
```
┌──────────────────────────────────────────────────┐
│ [🟢] [🔴] [🟢] [🟢] [🟡] [🟢] [🟢] [🔴] [🟢] [🟢] │
│  A-1  A-2  A-3  A-4  A-5  A-6  A-7  A-8  A-9 A-10│
└──────────────────────────────────────────────────┘

Large slots (70px × 70px)
Full text visible
No wrapping needed
Excellent readability
```

### Tablet View (Medium Screen)
```
┌─────────────────────────────┐
│ [🟢] [🔴] [🟢] [🟢]         │
│  A-1  A-2  A-3  A-4         │
│                             │
│ [🟡] [🟢] [🟢] [🔴]         │
│  A-5  A-6  A-7  A-8         │
│                             │
│ [🟢] [🟢]                   │
│  A-9 A-10                   │
└─────────────────────────────┘

Medium slots (70px × 70px)
Text may wrap
Good balance
Readable labels
```

### Mobile View (Small Screen)
```
┌──────────────────┐
│ [🟢] [🔴] [🟢]   │
│ A-1  A-2  A-3    │
│ CAR  CAR  CAR    │
│                  │
│ [🟢] [🟡] [🟢]   │
│ A-4  A-5  A-6    │
│ CAR  TRU  CAR    │
│                  │
│ [🔴] [🟢] [🟢]   │
│ A-7  A-8  A-9    │
│ CAR  CAR  CAR    │
└──────────────────┘

Compact slots (60px × 60px)
Wrapped text (TRU for Truck)
Touch-friendly spacing
Optimized for scrolling
```

## Real-World Usage Scenarios

### Scenario 1: Admin Monitoring Peak Hours
```
Administrator Action:
1. Log in to Admin Dashboard
2. Navigate to Slots page
3. View Parking Layout at top
4. See "Occupied: 28, Available: 2"
5. Identify Front Row is full
6. Plan staff for parking management
7. Monitor availability in real-time
```

### Scenario 2: User Finding Available Parking
```
User Action:
1. Login to User Dashboard
2. See "Available: 15 slots"
3. View Parking Layout
4. Notice Front Row: 2/10 occupied
5. Notice Last Row: 1/10 occupied
6. Decide to park in Last Row
7. Click "Book Parking"
8. Select Last Row slot
9. Complete booking
```

### Scenario 3: Admin Managing Maintenance
```
Administrator Action:
1. View Parking Layout
2. See 🟡 Yellow maintenance slots
3. Count: C-210, C-215, B-508
4. Monitor maintenance progress
5. Update slot status when ready
6. Layout automatically reflects change
```

### Scenario 4: Finding Specific Slot Types
```
User Looking for Motorcycle Slot:
1. View Parking Layout
2. Look for "MOT" label on slots
3. Available motorcycle slots: A-102, A-105
4. Choose preferred slot
5. Navigate for booking

Admin Adding New Slots:
1. View current layout
2. See "Front: 10/10 is full"
3. Add slots to Last Row
4. Layout updates automatically
5. New zones appear if needed
```

## API Data Flow Example

### Request (Admin)
```
GET /backend/api/admin.php?action=getSlots
Authorization: Bearer [admin-token]
```

### Response (JSON)
```json
{
  "success": true,
  "slots": [
    {
      "id": 1,
      "slot_number": "A-101",
      "slot_type": "car",
      "status": "available",
      "price_per_hour": 50,
      "vehicle_number": null,
      "entry_time": null
    },
    {
      "id": 2,
      "slot_number": "A-102",
      "slot_type": "car",
      "status": "occupied",
      "price_per_hour": 50,
      "vehicle_number": "KA-01-AB-1234",
      "entry_time": "2026-01-28 14:30:00"
    },
    {
      "id": 15,
      "slot_number": "B-515",
      "slot_type": "truck",
      "status": "maintenance",
      "price_per_hour": 100,
      "vehicle_number": null,
      "entry_time": null
    }
  ]
}
```

### Processing
```
1. Loop through slots array
2. Check slot_number to determine zone
3. Apply status as CSS class (available/occupied/maintenance)
4. Create div with appropriate color
5. Add slot_number and slot_type (abbreviated) as content
6. Store full data for tooltip
7. Count occupied per zone
8. Update statistics displays
```

### Display Result
```
Front Row (automatically filled from slots 1-10):
[🟢 A-1] [🟢 A-2] ... [🟢 A-10]

Zone Stats:
Front: 1/10 occupied

Total Stats:
Occupied: 3, Available: 27
```

## Customization Examples

### Adding to a New Page
```html
<!-- Add this section to any dashboard -->
<div class="parking-layout">
    <!-- Entry -->
    <div class="entry-section">
        <div class="section-label">ENTRY →</div>
    </div>
    
    <!-- Zone -->
    <div class="parking-zone">
        <div class="zone-header">Zone Name</div>
        <div id="slotsContainer" class="slots-row"></div>
        <div class="zone-footer">Stats Here</div>
    </div>
    
    <!-- Exit -->
    <div class="exit-section">
        <div class="section-label">← EXIT</div>
    </div>
</div>

<!-- Add JavaScript -->
<script>
    loadParkingLayout();
</script>
```

### Styling Modifications
```css
/* Change slot size */
.layout-slot {
    width: 80px;      /* was 70px */
    height: 80px;     /* was 70px */
}

/* Change colors */
.layout-slot.available {
    background-color: #e0f2fe;  /* Light blue instead of green */
    border-color: #0284c7;
    color: #075985;
}

/* Change entry/exit colors */
.entry-section, .exit-section {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    /* cyan instead of purple */
}
```

---

**Documentation Version**: 1.0
**Last Updated**: January 28, 2026
**Tested**: ✅ Admin & User Dashboards
