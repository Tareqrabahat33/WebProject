<?php
// Start the session
session_start();

// Check if user is logged in and return user info as JSON
header('Content-Type: application/json');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'loggedIn' => true,
        'userName' => $_SESSION['user_name'] ?? 'User',
        'userEmail' => $_SESSION['user_email'] ?? '',
        'isAdmin' => $_SESSION['is_admin'] ?? false
    ]);
} else {
    echo json_encode([
        'loggedIn' => false
    ]);
}
?>
