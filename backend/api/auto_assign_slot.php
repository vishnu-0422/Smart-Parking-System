<?php
/**
 * Auto Assign Slot API
 * Automatically recommends and assigns the first available parking slot
 * - Fetches slot with status 'available'
 * - Marks it as 'occupied'
 * - Returns slot number as JSON
 */

require_once '../../backend/config/db.php';
require_once '../../backend/models/Slot.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $pdo = getDBConnection();
    
    if (!$pdo) {
        sendErrorResponse('Database connection failed', 500);
    }
    
    // Get optional vehicle type filter
    $vehicleType = null;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $vehicleType = $_GET['vehicleType'] ?? null;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data)) {
            $data = $_POST;
        }
        $vehicleType = $data['vehicleType'] ?? null;
    }
    
    $slotModel = new Slot();
    
    // Auto-assign first available slot
    $slot = $slotModel->autoAssignSlot($vehicleType);
    
    if ($slot) {
        sendJSONResponse([
            'success' => true,
            'message' => 'Slot assigned successfully',
            'slot_number' => $slot['slot_number'],
            'slot' => $slot
        ]);
    } else {
        sendErrorResponse('No available slots found', 404);
    }
    
} catch (PDOException $e) {
    error_log("Auto Assign Slot API - Database Error: " . $e->getMessage());
    sendErrorResponse('Database error occurred', 500);
} catch (Exception $e) {
    error_log("Auto Assign Slot API - Error: " . $e->getMessage());
    sendErrorResponse('An error occurred while assigning slot', 500);
}






