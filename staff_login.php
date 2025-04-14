<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);
$staffId = $data['staffId'];
$password = $data['password'];

$stmt = $pdo->prepare("SELECT * FROM staff WHERE staff_id = ?");
$stmt->execute([$staffId]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

if ($staff && password_verify($password, $staff['password'])) {
    session_start();
    $_SESSION['staff_id'] = $staff['id'];
    echo json_encode(['success' => true, 'staff' => $staff]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}
?>