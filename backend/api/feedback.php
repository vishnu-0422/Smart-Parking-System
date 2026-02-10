<?php
/**
 * Feedback API
 * Handles user feedback and suggestions for the parking system
 */

require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Simple authentication check for admin-only endpoints
function checkAuth() {
    // Try multiple ways to get Authorization header
    $token = '';
    
    // Method 1: getallheaders() if available
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? '';
    }
    
    // Method 2: Check $_SERVER for HTTP_AUTHORIZATION
    if (empty($token)) {
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    }
    
    // Method 3: Check $_SERVER for REDIRECT_HTTP_AUTHORIZATION (for some server configs)
    if (empty($token)) {
        $token = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    }
    
    $token = str_replace('Bearer ', '', $token);
    
    // Simple token check (replace with proper authentication)
    if (empty($token) || $token !== 'admin_token_123') {
        sendErrorResponse('Unauthorized', 401);
    }
}

// Get action from either GET or POST
$action = '';
$data = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'] ?? 'submit';
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
}

// Handle different actions
if ($action === 'submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // Submit feedback (no auth required for users)
    try {
        $pdo = getDBConnection();
        
        if (!$pdo) {
            sendErrorResponse('Database connection failed', 500);
        }

        $userId = $data['userId'] ?? null;
        $vehicleNumber = $data['vehicleNumber'] ?? '';
        $bookingId = $data['bookingId'] ?? null;
        $rating = $data['rating'] ?? 0;
        $improvements = isset($data['improvements']) ? json_encode($data['improvements']) : null;
        $feedback = $data['feedback'] ?? '';
        $timestamp = $data['timestamp'] ?? date('Y-m-d H:i:s');

        // Check if feedback table exists, if not create it
        try {
            $pdo->query("SELECT 1 FROM feedback LIMIT 1");
        } catch (PDOException $e) {
            // Create feedback table if it doesn't exist
            $pdo->exec("
                CREATE TABLE IF NOT EXISTS feedback (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT,
                    vehicle_number VARCHAR(20),
                    booking_id INT,
                    rating INT NOT NULL,
                    improvements JSON,
                    feedback_text TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    status VARCHAR(50) DEFAULT 'new'
                )
            ");
        }

        // Insert feedback into database
        $stmt = $pdo->prepare("
            INSERT INTO feedback (user_id, vehicle_number, booking_id, rating, improvements, feedback_text, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $userId,
            $vehicleNumber,
            $bookingId,
            (int)$rating,
            $improvements,
            $feedback,
            $timestamp
        ]);

        sendJSONResponse([
            'success' => true,
            'message' => 'Feedback submitted successfully',
            'feedbackId' => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        error_log('Feedback::submit - ' . $e->getMessage());
        sendErrorResponse('Failed to submit feedback: ' . $e->getMessage(), 500);
    }

} elseif ($action === 'getFeedback' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get all feedback (for admin)
    checkAuth(); // Require admin authentication
    
    try {
        $pdo = getDBConnection();
        
        if (!$pdo) {
            sendErrorResponse('Database connection failed', 500);
        }

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        if ($limit <= 0 || $limit > 500) {
            $limit = 100;
        }

        $stmt = $pdo->prepare("
            SELECT 
                f.id,
                f.user_id,
                f.vehicle_number,
                f.booking_id,
                f.rating,
                f.improvements,
                f.feedback_text,
                f.created_at,
                f.status,
                u.name as user_name,
                u.email as user_email
            FROM feedback f
            LEFT JOIN users u ON f.user_id = u.id
            ORDER BY f.created_at DESC
            LIMIT :limit
        ");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $feedback = $stmt->fetchAll();

        sendJSONResponse([
            'success' => true,
            'feedback' => $feedback
        ]);

    } catch (PDOException $e) {
        error_log('Feedback::getFeedback - ' . $e->getMessage());
        sendErrorResponse('Failed to fetch feedback', 500);
    }

} elseif ($action === 'getFeedbackStats' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get feedback statistics (for admin dashboard)
    checkAuth(); // Require admin authentication
    
    try {
        $pdo = getDBConnection();
        
        if (!$pdo) {
            sendErrorResponse('Database connection failed', 500);
        }

        // Average rating
        $stmt = $pdo->query("SELECT AVG(rating) as avg_rating FROM feedback");
        $avgRating = $stmt->fetch()['avg_rating'] ?? 0;

        // Total feedback count
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM feedback");
        $totalFeedback = $stmt->fetch()['total'];

        // Rating distribution
        $stmt = $pdo->query("SELECT rating, COUNT(*) as count FROM feedback GROUP BY rating ORDER BY rating");
        $ratingDistribution = $stmt->fetchAll();

        // Most common improvements
        $stmt = $pdo->query("
            SELECT improvements FROM feedback WHERE improvements IS NOT NULL
        ");
        $improvements = [];
        foreach ($stmt->fetchAll() as $row) {
            $items = json_decode($row['improvements'], true);
            if (is_array($items)) {
                foreach ($items as $item) {
                    $improvements[$item] = ($improvements[$item] ?? 0) + 1;
                }
            }
        }
        arsort($improvements);

        sendJSONResponse([
            'success' => true,
            'stats' => [
                'average_rating' => round($avgRating, 2),
                'total_feedback' => $totalFeedback,
                'rating_distribution' => $ratingDistribution,
                'top_improvements' => array_slice($improvements, 0, 5)
            ]
        ]);

    } catch (PDOException $e) {
        error_log('Feedback::getFeedbackStats - ' . $e->getMessage());
        sendErrorResponse('Failed to fetch feedback statistics', 500);
    }

} else {
    sendErrorResponse('Invalid action or request method');
}
?>
