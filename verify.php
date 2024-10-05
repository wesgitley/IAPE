<?php
require_once 'Database.php'; // Include the Database class

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = trim($_POST['verification_code']);
    $email = trim($_POST['email']); // Retrieve email to verify against

    // Create a database connection
    $database = new Database();
    $db = $database->dbConnection();

    // Check the verification code in the database
    $query = "SELECT * FROM users WHERE email = :email AND verification_code = :verification_code";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':verification_code', $verification_code);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Code is valid, mark user as verified
        $query = "UPDATE users SET verification_code = NULL WHERE email = :email";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        echo "Email verified successfully!";
    } else {
        echo "Invalid verification code.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Email Verification</h2>
        <form action="verify.php" method="POST">
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Your Email" required>
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="verification_code" placeholder="Verification Code" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Verify</button>
        </form>
    </div>
</body>
</html>
