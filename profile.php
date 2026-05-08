<?php
session_start();
require_once 'includes/db.php';

// Fetch current user details using Prepared Statements (Requirement)
$stmt = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile - City Care Hospital</title>
    <link rel="stylesheet" href="style4.css">
    <style>
        /* Internal styles for the alerts to keep the UI clean */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Update Profile</h2>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
            <div class="alert success">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert error">
                <?php 
                    if($_GET['error'] == 'emptyfields') echo "Please fill in all fields.";
                    elseif($_GET['error'] == 'usertaken') echo "Username is already taken.";
                    else echo "Something went wrong. Please try again.";
                ?>
            </div>
        <?php endif; ?>

        <form action="includes/profilehandle.php" method="POST" class="booking-card">
            <div class="input-group">
                <label>New Username</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="input-group">
                <label>New Password</label>
                <input type="password" name="password" placeholder="Enter new password" required>
            </div>
            <button type="submit" name="update_profile">Save Changes</button>
        </form>
    </div>
</body>
</html>