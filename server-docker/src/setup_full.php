<?php
$conn = new mysqli('db-service', 'user', 'userpassword', 'app_db');
if ($conn->connect_error) die("Koneksi Gagal");

// Tabel Users & Data Dummy
$conn->query("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(50), password VARCHAR(255), role VARCHAR(20))");
$conn->query("TRUNCATE TABLE users");
$conn->query("INSERT INTO users (username, password, role) VALUES ('user1','123','user'), ('admin1','123','admin'), ('manager1','123','manager')");

// Tabel Parking Slots
$conn->query("CREATE TABLE IF NOT EXISTS parking_slots (id INT AUTO_INCREMENT PRIMARY KEY, slot_name VARCHAR(10), status VARCHAR(20))");
$conn->query("TRUNCATE TABLE parking_slots");
foreach (['A1','A2','A3','B1','B2','B3'] as $s) $conn->query("INSERT INTO parking_slots (slot_name, status) VALUES ('$s', 'EMPTY')");

// Tabel Logs
$conn->query("CREATE TABLE IF NOT EXISTS sensor_logs (id INT AUTO_INCREMENT PRIMARY KEY, slot_name VARCHAR(10), event VARCHAR(50), ip_address VARCHAR(50), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

echo "<h1>🎉 Database & User Berhasil Direset!</h1>";
?>