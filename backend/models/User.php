<?php
/**
 * User Model
 * Handles user accounts (registration, login, profile, history)
 */

require_once __DIR__ . '/../config/db.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = getDBConnection();
    }

    /**
     * Register a new user
     */
    public function register($data) {
        try {
            // Check if email already exists
            $stmt = $this->pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) {
                return [
                    'success' => false,
                    'message' => 'Email is already registered'
                ];
            }

            $passwordHash = password_hash($data['password'], PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("
                INSERT INTO users (name, email, phone, password_hash, created_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $passwordHash
            ]);

            $userId = $this->pdo->lastInsertId();

            return [
                'success' => true,
                'userId' => $userId
            ];
        } catch (PDOException $e) {
            error_log("User::register - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to create user account'
            ];
        }
    }

    /**
     * Login user by email/password
     */
    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                return [
                    'success' => false,
                    'message' => 'Invalid email or password'
                ];
            }

            // Simple token based on user ID (for demo – in production use JWT)
            $token = 'user_token_' . $user['id'];

            return [
                'success' => true,
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'phone' => $user['phone']
                ],
                'token' => $token
            ];
        } catch (PDOException $e) {
            error_log("User::login - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to login'
            ];
        }
    }

    /**
     * Get user by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT id, name, email, phone, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("User::getById - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get parking history for user (by email)
     * Uses vehicles.email to link vehicles to user
     */
    public function getParkingHistory($userId) {
        try {
            // Get user contact details
            $stmt = $this->pdo->prepare("SELECT email, phone FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if (!$user) {
                // No such user – return empty history rather than hard error
                return [
                    'success' => true,
                    'history' => [],
                    'summary' => [
                        'total_visits' => 0,
                        'total_amount' => 0
                    ]
                ];
            }

            // Prefer linking by phone number (works with existing vehicles schema)
            $linkValue = null;
            $linkByPhone = false;

            if (!empty($user['phone'])) {
                $linkValue = $user['phone'];
                $linkByPhone = true;
            } elseif (!empty($user['email'])) {
                // Fallback to email if phone not available and vehicles table has email column
                $linkValue = $user['email'];
            } else {
                // User has no phone/email – we can't link bookings, but don't treat as error
                return [
                    'success' => true,
                    'history' => [],
                    'summary' => [
                        'total_visits' => 0,
                        'total_amount' => 0
                    ]
                ];
            }

            // Join vehicles and bookings
            // If linking by phone: vehicles.phone_number = users.phone
            // If linking by email (fallback): vehicles.email = users.email
            $whereClause = $linkByPhone
                ? "v.phone_number = ?"
                : "v.email = ?";

            $sql = "
                SELECT 
                    b.id AS booking_id,
                    v.vehicle_number,
                    v.vehicle_type,
                    s.slot_number,
                    b.entry_time,
                    b.expiry_time,
                    b.exit_time,
                    b.amount_paid,
                    b.payment_method,
                    b.payment_status
                FROM bookings b
                JOIN vehicles v ON b.vehicle_id = v.id
                JOIN slots s ON b.slot_id = s.id
                WHERE {$whereClause}
                ORDER BY b.entry_time DESC
                LIMIT 100
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$linkValue]);
            $history = $stmt->fetchAll();

            // Calculate summary stats (even if empty)
            $totalVisits = count($history);
            $totalAmount = 0;
            foreach ($history as $row) {
                $totalAmount += (float)($row['amount_paid'] ?? 0);
            }

            return [
                'success' => true,
                'history' => $history,
                'summary' => [
                    'total_visits' => $totalVisits,
                    'total_amount' => $totalAmount
                ]
            ];
        } catch (PDOException $e) {
            error_log("User::getParkingHistory - " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to load parking history'
            ];
        }
    }
}


