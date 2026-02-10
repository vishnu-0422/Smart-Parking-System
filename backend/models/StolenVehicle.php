<?php
/**
 * Stolen Vehicle Model
 * Handles stolen vehicle data operations
 */

require_once __DIR__ . '/../config/db.php';

class StolenVehicle {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Check if a vehicle is reported as stolen
     */
    public function isStolen($vehicleNumber) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT * FROM stolen_vehicles 
                WHERE vehicle_number = ? AND status = 'active'
            ");
            $stmt->execute([$vehicleNumber]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("StolenVehicle::isStolen - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add a stolen vehicle
     */
    public function addStolenVehicle($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO stolen_vehicles (vehicle_number, vehicle_type, owner_name, description, status, reported_date)
                VALUES (?, ?, ?, ?, 'active', NOW())
            ");
            
            return $stmt->execute([
                $data['vehicleNumber'],
                $data['vehicleType'],
                $data['ownerName'],
                $data['description'] ?? null
            ]);
        } catch (PDOException $e) {
            error_log("StolenVehicle::addStolenVehicle - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all stolen vehicles
     */
    public function getAllStolenVehicles() {
        try {
            $stmt = $this->pdo->query("
                SELECT * FROM stolen_vehicles 
                ORDER BY reported_date DESC
            ");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("StolenVehicle::getAllStolenVehicles - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get stolen vehicle by ID
     */
    public function getStolenVehicleById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM stolen_vehicles WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("StolenVehicle::getStolenVehicleById - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update stolen vehicle status
     */
    public function updateStatus($id, $status) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE stolen_vehicles 
                SET status = ?, updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log("StolenVehicle::updateStatus - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Remove stolen vehicle alert
     */
    public function removeAlert($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM stolen_vehicles WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("StolenVehicle::removeAlert - " . $e->getMessage());
            return false;
        }
    }
}






