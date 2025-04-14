<?php
header("Content-Type: application/json");
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['staff_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$staffId = $_SESSION['staff_id'];

// Get received messages
$stmt = $pdo->prepare("SELECT m.*, s.first_name, s.last_name 
                      FROM messages m 
                      JOIN staff s ON m.sender_id = s.id 
                      WHERE m.receiver_id = ?
                      ORDER BY m.created_at DESC");
$stmt->execute([$staffId]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get student messages
$stmt = $pdo->prepare("SELECT sm.*, st.first_name, st.last_name, st.class 
                      FROM student_messages sm
                      JOIN students st ON sm.student_id = st.id
                      WHERE sm.staff_id = ?
                      ORDER BY sm.created_at DESC");
$stmt->execute([$staffId]);
$studentMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'messages' => $messages,
    'studentMessages' => $studentMessages
]);
?>