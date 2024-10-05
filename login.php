<?php
require_once 'Database.php'; // Include the Database class

session_start(); // Start the session

// Generate CSRF token for the form
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); // Redirect to dashboard or home page
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $csrf_token = $_POST['csrf_token'];

    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        die("Invalid CSRF token.");
    }

    // Validate input
    if (empty($email) || empty($password)) {
        die("Email and password are required.");
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Sanitize email input
    $email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');

    // Create a database connection
    $database = new Database();
    $db = $database->dbConnection();

    // Prepare an SQL statement to check credentials
    $query = "SELECT * FROM users WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Check if a user exists with the provided email
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch user data

        // Check if user is verified
        if (!$user['is_verified']) {
            die("Please verify your email before logging in.");
        }

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            echo "Login successful! Welcome, " . htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') . ".";
            // Redirect to a secure page (dashboard, etc.)
            header("Location: dashboard.php");
            exit();
        } else {
            // Password is incorrect
            echo "Invalid password. Please try again.";
        }
    } else {
        // No user found with that email
        echo "No user found with that email address.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>
</head>
<body>
    <form action="login.php" method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <button type="submit">Login</button>
    </form>
</body>
</html>
