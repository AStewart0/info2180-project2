<?php
// Start a secure session for all PHP files that require user context
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database Connection Details - IMPORTANT: UPDATE THESE
define('DB_HOST', 'localhost');
define('DB_NAME', 'dolphin_crm');
define('DB_USER', 'root'); 
define('DB_PASS', 'password123'); 

// Function to establish PDO connection
function connectDB() {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";port=3307;dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit();
    }
}

// Helper functions for security and access control
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function sanitize($input) {
    // Basic sanitization: strip whitespace and HTML special chars
    return htmlspecialchars(trim($input ?? ''), ENT_QUOTES, 'UTF-8');
}
?>