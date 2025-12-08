<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

if (!isLoggedIn() || !isAdmin()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission Denied. Must be an Admin.']);
    exit;
}

$pdo = connectDB();

$firstname = sanitize($_POST['firstname']);
$lastname = sanitize($_POST['lastname']);
$email = sanitize($_POST['email']);
$password = $_POST['password']; 
$role = sanitize($_POST['role']);

// --- Password Validation (Required RegEx) ---
// 1. At least 8 characters long
// 2. Contains at least one letter (a-z)
// 3. Contains at least one capital letter (A-Z)
// 4. Contains at least one number (0-9)
$password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/';

if (!preg_match($password_regex, $password)) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long, contain one letter, one capital letter, and one number.']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$created_at = date('Y-m-d H:i:s');

try {
    $stmt = $pdo->prepare("INSERT INTO Users (firstname, lastname, password, email, role, created_at) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$firstname, $lastname, $hashed_password, $email, $role, $created_at]);
    
    echo json_encode(['success' => true, 'message' => 'User added successfully.']);

} catch (PDOException $e) {
    if ($e->getCode() === '23000') { // 23000 is often for Integrity Constraint Violation (e.g., duplicate email)
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
    } else {
        error_log("Add user error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error while adding user.']);
    }
}
?>