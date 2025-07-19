<?php
// Mulai session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    // Hapus semua data session untuk keamanan
    session_unset();
    session_destroy();
    
    // Redirect ke halaman login
    header("Location: ../auth/login.php");
    exit();
}

// Regenerate session ID untuk keamanan (setiap 5 menit)
if (!isset($_SESSION['last_regeneration'])) {
    $_SESSION['last_regeneration'] = time();
} elseif (time() - $_SESSION['last_regeneration'] > 300) {
    session_regenerate_id(true);
    $_SESSION['last_regeneration'] = time();
}

// Set timezone jika belum di-set
if (!isset($_SESSION['timezone_set'])) {
    date_default_timezone_set('Asia/Jakarta');
    $_SESSION['timezone_set'] = true;
}
