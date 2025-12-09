<?php

require_once('../php_includes/config.php');

// 1. Authentication Check
if (!isLoggedIn()) { 
    header('Location: ../index.html'); 
    exit; 
}
$pdo = connectDB();

// 2. Get Filter and User Info
$filter = sanitize($_GET['filter'] ?? 'all');
$current_user_id = $_SESSION['user_id'];

// 3. Construct the Base SQL Query
$sql = "
    SELECT 
        c.id, c.title, c.firstname, c.lastname, c.email, c.company, c.type, 
        u.firstname AS assigned_firstname, u.lastname AS assigned_lastname
    FROM Contacts c
    JOIN Users u ON c.assigned_to = u.id
";

$conditions = [];
$params = [];

// 4. Apply Filters
if ($filter === 'sales') {
    $conditions[] = "c.type = 'Sales Lead'";
} elseif ($filter === 'support') {
    $conditions[] = "c.type = 'Support'";
} elseif ($filter === 'assigned') {
    $conditions[] = "c.assigned_to = ?";
    $params[] = $current_user_id;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(' AND ', $conditions);
}
$sql .= " ORDER BY c.created_at DESC";

// 5. Execute Query
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $contacts = $stmt->fetchAll();
} catch (Exception $e) {
    echo "<h2>Error loading contacts.</h2>";
    error_log("Dashboard query error: " . $e->getMessage());
    exit;
}

?>

<div class="dashboard-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Dashboard</h2>
    <button data-target="new_contact"><i class="fas fa-plus"></i> Add New Contact</button>
</div>

<div class="filter-controls">
    Filter By: 
    <span class="filter-btn <?= $filter === 'all' ? 'active' : '' ?>" data-filter="all">All Contacts</span>
    <span class="filter-btn <?= $filter === 'sales' ? 'active' : '' ?>" data-filter="sales">Sales Leads</span>
    <span class="filter-btn <?= $filter === 'support' ? 'active' : '' ?>" data-filter="support">Support</span>
    <span class="filter-btn <?= $filter === 'assigned' ? 'active' : '' ?>" data-filter="assigned">Assigned to me</span>
</div>

<?php if (empty($contacts)): ?>
    <p>No contacts found for this filter.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Type</th>
                <th>Assigned To</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($contacts as $contact): ?>
            <tr>
                <td>
                    <a href="#" class="view-contact-link" data-id="<?= $contact['id'] ?>">
                        <?= htmlspecialchars($contact['title'] . ' ' . $contact['firstname'] . ' ' . $contact['lastname']) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($contact['email']) ?></td>
                <td><?= htmlspecialchars($contact['company']) ?></td>
                <td class="<?= strtolower(str_replace(' ', '-', $contact['type'])) ?>"><?= htmlspecialchars($contact['type']) ?></td>
                <td><?= htmlspecialchars($contact['assigned_firstname'] . ' ' . $contact['assigned_lastname']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>