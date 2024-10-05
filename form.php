<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .btn-custom {
            transition: background-color 0.3s, transform 0.2s;
        }
        .btn-custom:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="submit.php" method="POST">
            <input type="text" name="username" placeholder="Username" class="form-control" required>
            <input type="email" name="email" placeholder="Email" class="form-control" required>
            <input type="password" name="password" placeholder="Password" class="form-control" required>
            
            <!-- Date of Birth Field -->
            <label for="dob">Date of Birth:</label>
            <input type="date" name="date_of_birth" class="form-control" required>

            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            
            <!-- reCAPTCHA Widget -->
            <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
            
            <button type="submit" class="btn btn-primary btn-custom">Register</button>
        </form>
    </div>
</body>
</html>
