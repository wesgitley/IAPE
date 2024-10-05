<?php
require_once 'Database.php'; // Include the Database class

session_start(); // Start the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $csrf_token = $_POST['csrf_token'];

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token.");
    }

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Check if username is alphanumeric and between 3-20 characters
    if (!preg_match('/^[a-zA-Z0-9]{3,20}$/', $username)) {
        die("Username must be 3-20 characters long and can only contain letters and numbers.");
    }

    // Check for password strength
    if (strlen($password) < 8 || 
        !preg_match('/[A-Z]/', $password) || 
        !preg_match('/[a-z]/', $password) || 
        !preg_match('/[0-9]/', $password) || 
        !preg_match('/[\W_]/', $password)) {
        die("Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one number, and one special character.");
    }

    // Sanitize inputs
    $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

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
    $query = "INSERT INTO users (username, email, password, is_verified) VALUES (:username, :email, :password, 0)";
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

// Generate CSRF token for the form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
</head>
<body>
    <form action="submit.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit">Register</button>
    </form>
</body>
</html>
