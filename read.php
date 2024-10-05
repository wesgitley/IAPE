<?php
require_once 'Database.php';
$db = new Database();
$conn = $db->dbConnection();

$query = "SELECT * FROM users";
$stmt = $conn->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    echo "{$user['username']} - {$user['email']}<br>";
}
?>
