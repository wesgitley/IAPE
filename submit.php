<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    
    // Process the data (e.g., validate, hash password, save to database)
    // For example:
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Hash the password and store the user data in the database here
    
    echo "Registration successful!";
} else {
    echo "Invalid request.";
}
?>
