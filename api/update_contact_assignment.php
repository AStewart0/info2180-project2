<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission Denied.']);
    exit;
}

// Data is sent as JSON for this action
$data = json_decode(file_get_contents('php://input'), true);
$contact_id = (int)($data['contact_id'] ?? 0);
$assigned_to_id = $_SESSION['user_id'];
$updated_at = date('Y-m-d H:i:s');

if (empty($contact_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid contact ID.']);
    exit;
}

$pdo = connectDB();

try {
    $stmt = $pdo->prepare(
        "UPDATE Contacts SET assigned_to = ?, updated_at = ? WHERE id = ?"
    );
    $stmt->execute([$assigned_to_id, $updated_at, $contact_id]);
    
    echo json_encode(['success' => true, 'message' => 'Contact assigned to you successfully.']);

} catch (Exception $e) {
    error_log("Assign contact error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error assigning contact.']);
}
?>