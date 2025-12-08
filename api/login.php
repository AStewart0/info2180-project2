<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

$pdo = connectDB();

$email = sanitize($_POST['email']);
$password = $_POST['password'];

if (empty($email) || empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, password, role, firstname, lastname FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful: set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];

        echo json_encode(['success' => true, 'role' => $user['role'], 'message' => 'Login successful.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An unexpected error occurred during login.']);
}
?>