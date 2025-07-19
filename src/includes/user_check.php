<?php
// Include auth_check terlebih dahulu
require_once 'auth_check.php';

// Cek apakah user adalah pelapor/user
if ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'pelapor') {
    // Log attempt untuk keamanan
    error_log("Unauthorized user access attempt by user ID: " . $_SESSION['user_id'] . " with role: " . $_SESSION['role']);
    
    // Redirect ke halaman yang sesuai berdasarkan role
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        // Role tidak dikenal, logout untuk keamanan
        header("Location: ../auth/logout.php");
    }
    exit();
}
?>
