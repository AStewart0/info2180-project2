<?php
// File: api/get_users_list.php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

// Check 1: Ensure the user is logged in
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission Denied. Must be logged in.']);
    exit;
}

$pdo = connectDB();

try {
    // Check 2: The SQL query to retrieve all users
    $stmt = $pdo->query("SELECT id, firstname, lastname FROM Users ORDER BY firstname");
    $users = $stmt->fetchAll();

    // Format the list for the JavaScript dropdown
    $user_list = array_map(function($user) {
        return ['id' => $user['id'], 'name' => $user['firstname'] . ' ' . $user['lastname']];
    }, $users);
    
    echo json_encode($user_list);

} catch (Exception $e) {
    // Check 3: Log any database error
    error_log("Get users list error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error retrieving user list.']);
}
?>