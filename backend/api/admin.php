<?php
/**
 * Admin API
 * Handles admin authentication and management functions
 */

require_once '../../backend/config/db.php';
require_once '../../backend/models/Alert.php';
require_once '../../backend/models/Slot.php';
require_once '../../backend/models/Vehicle.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Simple authentication check (in production, use proper JWT or session management)
function checkAuth() {
    $headers = getallheaders();
    $token = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $token);
    
    // Simple token check (replace with proper authentication)
    if (empty($token) || $token !== 'admin_token_123') {
        sendErrorResponse('Unauthorized', 401);
    }
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    // Admin login
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Simple authentication (replace with proper authentication)
    if (isset($data['username']) && isset($data['password'])) {
        if ($data['username'] === 'admin' && $data['password'] === 'admin123') {
            sendJSONResponse([
                'success' => true,
                'message' => 'Login successful',
                'token' => 'admin_token_123'
            ]);
        } else {
            sendErrorResponse('Invalid credentials');
        }
    } else {
        sendErrorResponse('Username and password are required');
    }
    exit();
}

// Check authentication for other endpoints
checkAuth();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action === 'dashboard') {
        $pdo = getDBConnection();
        
        // Get statistics
        $stats = [];
        
        // Total slots
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM slots");
        $stats['total_slots'] = $stmt->fetch()['total'];
        
        // Available slots
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM slots WHERE status = 'available'");
        $stats['available_slots'] = $stmt->fetch()['total'];
        
        // Occupied slots
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM slots WHERE status = 'occupied'");
        $stats['occupied_slots'] = $stmt->fetch()['total'];
        $stats['occupancy_rate'] = $stats['total_slots'] > 0 ? 
            round(($stats['occupied_slots'] / $stats['total_slots']) * 100, 2) : 0;
        
        // Today's revenue (only completed payments, based on entry date)
        $stmt = $pdo->query("
            SELECT SUM(amount_paid) as total 
            FROM bookings 
            WHERE DATE(entry_time) = CURDATE() 
              AND payment_status = 'completed'
        ");
        $stats['today_revenue'] = (float)($stmt->fetch()['total'] ?? 0);
        
        // Today's transactions (completed payments)
        $stmt = $pdo->query("
            SELECT COUNT(*) as total 
            FROM bookings 
            WHERE DATE(entry_time) = CURDATE() 
              AND payment_status = 'completed'
        ");
        $stats['today_transactions'] = (int)($stmt->fetch()['total'] ?? 0);

        // Total revenue (all time, completed payments)
        $stmt = $pdo->query("
            SELECT SUM(amount_paid) as total 
            FROM bookings 
            WHERE payment_status = 'completed'
        ");
        $stats['total_revenue'] = (float)($stmt->fetch()['total'] ?? 0);
        
        // Active alerts
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM stolen_vehicles WHERE status = 'active'");
        $stats['active_alerts'] = $stmt->fetch()['total'];
        
        // Stolen vehicles
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM stolen_vehicles");
        $stats['stolen_vehicles'] = $stmt->fetch()['total'];

        // Vehicles currently parked in open vs closed areas
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings b JOIN slots s ON b.slot_id = s.id WHERE b.exit_time IS NULL AND s.area = 'open'");
        $stats['vehicles_in_open'] = (int)($stmt->fetch()['total'] ?? 0);

        $stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings b JOIN slots s ON b.slot_id = s.id WHERE b.exit_time IS NULL AND s.area = 'closed'");
        $stats['vehicles_in_closed'] = (int)($stmt->fetch()['total'] ?? 0);
        
        // Recent activities
        $stmt = $pdo->query("
            SELECT b.created_at as timestamp, v.vehicle_number, s.slot_number, 
                   'Entry' as action, b.amount_paid as amount
            FROM bookings b
            JOIN vehicles v ON b.vehicle_id = v.id
            LEFT JOIN slots s ON b.slot_id = s.id
            ORDER BY b.created_at DESC
            LIMIT 10
        ");
        $activities = $stmt->fetchAll();
        
        sendJSONResponse([
            'success' => true,
            'stats' => $stats,
            'activities' => $activities
        ]);
        
    } elseif ($action === 'getAlerts') {
        $alertModel = new Alert();
        $alerts = $alertModel->getAllAlerts();
        
        // Get active parking alerts (stolen vehicles currently parked)
        $pdo = getDBConnection();
        $stmt = $pdo->query("
            SELECT v.vehicle_number, CONCAT('Stolen vehicle detected: ', v.vehicle_number) as message, 
                   b.created_at as timestamp
            FROM bookings b
            JOIN vehicles v ON b.vehicle_id = v.id
            JOIN stolen_vehicles sv ON v.vehicle_number = sv.vehicle_number
            WHERE sv.status = 'active' AND b.exit_time IS NULL
        ");
        $activeParkingAlerts = $stmt->fetchAll();
        
        sendJSONResponse([
            'success' => true,
            'alerts' => $alerts,
            'active_parking_alerts' => $activeParkingAlerts
        ]);
        
    } elseif ($action === 'getSlots') {
        $slotModel = new Slot();
        $slots = $slotModel->getAllSlots();
        
        sendJSONResponse([
            'success' => true,
            'slots' => $slots
        ]);
        
    } elseif ($action === 'getSlot') {
        if (!isset($_GET['slotId'])) {
            sendErrorResponse('Slot ID is required');
        }
        
        $slotModel = new Slot();
        $slot = $slotModel->getSlotById($_GET['slotId']);
        
        if ($slot) {
            sendJSONResponse([
                'success' => true,
                'slot' => $slot
            ]);
        } else {
            sendErrorResponse('Slot not found');
        }
    } elseif ($action === 'getHistory') {
        // Get full parking history for admin view
        $pdo = getDBConnection();
        if (!$pdo) {
            sendErrorResponse('Database connection failed', 500);
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200;
        if ($limit <= 0 || $limit > 1000) {
            $limit = 200;
        }

        $stmt = $pdo->prepare("
            SELECT 
                b.id AS booking_id,
                v.vehicle_number,
                v.vehicle_type,
                v.owner_name,
                v.phone_number,
                s.slot_number,
                b.entry_time,
                b.expiry_time,
                b.exit_time,
                b.amount_paid,
                b.payment_method,
                b.payment_status
            FROM bookings b
            JOIN vehicles v ON b.vehicle_id = v.id
            LEFT JOIN slots s ON b.slot_id = s.id
            ORDER BY b.entry_time DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $history = $stmt->fetchAll();

        sendJSONResponse([
            'success' => true,
            'history' => $history
        ]);
    } elseif ($action === 'getVehicles') {
        // Get all vehicles from database
        $pdo = getDBConnection();
        if (!$pdo) {
            sendErrorResponse('Database connection failed', 500);
        }

        $stmt = $pdo->query("
            SELECT 
                v.id,
                v.vehicle_number,
                v.vehicle_type,
                v.owner_name,
                v.phone_number,
                v.email,
                v.created_at,
                COUNT(b.id) as total_bookings,
                MAX(b.entry_time) as last_entry
            FROM vehicles v
            LEFT JOIN bookings b ON v.id = b.vehicle_id
            GROUP BY v.id
            ORDER BY v.created_at DESC
        ");
        $vehicles = $stmt->fetchAll();

        sendJSONResponse([
            'success' => true,
            'vehicles' => $vehicles
        ]);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'addAlert') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['vehicleNumber']) || !isset($data['vehicleType']) || !isset($data['ownerName'])) {
            sendErrorResponse('Missing required fields');
        }
        
        $alertModel = new Alert();
        $result = $alertModel->addStolenVehicle($data);
        
        if ($result) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Alert added successfully'
            ]);
        } else {
            sendErrorResponse('Failed to add alert');
        }
        
    } elseif ($action === 'removeAlert') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['alertId'])) {
            sendErrorResponse('Alert ID is required');
        }
        
        $alertModel = new Alert();
        $result = $alertModel->removeAlert($data['alertId']);
        
        if ($result) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Alert removed successfully'
            ]);
        } else {
            sendErrorResponse('Failed to remove alert');
        }
        
    } elseif ($action === 'addSlot') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['slotNumber']) || !isset($data['slotType']) || !isset($data['pricePerHour'])) {
            sendErrorResponse('Missing required fields');
        }
        
        $slotModel = new Slot();
        $result = $slotModel->addSlot($data);
        
        if ($result) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Slot added successfully'
            ]);
        } else {
            sendErrorResponse('Failed to add slot');
        }
        
    } elseif ($action === 'updateSlot') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['slotId'])) {
            sendErrorResponse('Slot ID is required');
        }
        
        $slotModel = new Slot();
        $result = $slotModel->updateSlot($data['slotId'], $data);
        
        if ($result) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Slot updated successfully'
            ]);
        } else {
            sendErrorResponse('Failed to update slot');
        }
        
    } elseif ($action === 'deleteSlot') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['slotId'])) {
            sendErrorResponse('Slot ID is required');
        }
        
        $slotModel = new Slot();
        $result = $slotModel->deleteSlot($data['slotId']);
        
        if ($result) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Slot deleted successfully'
            ]);
        } else {
            sendErrorResponse('Failed to delete slot');
        }
    }
} else {
    sendErrorResponse('Invalid request method', 405);
}




