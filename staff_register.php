<?php
header("Content-Type: application/json");
require_once 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

$hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("INSERT INTO staff (staff_id, first_name, last_name, email, password, phone, department, ntc_number) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([
    $data['staffId'],
    $data['firstName'],
    $data['lastName'],
    $data['email'],
    $hashedPassword,
    $data['phone'],
    $data['department'],
    $data['ntcNumber']
]);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed']);
}
?>