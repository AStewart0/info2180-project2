<?php
// Run this file via your terminal (php generate_hash.php)
$password = 'password123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Password: " . $password . "\n";
echo "Hashed Password (copy this to paste in Schema.sql): " . $hashed_password . "\n";
?>