<?php
// File: content/new_contact.php
require_once('../php_includes/config.php');
if (!isLoggedIn()) { header('Location: ../index.html'); exit; }
?>

<h2>New Contact</h2>
<div id="new-contact-container">
    <form id="new-contact-form">
        <label for="title">Title</label>
        <select id="title" name="title">
            <option value="Mr">Mr.</option>
            <option value="Mrs">Mrs.</option>
            <option value="Ms">Ms.</option>
            <option value="Dr">Dr.</option>
            <option value="Prof">Prof.</option>
        </select>
        
        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" required>
        
        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" required>
        
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>
        
        <label for="telephone">Telephone</label>
        <input type="tel" id="telephone" name="telephone">
        
        <label for="company">Company</label>
        <input type="text" id="company" name="company">

        <label for="type">Type</label>
        <select id="type" name="type" required>
            <option value="Sales Lead">Sales Lead</option>
            <option value="Support">Support</option>         </select>
        
        <label for="assigned-to">Assigned To</label>
        <select id="assigned-to" name="assigned_to" required>
            <option value="">Loading users...</option>
        </select>
        
        <button type="submit">Save</button>
        <div id="new-contact-feedback" class="feedback-message"></div>     </form>
</div>