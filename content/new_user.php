<?php
// File: content/new_user.php
require_once('../php_includes/config.php');
if (!isLoggedIn() || !isAdmin()) {
    echo "<h2>Access Denied</h2><p>You must be an administrator to view this page.</p>";
    exit;
}
?>

<h2>New User</h2>
<div id="new-user-container">
    <form id="new-user-form">
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" required>
        
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>
        <small>Password must be at least 8 chars, contain one letter, one capital letter, and one number. </small>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="Member">Member</option>
            <option value="Admin">Admin</option>         </select>
        
        <button type="submit">Save</button>
        <div id="new-user-feedback" class="feedback-message"></div>     </form>
</div>