<?php
/**
 * User API
 * Handles user registration, login, profile and parking history
 */

require_once '../../backend/config/db.php';
require_once '../../backend/models/User.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data)) {
        $data = $_POST;
    }

    $action = $data['action'] ?? '';

    if ($action === 'register') {
        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            sendErrorResponse('Name, email, and password are required');
        }

        $result = $userModel->register($data);

        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'message' => 'Account created successfully',
                'userId' => $result['userId']
            ]);
        } else {
            sendErrorResponse($result['message'] ?? 'Failed to create account');
        }
    } elseif ($action === 'login') {
        if (empty($data['email']) || empty($data['password'])) {
            sendErrorResponse('Email and password are required');
        }

        $result = $userModel->login($data['email'], $data['password']);

        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'user' => $result['user'],
                'token' => $result['token']
            ]);
        } else {
            sendErrorResponse($result['message'] ?? 'Login failed');
        }
    } else {
        sendErrorResponse('Invalid action');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';

    if ($action === 'profile') {
        if (empty($_GET['userId'])) {
            sendErrorResponse('User ID is required');
        }

        $user = $userModel->getById($_GET['userId']);
        if ($user) {
            sendJSONResponse([
                'success' => true,
                'user' => $user
            ]);
        } else {
            sendErrorResponse('User not found');
        }
    } elseif ($action === 'history') {
        if (empty($_GET['userId'])) {
            sendErrorResponse('User ID is required');
        }

        $result = $userModel->getParkingHistory($_GET['userId']);

        if ($result['success']) {
            sendJSONResponse([
                'success' => true,
                'history' => $result['history'],
                'summary' => $result['summary']
            ]);
        } else {
            sendErrorResponse($result['message'] ?? 'Failed to load history');
        }
    } else {
        sendErrorResponse('Invalid action');
    }
} else {
    sendErrorResponse('Invalid request method', 405);
}




