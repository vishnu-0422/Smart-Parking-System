<?php
/**
 * Vehicle Model
 * Handles vehicle data operations
 */

require_once __DIR__ . '/../config/db.php';

class Vehicle {
    private $pdo;
    
    public function __construct() {
        $this->pdo = getDBConnection();
    }
    
    /**
     * Register a new vehicle
     */
    public function registerVehicle($data) {
        try {
            // Check if vehicle already exists
            $checkStmt = $this->pdo->prepare("SELECT id FROM vehicles WHERE vehicle_number = ?");
            $checkStmt->execute([$data['vehicleNumber']]);
            $existingVehicle = $checkStmt->fetch();
            
            if ($existingVehicle) {
                // Vehicle exists, return existing ID
                return $existingVehicle['id'];
            }
            
            // Vehicle doesn't exist, create new one
            $stmt = $this->pdo->prepare("
                INSERT INTO vehicles (vehicle_number, vehicle_type, owner_name, phone_number, email, created_at, updated_at)
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            
            $stmt->execute([
                $data['vehicleNumber'],
                $data['vehicleType'],
                $data['ownerName'],
                $data['phoneNumber'],
                $data['userEmail'] ?? null
            ]);
            
            $vehicleId = $this->pdo->lastInsertId();

            error_log("Vehicle registered: ID=$vehicleId, Number={$data['vehicleNumber']}, Type={$data['vehicleType']}");

            return $vehicleId;
        } catch (PDOException $e) {
            error_log("Vehicle::registerVehicle - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get vehicle by ID
     */
    public function getVehicleById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vehicles WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Vehicle::getVehicleById - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get vehicle by vehicle number
     */
    public function getVehicleByNumber($vehicleNumber) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM vehicles WHERE vehicle_number = ?");
            $stmt->execute([$vehicleNumber]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Vehicle::getVehicleByNumber - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all vehicles
     */
    public function getAllVehicles() {
        try {
            $stmt = $this->pdo->query("SELECT * FROM vehicles ORDER BY created_at DESC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Vehicle::getAllVehicles - " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update vehicle information
     */
    public function updateVehicle($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE vehicles 
                SET vehicle_type = ?, owner_name = ?, phone_number = ?, updated_at = NOW()
                WHERE id = ?
            ");
            
            return $stmt->execute([
                $data['vehicleType'],
                $data['ownerName'],
                $data['phoneNumber'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Vehicle::updateVehicle - " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete vehicle
     */
    public function deleteVehicle($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM vehicles WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Vehicle::deleteVehicle - " . $e->getMessage());
            return false;
        }
    }
}




