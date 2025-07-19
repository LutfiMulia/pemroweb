<?php
session_start();
echo "<h2>Test Sistem Insidentia</h2>";

// Test database connection
echo "<h3>1. Test Koneksi Database:</h3>";
try {
    $conn = new mysqli("localhost", "root", "", "inisidentia_db");
    if ($conn->connect_error) {
        echo "❌ Gagal: " . $conn->connect_error;
    } else {
        echo "✅ Database terhubung<br>";
        
        // Test tables
        $tables = ['users', 'roles', 'incidents', 'incident_categories', 'incident_priorities', 'incident_statuses', 'departments'];
        foreach ($tables as $table) {
            $result = $conn->query("SELECT COUNT(*) FROM $table");
            if ($result) {
                $count = $result->fetch_row()[0];
                echo "✅ Tabel $table: $count records<br>";
            } else {
                echo "❌ Tabel $table tidak ditemukan<br>";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

// Test session
echo "<h3>2. Test Session:</h3>";
if (isset($_SESSION['user_id'])) {
    echo "✅ User sudah login: ID = " . $_SESSION['user_id'] . ", Role = " . $_SESSION['role'];
} else {
    echo "ℹ️ Belum login (ini normal jika belum login)";
}

// Test file permissions
echo "<h3>3. Test File System:</h3>";
$files_to_check = [
    'src/includes/auth_check.php',
    'src/includes/admin_check.php', 
    'src/includes/user_check.php',
    'src/includes/config.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h3>4. Links untuk Test:</h3>";
echo "<a href='src/'>🏠 Halaman Utama</a><br>";
echo "<a href='src/auth/login.php'>🔐 Login</a><br>";
echo "<a href='src/admin/dashboard.php'>👨‍💼 Admin Dashboard</a><br>";
echo "<a href='src/user/dashboard.php'>👤 User Dashboard</a><br>";

echo "<br><br><strong>Jika semua ✅, sistem siap digunakan!</strong>";
?>
