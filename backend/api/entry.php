<?php
/**
 * Vehicle Entry API
 * Handles vehicle registration and entry
 */

require_once '../../backend/config/db.php';
require_once '../../backend/controllers/EntryController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$controller = new EntryController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Register new vehicle entry
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['vehicleNumber']) || !isset($data['vehicleType']) || 
        !isset($data['ownerName']) || !isset($data['phoneNumber'])) {
        sendErrorResponse('Missing required fields');
    }
    
    $result = $controller->registerVehicle($data);
    
    if ($result['success']) {
        sendJSONResponse([
            'success' => true,
            'message' => 'Vehicle registered successfully',
            'vehicleId' => $result['vehicleId']
        ]);
    } else {
        sendErrorResponse($result['message']);
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get vehicle information
    if (isset($_GET['vehicleId'])) {
        $vehicleId = $_GET['vehicleId'];
        $result = $controller->getVehicle($vehicleId);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'vehicle' => $result['vehicle']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } else {
        sendErrorResponse('Vehicle ID is required');
    }
} else {
    sendErrorResponse('Invalid request method', 405);
}






