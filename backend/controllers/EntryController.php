<?php
/**
 * Entry Controller
 * Handles vehicle entry and registration logic
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Vehicle.php';
require_once __DIR__ . '/../models/StolenVehicle.php';
require_once __DIR__ . '/../models/Alert.php';

class EntryController {
    private $vehicleModel;
    private $stolenVehicleModel;
    private $alertModel;
    
    public function __construct() {
        $this->vehicleModel = new Vehicle();
        $this->stolenVehicleModel = new StolenVehicle();
        $this->alertModel = new Alert();
    }
    
    /**
     * Register a new vehicle entry
     */
    public function registerVehicle($data) {
        try {
            // Check if vehicle is stolen
            $isStolen = $this->stolenVehicleModel->isStolen($data['vehicleNumber']);

            // Register vehicle (optionally linking to logged-in user via email)
            $vehicleId = $this->vehicleModel->registerVehicle($data);
            
            if ($vehicleId) {
                // If stolen, create admin alert but still allow parking
                if ($isStolen) {
                    $title = 'Stolen Vehicle Detected at Entry';
                    $message = sprintf(
                        'Vehicle %s (%s) owned by %s has been reported as stolen but was allowed to park. Please review immediately.',
                        $data['vehicleNumber'],
                        $data['vehicleType'],
                        $data['ownerName']
                    );
                    $this->alertModel->createAdminAlert(
                        'stolen_vehicle',
                        $title,
                        $message,
                        'critical',
                        $vehicleId,
                        null,
                        null
                    );
                }

                return [
                    'success' => true,
                    'vehicleId' => $vehicleId,
                    'isStolen' => $isStolen
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to register vehicle'
                ];
            }
        } catch (Exception $e) {
            error_log("EntryController::registerVehicle - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while registering the vehicle'
            ];
        }
    }
    
    /**
     * Get vehicle information
     */
    public function getVehicle($vehicleId) {
        try {
            $vehicle = $this->vehicleModel->getVehicleById($vehicleId);
            
            if ($vehicle) {
                return [
                    'success' => true,
                    'vehicle' => $vehicle
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Vehicle not found'
                ];
            }
        } catch (Exception $e) {
            error_log("EntryController::getVehicle - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving vehicle information'
            ];
        }
    }
    
    /**
     * Get booking by vehicle number
     */
    public function getBookingByVehicle($vehicleNumber) {
        try {
            $pdo = getDBConnection();
            
            $stmt = $pdo->prepare("
                SELECT b.*, v.vehicle_number, v.vehicle_type, s.slot_number, s.price_per_hour as rate_per_hour
                FROM bookings b
                JOIN vehicles v ON b.vehicle_id = v.id
                LEFT JOIN slots s ON b.slot_id = s.id
                WHERE v.vehicle_number = ? AND b.exit_time IS NULL
                ORDER BY b.created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$vehicleNumber]);
            $booking = $stmt->fetch();
            
            if ($booking) {
                // Calculate duration
                $entryTime = new DateTime($booking['entry_time']);
                $expiryTime = new DateTime($booking['expiry_time']);
                $now = new DateTime();
                
                $duration = $now->diff($entryTime);
                $booking['duration'] = $duration->format('%h hours %i minutes');
                
                return [
                    'success' => true,
                    'booking' => $booking
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No active booking found for this vehicle'
                ];
            }
        } catch (Exception $e) {
            error_log("EntryController::getBookingByVehicle - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving booking information'
            ];
        }
    }
}




