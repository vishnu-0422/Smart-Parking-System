<?php
/**
 * Alert Model
 * Handles alert data operations
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/StolenVehicle.php';

class Alert {
    private $pdo;
    private $stolenVehicleModel;
    
    public function __construct() {
        $this->pdo = getDBConnection();
        $this->stolenVehicleModel = new StolenVehicle();
    }
    
    /**
     * Get all alerts (stolen vehicles)
     */
    public function getAllAlerts() {
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM stolen_vehicles 
                ORDER BY reported_date DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Alert::getAllAlerts - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get active alerts
     */
    public function getActiveAlerts() {
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM stolen_vehicles 
                WHERE status = 'active'
                ORDER BY reported_date DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Alert::getActiveAlerts - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Add stolen vehicle alert
     */
    public function addStolenVehicle($data) {
        return $this->stolenVehicleModel->addStolenVehicle($data);
    }
    
    /**
     * Remove alert
     */
    public function removeAlert($id) {
        return $this->stolenVehicleModel->removeAlert($id);
    }
    
    /**
     * Check for stolen vehicles in parking
     */
    public function checkStolenVehiclesInParking() {
        try {
            $stmt = $this->pdo->query("
                SELECT v.vehicle_number, v.vehicle_type, v.owner_name, b.slot_id, s.slot_number
                FROM bookings b
                JOIN vehicles v ON b.vehicle_id = v.id
                JOIN slots s ON b.slot_id = s.id
                JOIN stolen_vehicles sv ON v.vehicle_number = sv.vehicle_number
                WHERE b.exit_time IS NULL AND sv.status = 'active'
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Alert::checkStolenVehiclesInParking - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create admin alert
     */
    public function createAdminAlert($alertType, $title, $message, $severity = 'medium', $relatedVehicleId = null, $relatedSlotId = null, $relatedEntryId = null) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO admin_alerts (
                    alert_type, title, message, severity,
                    related_vehicle_id, related_slot_id, related_entry_id,
                    status, created_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, 'new', NOW())
            ");
            
            return $stmt->execute([
                $alertType,
                $title,
                $message,
                $severity,
                $relatedVehicleId,
                $relatedSlotId,
                $relatedEntryId
            ]);
        } catch (PDOException $e) {
            error_log("Alert::createAdminAlert - " . $e->getMessage());
            return false;
        }
    }
}

