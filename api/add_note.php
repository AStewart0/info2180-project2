<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Permission Denied. Must be logged in.']);
    exit;
}

$pdo = connectDB();

$contact_id = (int)$_POST['contact_id'];
$comment = sanitize($_POST['comment']);
$created_by = $_SESSION['user_id'];
$created_at = date('Y-m-d H:i:s');

if (empty($contact_id) || empty($comment)) {
    echo json_encode(['success' => false, 'message' => 'Comment cannot be empty.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // 1. Insert the new note
    $stmt_note = $pdo->prepare(
        "INSERT INTO Notes (contact_id, comment, created_by, created_at) VALUES (?, ?, ?, ?)"
    );
    $stmt_note->execute([$contact_id, $comment, $created_by, $created_at]);

    // 2. Update the contact's updated_at timestamp
    $stmt_contact = $pdo->prepare(
        "UPDATE Contacts SET updated_at = ? WHERE id = ?"
    );
    $stmt_contact->execute([$created_at, $contact_id]);

    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Note added successfully.']);

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Add note error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error adding note and updating contact.']);
}
?>