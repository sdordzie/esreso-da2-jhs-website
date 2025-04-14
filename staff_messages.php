<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['staff_id'])) {
    header("Location: staff_login.php");
    exit();
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $receiver_id = $_POST['receiver_id'];
    $subject = $_POST['subject'];
    $content = $_POST['content'];
    
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, content) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['staff_id'], $receiver_id, $subject, $content]);
    
    header("Location: staff_messages.php?sent=1");
    exit();
}

// Handle message reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_message'])) {
    $original_id = $_POST['original_id'];
    $reply_content = $_POST['reply_content'];
    
    // Get original message
    $stmt = $pdo->prepare("SELECT * FROM messages WHERE id = ?");
    $stmt->execute([$original_id]);
    $original = $stmt->fetch();
    
    if ($original) {
        $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, subject, content) 
                              VALUES (?, ?, ?, ?)");
        $subject = "Re: " . $original['subject'];
        $stmt->execute([
            $_SESSION['staff_id'],
            $original['sender_id'],
            $subject,
            $reply_content
        ]);
        
        // Mark original as read
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$original_id]);
        
        header("Location: staff_messages.php?replied=1");
        exit();
    }
}

// Get all messages for this staff member
$stmt = $pdo->prepare("SELECT m.*, s.first_name, s.last_name 
                      FROM messages m
                      JOIN staff s ON m.sender_id = s.id
                      WHERE m.receiver_id = ?
                      ORDER BY m.created_at DESC");
$stmt->execute([$_SESSION['staff_id']]);
$messages = $stmt->fetchAll();

// Get staff list for composing messages
$stmt = $pdo->prepare("SELECT id, first_name, last_name, department FROM staff WHERE id != ?");
$stmt->execute([$_SESSION['staff_id']]);
$staff_list = $stmt->fetchAll();

// Get selected message
$selected_message = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("SELECT m.*, s.first_name, s.last_name 
                          FROM messages m
                          JOIN staff s ON m.sender_id = s.id
                          WHERE m.id = ?");
    $stmt->execute([$_GET['view']]);
    $selected_message = $stmt->fetch();
    
    // Mark as read
    if ($selected_message && !$selected_message['is_read']) {
        $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$_GET['view']]);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your existing messages head -->
</head>
<body>
    <!-- Your existing messages body -->
    <?php if (isset($_GET['compose'])): ?>
        <div class="compose-message">
            <h3>Compose New Message</h3>
            <form method="POST">
                <div class="form-group">
                    <label for="receiver">Recipient:</label>
                    <select id="receiver" name="receiver_id" required>
                        <option value="">Select recipient...</option>
                        <?php foreach ($staff_list as $staff): ?>
                            <option value="<?= $staff['id'] ?>">
                                <?= htmlspecialchars($staff['first_name'] . ' ' . $staff['last_name'] . ' (' . $staff['department'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="content">Message:</label>
                    <textarea id="content" name="content" required></textarea>
                </div>
                <button type="submit" name="send_message" class="btn">Send Message</button>
            </form>
        </div>
    <?php endif; ?>
    
    <?php if ($selected_message): ?>
        <div class="message-detail">
            <div class="message-detail-header">
                <div class="message-detail-sender">
                    From: <?= htmlspecialchars($selected_message['first_name'] . ' ' . $selected_message['last_name']) ?>
                </div>
                <div class="message-detail-subject">
                    Subject: <?= htmlspecialchars($selected_message['subject']) ?>
                </div>
                <div class="message-detail-date">
                    <?= date('M j, Y g:i A', strtotime($selected_message['created_at'])) ?>
                </div>
            </div>
            <div class="message-detail-content">
                <?= nl2br(htmlspecialchars($selected_message['content'])) ?>
            </div>
            <div class="reply-form">
                <h4>Reply</h4>
                <form method="POST">
                    <input type="hidden" name="original_id" value="<?= $selected_message['id'] ?>">
                    <div class="form-group">
                        <textarea name="reply_content" required></textarea>
                    </div>
                    <button type="submit" name="reply_message" class="btn">Send Reply</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
