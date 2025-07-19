<?php
require_once '../includes/config.php';

// Data default settings
$default_settings = [
    'default_incident_status' => 'Terbuka',
    'max_incidents_per_day' => '50',
    'registration_enabled' => 'true',
    'notify_admin_on_new_incident' => 'true'
];

echo "<h2>Inisialisasi Default Settings</h2>";

// Cek dan tambahkan setting yang belum ada
foreach ($default_settings as $key => $value) {
    $check = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
    $check->bind_param("s", $key);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows == 0) {
        // Setting belum ada, tambahkan
        $insert = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $insert->bind_param("ss", $key, $value);
        if ($insert->execute()) {
            echo "✅ Menambahkan setting: $key = $value<br>";
        } else {
            echo "❌ Gagal menambahkan setting: $key<br>";
        }
    } else {
        echo "ℹ️ Setting sudah ada: $key<br>";
    }
}

echo "<h3>Current Settings:</h3>";
$query = $conn->query("SELECT * FROM settings ORDER BY setting_key");
if ($query) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    while ($row = $query->fetch_assoc()) {
        echo "<tr><td>{$row['setting_key']}</td><td>{$row['setting_value']}</td></tr>";
    }
    echo "</table>";
}

echo "<br><a href='../admin/settings.php'>← Kembali ke Settings</a>";
?>
