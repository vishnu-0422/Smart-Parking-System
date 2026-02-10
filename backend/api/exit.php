<?php
/**
 * Vehicle Exit API
 * Handles vehicle exit and payment calculation
 */

require_once '../../backend/config/db.php';
require_once '../../backend/controllers/EntryController.php';
require_once '../../backend/controllers/PaymentController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$entryController = new EntryController();
$paymentController = new PaymentController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getBooking') {
        if (!isset($_GET['vehicleNumber'])) {
            sendErrorResponse('Vehicle number is required');
        }
        
        $result = $entryController->getBookingByVehicle($_GET['vehicleNumber']);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'booking' => $result['booking']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } else {
        sendErrorResponse('Invalid action');
    }
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'calculate';
    
    if ($action === 'calculate') {
        // Calculate exit charges
        $bookingId = $data['bookingId'] ?? null;
        $vehicleNumber = $data['vehicleNumber'] ?? null;
        
        if (!$bookingId && !$vehicleNumber) {
            sendErrorResponse('Booking ID or Vehicle Number is required');
        }
        
        $result = $paymentController->calculateExitCharges($bookingId, $vehicleNumber);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'exit' => $result['exit']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } elseif ($action === 'confirm') {
        // Confirm exit and free slot
        if (!isset($data['bookingId'])) {
            sendErrorResponse('Booking ID is required');
        }
        
        $result = $paymentController->confirmExit($data['bookingId']);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Exit confirmed successfully'
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } elseif ($action === 'extend') {
        // Extend parking time
        if (!isset($data['bookingId']) || !isset($data['hours']) || !isset($data['amount'])) {
            sendErrorResponse('Booking ID, hours, and amount are required');
        }
        
        $result = $paymentController->extendParking($data['bookingId'], $data['hours'], $data['amount']);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Parking time extended successfully',
                'booking' => $result['booking']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } else {
        sendErrorResponse('Invalid action');
    }
} else {
    sendErrorResponse('Invalid request method', 405);
}






