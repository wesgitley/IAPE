<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Login</title>
</head>
<body>
    <form action="login_submit.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input typetext="text" name="oneCode" placeholder="2FA Code" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
