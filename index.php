<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Care Hospital - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-wrapper">
        <form action="includes/auth.php" method="POST" class="login-form">
            <h2>Hospital Portal Login</h2>
            <div class="input-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login_btn">Sign In</button>
            
            <div class="register-link">
                <p>Not yet a member? <a href="register.php">Register Today</a></p>
            </div>
        </form>
    </div>
</body>
</html>