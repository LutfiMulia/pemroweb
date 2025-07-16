<?php
require_once '../includes/config.php';

$name = "Admin Baru";
$email = "admin2@inisidentia.local";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role_id = 1; // admin
$status = 'aktif';

$sql = "INSERT INTO users (name, email, password, role_id, status) 
        VALUES (?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssis", $name, $email, $password, $role_id, $status);

if ($stmt->execute()) {
    echo "✅ User admin berhasil ditambahkan!<br>Email: $email<br>Password: admin123";
} else {
    echo "❌ Gagal: " . $stmt->error;
}
