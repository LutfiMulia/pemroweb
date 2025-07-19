<?php
// Include auth_check terlebih dahulu
require_once 'auth_check.php';

// Cek apakah user adalah admin
if ($_SESSION['role'] !== 'admin') {
    // Log attempt untuk keamanan
    error_log("Unauthorized admin access attempt by user ID: " . $_SESSION['user_id'] . " with role: " . $_SESSION['role']);
    
    // Redirect ke halaman yang sesuai berdasarkan role
    if ($_SESSION['role'] === 'pelapor') {
        header("Location: ../user/dashboard.php");
    } else {
        // Role tidak dikenal, logout untuk keamanan
        header("Location: ../auth/logout.php");
    }
    exit();
}
?>
