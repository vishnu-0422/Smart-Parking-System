<?php
/**
 * Database Configuration
 * Smart Parking System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_parking');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get database connection
 * @return PDO|null
 */
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            return null;
        }
    }
    
    return $pdo;
}

/**
 * Send JSON response
 */
function sendJSONResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Send error response
 */
function sendErrorResponse($message, $statusCode = 400) {
    sendJSONResponse([
        'success' => false,
        'message' => $message
    ], $statusCode);
}

/**
 * Send success response
 */
function sendSuccessResponse($data = [], $message = 'Success') {
    sendJSONResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}






