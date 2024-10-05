<?php
require_once 'Database.php'; // Include the Database class

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $date_of_birth = $_POST['date_of_birth']; // New DOB field
    $csrf_token = $_POST['csrf_token'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token.");
    }

    // Validate reCAPTCHA
    $secret_key = 'YOUR_SECRET_KEY'; // Replace with your secret key
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secret_key&response=$recaptcha_response");
    $response_keys = json_decode($response, true);
    if (!$response_keys["success"]) {
        die("Please complete the CAPTCHA.");
    }

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($date_of_birth)) {
        die("All fields are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // ... (rest of your validation code)

    // Sanitize inputs
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
    $date_of_birth = htmlspecialchars($date_of_birth, ENT_QUOTES, 'UTF-8'); // Sanitize date of birth

    // Create a database connection
    $database = new Database();
    $db = $database->dbConnection();

    // Check if email or username already exists
    $query = "SELECT * FROM users WHERE email = :email OR username = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        die("Email or username already exists.");
    }

    // Prepare an SQL statement to insert the new user
    $query = "INSERT INTO users (username, email, password, date_of_birth, is_verified) VALUES (:username, :email, :password, :date_of_birth, 0)";
    $stmt = $db->prepare($query);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':date_of_birth', $date_of_birth); // Bind date of birth

    // Execute the statement
    if ($stmt->execute()) {
        // Generate a verification code
        // ... (rest of your user registration logic)
    } else {
        echo "Error registering user.";
    }
} else {
    echo "Invalid request.";
}
