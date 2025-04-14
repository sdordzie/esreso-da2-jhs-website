<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

// Get staff info
$stmt = $pdo->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->execute([$_SESSION['staff_id']]);
$staff = $stmt->fetch();

// Get unread messages count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM messages WHERE receiver_id = ? AND is_read = 0");
$stmt->execute([$_SESSION['staff_id']]);
$unread_messages = $stmt->fetchColumn();

// Get student count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM class_assignments ca 
                      JOIN students s ON ca.class_name = s.class
                      WHERE ca.staff_id = ?");
$stmt->execute([$_SESSION['staff_id']]);
$student_count = $stmt->fetchColumn();

// Get class count
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT class_name) FROM class_assignments WHERE staff_id = ?");
$stmt->execute([$_SESSION['staff_id']]);
$class_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing dashboard head -->
</head>
<body>
    <header>
        <img src="../images/logo.png" alt="School Logo" class="logo">
        <div class="user-info">
            <div class="user-avatar"><?= substr($staff['first_name'], 0, 1) . substr($staff['last_name'], 0, 1) ?></div>
            <button class="logout-btn" onclick="location.href='logout.php'">Logout</button>
        </div>
    </header>
    
    <div class="sidebar">
        <ul class="sidebar-nav">
            <li><a href="staff_dashboard.php" class="active">Dashboard</a></li>
            <li><a href="staff_messages.php">Messages <?= $unread_messages > 0 ? "($unread_messages)" : "" ?></a></li>
            <li><a href="staff_students.php">My Students</a></li>
            <li><a href="staff_classes.php">My Classes</a></li>
            <li><a href="staff_attendance.php">Attendance</a></li>
            <li><a href="staff_assignment.php">Assignments</a></li>
            <li><a href="staff_profile.php">My Profile</a></li>
            <li><a href="staff_settings.php">Settings</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="dashboard-header">
            <div class="welcome-message">
                <h2>Welcome, <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name']) ?></h2>
                <p><?= htmlspecialchars($staff['department']) ?> Department | Staff ID: <?= htmlspecialchars($staff['staff_id']) ?></p>
            </div>
            <button class="btn" onclick="location.href='staff_messages.php?compose=1'">New Announcement</button>
        </div>
        
        <div class="stats-container">
            <div class="stat-card">
                <div>Unread Messages</div>
                <div class="stat-number"><?= $unread_messages ?></div>
                <div><a href="staff_messages.php">View Messages</a></div>
            </div>
            <div class="stat-card">
                <div>Students</div>
                <div class="stat-number"><?= $student_count ?></div>
                <div><a href="staff_students.php">View Students</a></div>
            </div>
            <div class="stat-card">
                <div>Classes</div>
                <div class="stat-number"><?= $class_count ?></div>
                <div><a href="staff_classes.php">View Classes</a></div>
            </div>
        </div>
        
        <!-- Recent messages section -->
    </div>
</body>
</html>
