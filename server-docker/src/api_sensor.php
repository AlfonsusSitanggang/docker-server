<?php
$conn = new mysqli('db-service', 'user', 'userpassword', 'app_db');
$data = json_decode(file_get_contents('php://input'), true);
if ($data) {
    $conn->query("UPDATE parking_slots SET status='{$data['status']}' WHERE slot_name='{$data['slot_name']}'");
    $conn->query("INSERT INTO sensor_logs (slot_name, event, ip_address) VALUES ('{$data['slot_name']}', 'Update {$data['status']}', '{$_SERVER['REMOTE_ADDR']}')");
    echo json_encode(["status" => "success"]);
}
?>