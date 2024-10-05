<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    
    
    echo "Registration successful!";
} else {
    echo "Invalid request.";
}
?>
