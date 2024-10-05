<?php
require_once 'Database.php'; // Include the Database class

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Password strength validation
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password)) {
        die("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.");
    }

    // Sanitize inputs to prevent XSS and SQL Injection
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

    // Create a database connection
    $database = new Database();
    $db = $database->dbConnection();

    // Prepare an SQL statement to prevent SQL injection
    $query = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
    $stmt = $db->prepare($query);

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    // Execute the statement
    if ($stmt->execute()) {
        // Generate a verification code
        $verification_code = bin2hex(random_bytes(16)); // Generate a random code

        // Store the code in the database for later verification
        $user_id = $db->lastInsertId(); // Get the last inserted user ID
        $query = "UPDATE users SET verification_code = :verification_code WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':verification_code', $verification_code);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();

        // Send the verification email
        $subject = "Verify Your Email";
        $message = "Your verification code is: " . $verification_code;
        mail($email, $subject, $message); // Basic email function

        echo "Registration successful! A verification code has been sent to your email.";
        echo '<a href="verify.php">Verify Email</a>'; // Link to verification page
    } else {
        echo "Error registering user.";
    }
} else {
    echo "Invalid request.";
}
?>
