<?php
// File: content/contact_details.php
require_once('../php_includes/config.php');
if (!isLoggedIn()) { header('Location: ../index.html'); exit; }
$pdo = connectDB();

$contact_id = (int)sanitize($_GET['id'] ?? 0);
if ($contact_id === 0) {
    echo "<h2>Contact Not Found.</h2>";
    exit;
}

// 1. Fetch Contact Details, including creator and assigned user names [cite: 178]
$sql_contact = "
    SELECT 
        c.*, 
        uc.firstname AS creator_fname, uc.lastname AS creator_lname,
        ua.firstname AS assigned_fname, ua.lastname AS assigned_lname
    FROM Contacts c
    LEFT JOIN Users uc ON c.created_by = uc.id
    LEFT JOIN Users ua ON c.assigned_to = ua.id
    WHERE c.id = ?
";
$stmt_contact = $pdo->prepare($sql_contact);
$stmt_contact->execute([$contact_id]);
$contact = $stmt_contact->fetch();

if (!$contact) {
    echo "<h2>Contact Not Found.</h2>";
    exit;
}

// 2. Fetch Notes [cite: 182]
$sql_notes = "
    SELECT n.comment, n.created_at, u.firstname, u.lastname
    FROM Notes n
    JOIN Users u ON n.created_by = u.id
    WHERE n.contact_id = ?
    ORDER BY n.created_at DESC
";
$stmt_notes = $pdo->prepare($sql_notes);
$stmt_notes->execute([$contact_id]);
$notes = $stmt_notes->fetchAll();

// Determine action button states
$is_assigned_to_me = $contact['assigned_to'] == $_SESSION['user_id'];
$opposite_type = ($contact['type'] === 'Sales Lead') ? 'Support' : 'Sales Lead';
$current_type_class = strtolower(str_replace(' ', '-', $contact['type']));

?>

<div class="contact-details-page">
    <header style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1><?= htmlspecialchars($contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname']) ?></h1>
            <p style="font-size: 1.1em;">Contact | <span class="<?= $current_type_class ?>"><?= htmlspecialchars($contact['type']) ?></span></p>
            <p>Email: <?= htmlspecialchars($contact['email']) ?></p>
            <p>Company: <?= htmlspecialchars($contact['company']) ?></p>
            <p>Telephone: <?= htmlspecialchars($contact['telephone']) ?></p>
        </div>
        
        <div class="contact-actions">
            <button 
                class="contact-action-btn" 
                data-id="<?= $contact['id'] ?>" 
                data-action="switch" 
                data-current-type="<?= htmlspecialchars($contact['type']) ?>"
            >
                <i class="fas fa-exchange-alt"></i> Switch to <?= $opposite_type ?>
            </button>
            <button 
                class="contact-action-btn" 
                data-id="<?= $contact['id'] ?>" 
                data-action="assign"
            >
                <i class="fas fa-user-plus"></i> Assign to me
            </button>
            <div id="contact-details-feedback" class="feedback-message"></div>
        </div>
    </header>

    <hr>
    
    <div style="display: flex; justify-content: space-between; font-size: 0.9em; color: #555;">
        <div>
            <p><strong>Assigned To:</strong> <?= htmlspecialchars($contact['assigned_fname'] . ' ' . $contact['assigned_lname']) ?></p>
            <p><strong>Created By:</strong> <?= htmlspecialchars($contact['creator_fname'] . ' ' . $contact['creator_lname']) ?></p>
        </div>
        <div>
            <p><strong>Created On:</strong> <?= date('Y-m-d h:i A', strtotime($contact['created_at'])) ?></p>
            [cite_start]<p><strong>Last Updated:</strong> <?= date('Y-m-d h:i A', strtotime($contact['updated_at'])) ?></p> [cite: 178]
        </div>
    </div>
    
    <hr>

    <div class="notes-section">
        [cite_start]<h3>Notes</h3> [cite: 196]
        
        <div class="notes-list" style="max-height: 400px; overflow-y: auto;">
            <?php if (empty($notes)): ?>
                <p>No notes have been added for this contact.</p>
            <?php else: ?>
                <?php foreach ($notes as $note): ?>
                    <div class="note-item">
                        <p><?= nl2br(htmlspecialchars($note['comment'])) ?></p>
                        <p class="note-meta">
                            - <?= htmlspecialchars($note['firstname'] . ' ' . $note['lastname']) ?>
                            on <?= date('Y-m-d h:i A', strtotime($note['created_at'])) ?>
                        [cite_start]</p> [cite: 183]
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <hr>

        <h4>Add a Note</h4>
        <form id="add-note-form">
            <input type="hidden" name="contact_id" value="<?= $contact['id'] ?>">
            <label for="comment">Enter details here</label>
            <textarea id="comment" name="comment" rows="4" required></textarea>
            
            <button type="submit">Add Note</button>
            <div id="add-note-feedback" class="feedback-message"></div>
        </form>
    </div>
</div>