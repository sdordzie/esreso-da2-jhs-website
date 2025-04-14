<?php
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = [];
    
    // Validate inputs
    if (empty($_POST['first_name'])) $errors[] = "First name is required";
    if (empty($_POST['staff_id'])) $errors[] = "Staff ID is required";
    if (empty($_POST['email'])) $errors[] = "Email is required";
    if (empty($_POST['password'])) $errors[] = "Password is required";
    if ($_POST['password'] !== $_POST['confirm_password']) $errors[] = "Passwords don't match";
    
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO staff (staff_id, first_name, last_name, email, password, phone, department, ntc_number) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt->execute([
                $_POST['staff_id'],
                $_POST['first_name'],
                $_POST['last_name'],
                $_POST['email'],
                $hashed_password,
                $_POST['phone'],
                $_POST['department'],
                $_POST['ntc_number']
            ]);
            
            header("Location: staff_login.php?registered=1");
            exit();
        } catch (PDOException $e) {
            $errors[] = "Registration failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing registration page head -->
</head>
<body>
    <div class="register-container">
        <!-- Your existing registration form -->
        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <div class="error"><?= htmlspecialchars($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
