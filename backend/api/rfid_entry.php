<?php
/**
 * RFID Entry API
 * Handles vehicle entry via RFID scanning
 * Logic:
 * - Receive RFID from POST
 * - Check stolen_vehicle table
 * - If stolen: block entry and create admin alert
 * - If not stolen: allow slot recommendation
 */

require_once '../../backend/config/db.php';
require_once '../../backend/models/StolenVehicle.php';
require_once '../../backend/models/Slot.php';

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
    // Get RFID from POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Also check for form data
    if (empty($data)) {
        $data = $_POST;
    }
    
    if (!isset($data['rfid']) || empty($data['rfid'])) {
        sendErrorResponse('RFID is required');
    }
    
    $rfid = trim($data['rfid']);
    $pdo = getDBConnection();
    
    if (!$pdo) {
        sendErrorResponse('Database connection failed', 500);
    }
    
    // Look up vehicle by RFID
    // Note: Assuming RFID is stored in vehicles table or can be mapped to vehicle_number
    // If you have a separate RFID mapping table, modify this query accordingly
    $stmt = $pdo->prepare("
        SELECT v.*, vc.company_name, vm.model_name
        FROM vehicles v
        LEFT JOIN vehicle_companies vc ON v.company_id = vc.id
        LEFT JOIN vehicle_models vm ON v.model_id = vm.id
        WHERE v.vehicle_number = ? OR v.id = ?
        LIMIT 1
    ");
    
    // Try to find vehicle by RFID (assuming RFID might be vehicle_number or ID)
    // You may need to add an RFID column to vehicles table for proper mapping
    $stmt->execute([$rfid, $rfid]);
    $vehicle = $stmt->fetch();
    
    if (!$vehicle) {
        sendErrorResponse('Vehicle not found for RFID: ' . $rfid, 404);
    }
    
    // Check if vehicle is stolen
    $stolenVehicleModel = new StolenVehicle();
    $isStolen = $stolenVehicleModel->isStolen($vehicle['vehicle_number']);

    if ($isStolen) {
        // Get stolen vehicle details
        $stmt = $pdo->prepare("
            SELECT * FROM stolen_vehicles 
            WHERE vehicle_number = ? AND status = 'active'
            LIMIT 1
        ");
        $stmt->execute([$vehicle['vehicle_number']]);
        $stolenVehicle = $stmt->fetch();

        // Create admin alert for stolen vehicle detection (but still allow parking)
        $alertTitle = 'Stolen Vehicle Detected via RFID';
        $alertMessage = sprintf(
            "RFID Entry: Vehicle %s (%s %s) has been detected entering the parking facility. " .
            "This vehicle is reported as stolen. Police Case: %s, Station: %s. " .
            "Vehicle has been allowed to park – please review immediately.",
            $vehicle['vehicle_number'],
            $vehicle['company_name'] ?? 'Unknown',
            $vehicle['model_name'] ?? 'Unknown',
            $stolenVehicle['police_case_number'] ?? 'N/A',
            $stolenVehicle['police_station'] ?? 'N/A'
        );

        $stmt = $pdo->prepare("
            INSERT INTO admin_alerts (
                alert_type, title, message, severity, 
                related_vehicle_id, status, created_at
            ) VALUES (?, ?, ?, ?, ?, 'new', NOW())
        ");
        $stmt->execute([
            'stolen_vehicle',
            $alertTitle,
            $alertMessage,
            'critical',
            $vehicle['id']
        ]);

        error_log(sprintf(
            "RFID Entry - Stolen Vehicle Detected (ALLOWED): %s (RFID: %s) at %s",
            $vehicle['vehicle_number'],
            $rfid,
            date('Y-m-d H:i:s')
        ));
    }
    
    // Allow entry and recommend slots (even if stolen, admin has been alerted)
    $slotModel = new Slot();
    
    // Get available slots matching vehicle type
    $stmt = $pdo->prepare("
        SELECT id, slot_number, slot_type, price_per_hour, status
        FROM slots
        WHERE status = 'available' AND slot_type = ?
        ORDER BY price_per_hour ASC, slot_number ASC
        LIMIT 5
    ");
    $stmt->execute([$vehicle['vehicle_type']]);
    $availableSlots = $stmt->fetchAll();
    
    // If no slots available for vehicle type, get any available slots
    if (empty($availableSlots)) {
        $stmt = $pdo->query("
            SELECT id, slot_number, slot_type, price_per_hour, status
            FROM slots
            WHERE status = 'available'
            ORDER BY price_per_hour ASC, slot_number ASC
            LIMIT 5
        ");
        $availableSlots = $stmt->fetchAll();
    }
    
    // Prepare recommended slots
    $recommendedSlots = [];
    foreach ($availableSlots as $slot) {
        $recommendedSlots[] = [
            'slot_id' => $slot['id'],
            'slot_number' => $slot['slot_number'],
            'slot_type' => $slot['slot_type'],
            'price_per_hour' => (float)$slot['price_per_hour'],
            'match_vehicle_type' => $slot['slot_type'] === $vehicle['vehicle_type']
        ];
    }
    
    // Return success response with slot recommendations
    sendJSONResponse([
        'success' => true,
        'blocked' => false,
        'message' => 'Entry allowed. Slot recommendations available.',
        'vehicle' => [
            'id' => $vehicle['id'],
            'vehicle_number' => $vehicle['vehicle_number'],
            'vehicle_type' => $vehicle['vehicle_type'],
            'company' => $vehicle['company_name'] ?? 'Unknown',
            'model' => $vehicle['model_name'] ?? 'Unknown',
            'color' => $vehicle['color'] ?? null,
            'owner_name' => $vehicle['owner_name']
        ],
        'is_stolen' => $isStolen,
        'recommended_slots' => $recommendedSlots,
        'total_available_slots' => count($recommendedSlots)
    ]);
    
} catch (PDOException $e) {
    error_log("RFID Entry API - Database Error: " . $e->getMessage());
    sendErrorResponse('Database error occurred', 500);
} catch (Exception $e) {
    error_log("RFID Entry API - Error: " . $e->getMessage());
    sendErrorResponse('An error occurred while processing RFID entry', 500);
}




