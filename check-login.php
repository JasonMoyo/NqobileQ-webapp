<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Make sure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$response = [
    'logged_in' => isset($_SESSION['user_id']),
    'user_name' => $_SESSION['user_name'] ?? null
];

echo json_encode($response);
?>