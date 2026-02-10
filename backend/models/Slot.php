<?php
/**
 * Slot Model
 * Handles parking slot data operations
 */

require_once __DIR__ . '/../config/db.php';

class Slot {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Get all available slots
     */
    public function getAvailableSlots() {
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM slots 
                WHERE status = 'available' 
                ORDER BY slot_number ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Slot::getAvailableSlots - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all slots
     */
    public function getAllSlots() {
        try {
            $stmt = $this->pdo->query("
                SELECT s.*, v.vehicle_number, b.entry_time
                FROM slots s
                LEFT JOIN bookings b ON s.id = b.slot_id AND b.exit_time IS NULL
                LEFT JOIN vehicles v ON b.vehicle_id = v.id
                ORDER BY s.slot_number ASC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Slot::getAllSlots - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get slot by ID
     */
    public function getSlotById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM slots WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Slot::getSlotById - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add a new slot
     */
    public function addSlot($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO slots (slot_number, slot_type, price_per_hour, status, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            
            return $stmt->execute([
                $data['slotNumber'],
                $data['slotType'],
                $data['pricePerHour'],
                $data['status'] ?? 'available'
            ]);
        } catch (PDOException $e) {
            error_log("Slot::addSlot - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update slot
     */
    public function updateSlot($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE slots 
                SET slot_number = ?, slot_type = ?, price_per_hour = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['slotNumber'],
                $data['slotType'],
                $data['pricePerHour'],
                $data['status'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Slot::updateSlot - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update slot status
     */
    public function updateSlotStatus($id, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE slots 
                SET status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("Slot::updateSlotStatus - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete slot
     */
    public function deleteSlot($id) {
        try {
            // Check if slot is currently occupied
            $stmt = $this->pdo->prepare("
                SELECT COUNT(*) as count FROM bookings 
                WHERE slot_id = ? AND exit_time IS NULL
            ");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            
            if ($result['count'] > 0) {
                return false; // Cannot delete occupied slot
            }
            
            $stmt = $this->pdo->prepare("DELETE FROM slots WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Slot::deleteSlot - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Automatically recommend and assign the first available slot
     * Uses transaction and row locking to prevent race conditions
     * 
     * @param string|null $vehicleType Optional: filter by vehicle type
     * @return array|false Returns slot data on success, false on failure
     */
    public function autoAssignSlot($vehicleType = null) {
        try {
            $this->pdo->beginTransaction();
            
            // Build query with optional vehicle type filter
            if ($vehicleType) {
                $sql = "
                    SELECT id, slot_number, slot_type, price_per_hour
                    FROM slots
                    WHERE status = 'available' AND slot_type = ?
                    ORDER BY slot_number ASC
                    LIMIT 1
                    FOR UPDATE
                ";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$vehicleType]);
            } else {
                $sql = "
                    SELECT id, slot_number, slot_type, price_per_hour
                    FROM slots
                    WHERE status = 'available'
                    ORDER BY slot_number ASC
                    LIMIT 1
                    FOR UPDATE
                ";
                $stmt = $this->pdo->query($sql);
            }
            
            $slot = $stmt->fetch();
            
            if (!$slot) {
                $this->pdo->rollBack();
                return false; // No available slots
            }
            
            // Mark slot as occupied
            $updateStmt = $this->pdo->prepare("
                UPDATE slots 
                SET status = 'occupied', updated_at = NOW()
                WHERE id = ?
            ");
            $updateStmt->execute([$slot['id']]);
            
            $this->pdo->commit();
            
            return [
                'id' => (int)$slot['id'],
                'slot_number' => $slot['slot_number'],
                'slot_type' => $slot['slot_type'],
                'price_per_hour' => (float)$slot['price_per_hour']
            ];
            
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log("Slot::autoAssignSlot - " . $e->getMessage());
            return false;
        }
    }
}

