<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission Denied.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$contact_id = (int)($data['contact_id'] ?? 0);
$current_type = $data['current_type'] ?? '';
$updated_at = date('Y-m-d H:i:s');

if (empty($contact_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid contact ID.']);
    exit;
}

$new_type = ($current_type === 'Sales Lead') ? 'Support' : 'Sales Lead';

$pdo = connectDB();

try {
    $stmt = $pdo->prepare(
        "UPDATE Contacts SET type = ?, updated_at = ? WHERE id = ?"
    );
    $stmt->execute([$new_type, $updated_at, $contact_id]);
    
    echo json_encode(['success' => true, 'message' => "Contact type successfully changed to {$new_type}.", 'new_type' => $new_type]);

} catch (Exception $e) {
    error_log("Switch type error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error switching contact type.']);
}
?>