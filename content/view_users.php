<?php
// File: content/view_users.php
require_once('../php_includes/config.php');

// Only Admin should be able to view this list 
if (!isLoggedIn() || !isAdmin()) {
    echo "<h2>Access Denied</h2><p>You must be an administrator to view this page.</p>";
    exit;
}
$pdo = connectDB();

try {
    $stmt = $pdo->query("SELECT firstname, lastname, email, role, created_at FROM Users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (Exception $e) {
    echo "<h2>Error loading users.</h2>";
    error_log("Users query error: " . $e->getMessage());
    exit;
}

?>
<div class="users-header" style="display: flex; justify-content: space-between; align-items: center;">
    <h2>Users</h2>
    <button data-target="new_user"><i class="fas fa-user-plus"></i> Add User</button> </div>

<?php if (empty($users)): ?>
    <p>No users found in the system.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></td>
                <td><?= htmlspecialchars($user['email']); ?></td>
                <td><?= htmlspecialchars($user['role']); ?></td>
                <td><?= date('Y-m-d', strtotime($user['created_at'])); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>