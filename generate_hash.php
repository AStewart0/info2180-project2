<?php
// Run this file via your terminal (e.g., php db_setup/generate_hash.php)
$password = 'password123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
echo "Password: " . $password . "\n";
echo "Hashed Password (COPY THIS!): " . $hashed_password . "\n";
?>