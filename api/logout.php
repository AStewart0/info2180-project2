<?php
require_once('../php_includes/config.php');
header('Content-Type: application/json');

session_unset();
session_destroy();
session_write_close();

echo json_encode(['success' => true]);
?>