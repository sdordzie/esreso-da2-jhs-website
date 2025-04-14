<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $staff_id = $_POST['staff_id'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['staff_id'] = $user['id'];
        $_SESSION['staff_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['department'] = $user['department'];
        header("Location: staff_dashboard.php");
        exit();
    } else {
        $error = "Invalid staff ID or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - ESRESO D/A 2 JHS</title>
    <style>
        /* Your existing login styles */
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../images/logo.png" alt="School Logo">
            <h2>Staff Login Portal</h2>
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
        </div>
        <form method="POST">
            <div class="form-group">
                <label for="staffId">Staff ID</label>
                <input type="text" id="staffId" name="staff_id" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="login-footer">
            <p>New staff? <a href="staff_register.php">Register here</a></p>
            <p><a href="forgot_password.php">Forgot password?</a></p>
        </div>
    </div>
</body>
</html>
