<?php
/**
 * UPI Payment API
 * Generates UPI QR data, stores parking entry, and marks payment as completed
 */

require_once '../../backend/config/db.php';
require_once '../../backend/controllers/PaymentController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendErrorResponse('Only POST method is allowed', 405);
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Also check for form data
    if (empty($data)) {
        $data = $_POST;
    }
    
    // Validate required fields
    if (!isset($data['vehicleId']) || !isset($data['slotId']) || !isset($data['amount'])) {
        sendErrorResponse('Vehicle ID, Slot ID, and Amount are required');
    }
    
    // Optional fields with defaults
    if (!isset($data['hours'])) {
        $data['hours'] = 1; // Default 1 hour
    }
    
    if (!isset($data['upiId'])) {
        // Default UPI ID - should be configured in system settings
        $data['upiId'] = 'parking@paytm';
    }
    
    if (!isset($data['payeeName'])) {
        $data['payeeName'] = 'Smart Parking System';
    }
    
    $controller = new PaymentController();
    
    // Generate UPI QR and store parking entry
    $result = $controller->generateUPIQRAndStoreEntry($data);
    
    if ($result['success']) {
        sendJSONResponse([
            'success' => true,
            'message' => $result['message'],
            'qr_data' => $result['qr_data'],
            'entry' => $result['entry'],
            'entry_id' => $result['entry_id']
        ]);
    } else {
        sendErrorResponse($result['message']);
    }
    
} catch (PDOException $e) {
    error_log("UPI Payment API - Database Error: " . $e->getMessage());
    sendErrorResponse('Database error occurred', 500);
} catch (Exception $e) {
    error_log("UPI Payment API - Error: " . $e->getMessage());
    sendErrorResponse('An error occurred while processing UPI payment', 500);
}






