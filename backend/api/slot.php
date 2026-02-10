<?php
/**
 * Parking Slot API
 * Handles slot management and assignment
 */

require_once '../../backend/config/db.php';
require_once '../../backend/controllers/SlotController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$controller = new SlotController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'getAvailable';
    
    if ($action === 'getAvailable') {
        $result = $controller->getAvailableSlots();
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'slots' => $result['slots']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } elseif ($action === 'autoAssign' || $action === 'recommend') {
        // Auto-assign first available slot
        require_once '../../backend/models/Slot.php';
        $slotModel = new Slot();
        
        // Optional: filter by vehicle type
        $vehicleType = $_GET['vehicleType'] ?? null;
        
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
    } else {
        sendErrorResponse('Invalid action');
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Also check for form data
    if (empty($data)) {
        $data = $_POST;
    }
    
    $action = $data['action'] ?? 'assign';
    
    if ($action === 'assign') {
        if (!isset($data['vehicleId']) || !isset($data['slotId'])) {
            sendErrorResponse('Vehicle ID and Slot ID are required');
        }
        
        $result = $controller->assignSlot($data['vehicleId'], $data['slotId']);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Slot assigned successfully',
                'bookingId' => $result['bookingId']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } elseif ($action === 'autoAssign' || $action === 'recommend') {
        // Auto-assign first available slot via POST
        require_once '../../backend/models/Slot.php';
        $slotModel = new Slot();
        
        // Optional: filter by vehicle type
        $vehicleType = $data['vehicleType'] ?? null;
        
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
    } else {
        sendErrorResponse('Invalid action');
    }
} else {
    sendErrorResponse('Invalid request method', 405);
}

