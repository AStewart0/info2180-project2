<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission Denied. Must be logged in.']);
    exit;
}

$pdo = connectDB();

$title = sanitize($_POST['title']);
$firstname = sanitize($_POST['firstname']);
$lastname = sanitize($_POST['lastname']);
$email = sanitize($_POST['email']);
$telephone = sanitize($_POST['telephone']);
$company = sanitize($_POST['company']);
$type = sanitize($_POST['type']);
$assigned_to = (int)$_POST['assigned_to']; 
$created_by = $_SESSION['user_id'];
$timestamp = date('Y-m-d H:i:s');

if (empty($firstname) || empty($lastname) || empty($email) || empty($type) || empty($assigned_to)) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing.']);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "INSERT INTO Contacts (title, firstname, lastname, email, telephone, company, type, assigned_to, created_by, created_at, updated_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->execute([
        $title, $firstname, $lastname, $email, $telephone, $company, $type, 
        $assigned_to, $created_by, $timestamp, $timestamp
    ]);
    
    echo json_encode(['success' => true, 'message' => 'Contact added successfully.']);

} catch (Exception $e) {
    error_log("Add contact error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error while adding contact.']);
}
?>