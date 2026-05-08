<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>City Care Hospital - Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="login-wrapper">

    <form action="includes/reghandler.php" method="POST" class="login-form">

        <h2>Create Account</h2>

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button type="submit" name="register_btn">
            Register
        </button>

        <p>
            <a href="index.php">
                Already have an account? Login
            </a>
        </p>

    </form>

</div>

</body>
</html>