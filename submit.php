<?php
require_once 'Database.php';
$db = new Database();
$conn = $db->dbConnection();

$query = "INSERT INTO users (username, password, email) VALUES (:username, :password, :email)";
$stmt = $conn->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $hashed_password);
$stmt->bindParam(':email', $email);

if ($stmt->execute()) {
    echo "User registered successfully!";
} else {
    echo "User registration failed.";
}
?>
