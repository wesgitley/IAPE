<?php
require_once 'Database.php';
$db = new Database();
$conn = $db->dbConnection();
if ($conn) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed.";
}
?>
