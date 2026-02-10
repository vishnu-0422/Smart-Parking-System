<?php
/**
 * Payment API
 * Handles payment processing and ticket generation
 */

require_once '../../backend/config/db.php';
require_once '../../backend/controllers/PaymentController.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$controller = new PaymentController();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getBooking') {
        if (!isset($_GET['vehicleId']) || !isset($_GET['slotId'])) {
            sendErrorResponse('Vehicle ID and Slot ID are required');
        }
        
        $result = $controller->getBookingInfo($_GET['vehicleId'], $_GET['slotId']);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'booking' => $result['booking']
            ]);
        } else {
            sendErrorResponse($result['message']);
        }
    } elseif ($action === 'getTicket') {
        if (!isset($_GET['bookingId'])) {
            sendErrorResponse('Booking ID is required');
        }
        
        $result = $controller->getTicket($_GET['bookingId']);
        
        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'ticket' => $result['ticket']
            ]);
        } else {
            sendErrorResponse($result['message']);
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
    
    $action = $data['action'] ?? 'process';
    
    if ($action === 'generateUPI') {
        // Generate UPI QR and store parking entry
        if (!isset($data['vehicleId']) || !isset($data['slotId']) || !isset($data['amount'])) {
            sendErrorResponse('Vehicle ID, Slot ID, and Amount are required');
        }
        
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
        
    } elseif ($action === 'process' || !isset($data['action'])) {
        // Check if this is an extension payment
        $paymentType = $data['type'] ?? 'booking';
        
        error_log("Payment process - Type: {$paymentType}, Data: " . json_encode($data));
        
        if ($paymentType === 'extension') {
            // Handle parking extension payment
            if (!isset($data['bookingId']) || !isset($data['hours']) || !isset($data['amount'])) {
                error_log("Extension payment missing fields - bookingId: " . isset($data['bookingId']) . ", hours: " . isset($data['hours']) . ", amount: " . isset($data['amount']));
                sendErrorResponse('Missing required fields for extension: bookingId, hours, and amount');
            }
            
            // For extension, we just need to record the extension payment
            // The actual booking lookup is not needed since we already have the booking ID
            $result = $controller->processExtensionPayment($data);
            
            if ($result['success']) {
                sendJSONResponse([
                    'success' => true,
                    'message' => 'Extension payment processed successfully',
                    'bookingId' => $data['bookingId'],
                    'extensionId' => $result['extensionId'] ?? null
                ]);
            } else {
                sendErrorResponse($result['message'] ?? 'Failed to process extension payment');
            }
        } else {
            // Standard payment processing for new bookings
            if (!isset($data['vehicleId']) || !isset($data['slotId']) || !isset($data['paymentMethod'])) {
                sendErrorResponse('Missing required fields for booking');
            }
            
            $result = $controller->processPayment($data);
            
            if ($result['success']) {
                sendJSONResponse([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'bookingId' => $result['bookingId']
                ]);
            } else {
                sendErrorResponse($result['message']);
            }
        }
    } else {
        sendErrorResponse('Invalid action');
    }
} else {
    sendErrorResponse('Invalid request method', 405);
}

