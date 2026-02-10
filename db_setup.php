<?php
/**
 * Database Setup Script
 * Imports the smart_parking.sql file
 */

require_once __DIR__ . '/backend/config/db.php';

try {
    // First, try to connect without database to create it
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Database 'smart_parking' created or already exists.\n";
    
    // Select the database
    $pdo->exec("USE " . DB_NAME);
    
    // Read and execute the SQL file
    $sql = file_get_contents(__DIR__ . '/database/smart_parking.sql');
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $pdo->exec($statement);
            } catch (PDOException $e) {
                // Skip errors for existing tables/indexes
                if (strpos($e->getMessage(), 'already exists') === false && 
                    strpos($e->getMessage(), 'Duplicate key') === false) {
                    echo "Error executing statement: " . $statement . "\n";
                    echo "Error: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "Database imported successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>