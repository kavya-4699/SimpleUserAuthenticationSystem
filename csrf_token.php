<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generate a new token if not set
    }
    return $_SESSION['csrf_token'];
}

function validateToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
