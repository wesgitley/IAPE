<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $oneCode = trim($_POST['oneCode']);

    if (empty($username) || empty($password) || empty($oneCode)) {
        die("All fields are required.");
    }
    // Continue with authentication...
}
?>
