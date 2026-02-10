<?php
/**
 * Slot Controller
 * Handles parking slot management and assignment
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Slot.php';

class SlotController {
    private $slotModel;
    
    public function __construct() {
        $this->slotModel = new Slot();
    }
    
    /**
     * Get available slots
     */
    public function getAvailableSlots() {
        try {
            $slots = $this->slotModel->getAvailableSlots();
            
            return [
                'success' => true,
                'slots' => $slots
            ];
        } catch (Exception $e) {
            error_log("SlotController::getAvailableSlots - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving available slots'
            ];
        }
    }
    
    /**
     * Assign a slot to a vehicle
     */
    public function assignSlot($vehicleId, $slotId) {
        try {
            $pdo = getDBConnection();
            
            // Check if slot is available
            $slot = $this->slotModel->getSlotById($slotId);
            
            if (!$slot) {
                return [
                    'success' => false,
                    'message' => 'Slot not found'
                ];
            }
            
            if ($slot['status'] !== 'available') {
                return [
                    'success' => false,
                    'message' => 'Slot is not available'
                ];
            }
            
            // Start transaction
            $pdo->beginTransaction();
            
            try {
                // Update slot status
                $stmt = $pdo->prepare("UPDATE slots SET status = 'occupied' WHERE id = ?");
                $stmt->execute([$slotId]);
                
                // Create booking with current real-time entry
                $entryTime = new DateTime('now', new DateTimeZone(ini_get('date.timezone') ?: 'UTC'));
                $entryTimeStr = $entryTime->format('Y-m-d H:i:s');
                
                // Set expiry to 1 hour from entry time
                $expiryTime = clone $entryTime;
                $expiryTime->modify('+1 hour');
                $expiryTimeStr = $expiryTime->format('Y-m-d H:i:s');
                
                error_log("Booking created - Entry: {$entryTimeStr}, Expiry: {$expiryTimeStr}");
                
                $stmt = $pdo->prepare("
                    INSERT INTO bookings (vehicle_id, slot_id, entry_time, expiry_time, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$vehicleId, $slotId, $entryTimeStr, $expiryTimeStr]);
                
                $bookingId = $pdo->lastInsertId();
                
                $pdo->commit();
                
                return [
                    'success' => true,
                    'bookingId' => $bookingId
                ];
            } catch (Exception $e) {
                $pdo->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log("SlotController::assignSlot - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while assigning the slot'
            ];
        }
    }
    
    /**
     * Free a slot
     */
    public function freeSlot($slotId) {
        try {
            $result = $this->slotModel->updateSlotStatus($slotId, 'available');
            
            return [
                'success' => $result
            ];
        } catch (Exception $e) {
            error_log("SlotController::freeSlot - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while freeing the slot'
            ];
        }
    }
}





