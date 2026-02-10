<?php
/**
 * Smart Parking System - Main Entry Point
 * Redirects users to appropriate pages based on their role
 */

session_start();

// Check if user is logged in
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header('Location: frontend/admin/dashboard.html');
    } else {
        header('Location: frontend/user/entry.html');
    }
    exit();
}

// Default redirect to user entry page
header('Location: frontend/user/entry.html');
exit();
?>






