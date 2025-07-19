<?php
require_once '../includes/admin_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Ambil semua setting dari database
$settings = [];
$default_settings = [
    'default_incident_status' => 'Terbuka',
    'max_incidents_per_day' => '50', 
    'registration_enabled' => 'true',
    'notify_admin_on_new_incident' => 'true'
];

$query = $conn->query("SELECT * FROM settings");
if ($query) {
    while ($row = $query->fetch_assoc()) {
        $settings[$row['setting_key']] = $row;
    }
}

// Function untuk mendapatkan setting value dengan default
function getSetting($key, $settings, $default_settings) {
    if (isset($settings[$key]['setting_value'])) {
        return $settings[$key]['setting_value'];
    }
    return $default_settings[$key] ?? '';
}

// Variable untuk notifikasi
$success_message = '';

// Proses update setting
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated_count = 0;
    foreach ($_POST as $key => $value) {
        // Cek apakah setting sudah ada
        $check = $conn->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $check->bind_param("s", $key);
        $check->execute();
        $result = $check->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing
            $stmt = $conn->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->bind_param("ss", $value, $key);
        } else {
            // Insert new
            $stmt = $conn->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->bind_param("ss", $key, $value);
        }
        if ($stmt->execute()) {
            $updated_count++;
        }
    }
    
    if ($updated_count > 0) {
        $success_message = "Pengaturan berhasil disimpan! ($updated_count setting diupdate)";
    }
    
    // Refresh data settings
    $settings = [];
    $query = $conn->query("SELECT * FROM settings");
    if ($query) {
        while ($row = $query->fetch_assoc()) {
            $settings[$row['setting_key']] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Sistem - Insidentia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background-color: #fff0f5; /* Consistent background color */
        }
        .sidebar {
            background: linear-gradient(135deg, #ffb6c1, #ff69b4); /* Consistent sidebar gradient */
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: white;
            margin-bottom: 10px;
        }
        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
        }
        .sidebar .nav-link.active,
        .sidebar .nav-link.fw-bold {
            background-color: rgba(255, 255, 255, 0.2);
        }
        .section-title {
            color: #ff69b4; /* Consistent section title color */
            font-weight: bold;
        }
        /* Enhanced styles for the settings form card */
        .settings-card { /* Custom class for the form card in settings */
            background-color: white; /* White background for a clean look */
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Consistent shadow */
            border: 1px solid #eee; /* Border tipis */
            transition: transform 0.2s ease-in-out; /* Added hover effect */
        }
        .settings-card:hover {
            transform: translateY(-5px); /* Lift card on hover */
        }
        .form-control, .form-select { /* Consistent styling for form inputs and selects */
            border-radius: 10px;
            border: 1px solid #ddd; /* Light gray border */
            background-color: #f8f9fa; /* Very light gray background */
            color: #333; /* Dark text color */
        }
        .form-control::placeholder { /* Placeholder color */
            color: #888;
        }
        .form-control:focus, .form-select:focus { /* Focus styles */
            border-color: #ff69b4; /* Pink border on focus */
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.25); /* Pink shadow on focus */
            background-color: white; /* White background on focus */
            color: #333;
        }
        .form-label {
            font-weight: bold;
            color: #555; /* Darker text for labels */
        }
        .btn-pink { /* Consistent button style */
            background-color: #ff69b4;
            color: white;
            border: none;
            font-weight: bold;
            transition: 0.3s ease;
            border-radius: 8px; /* Rounded corners for buttons */
            padding: 10px 20px; /* More padding */
        }
        .btn-pink:hover {
            background-color: #ff1493;
            color: white;
            transform: translateY(-2px); /* Lift button on hover */
        }
        /* No table-container or table-specific styles needed for this page */
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- SIDEBAR -->
        <div class="col-md-3 sidebar p-3">
            <div class="text-center mb-4">
                <img src="../assets/img/logo.png" alt="Insidentia" width="100" class="mb-2 rounded">
                <h4 class="text-white fw-bold mt-2">Insidentia</h4>
            </div>

            <nav class="nav flex-column">
                <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door me-2"></i> Dashboard</a>
                <a href="users.php" class="nav-link"><i class="bi bi-people me-2"></i> Pengguna</a>
                <a href="categories.php" class="nav-link"><i class="bi bi-tags me-2"></i> Kategori</a>
                <a href="priorities.php" class="nav-link"><i class="bi bi-exclamation-triangle me-2"></i> Prioritas</a>
                <a href="statuses.php" class="nav-link"><i class="bi bi-check2-circle me-2"></i> Status</a>
                <a href="locations.php" class="nav-link"><i class="bi bi-geo-alt me-2"></i> Lokasi</a>
                <a href="incidents.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> Insiden</a>
                <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line me-2"></i> Laporan</a>
                <a href="settings.php" class="nav-link fw-bold active"><i class="bi bi-gear me-2"></i> Pengaturan</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="mb-4 text-center section-title"><i class="bi bi-gear me-2"></i>Pengaturan Sistem</h2>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= $success_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form method="post" class="settings-card">
                <div class="mb-3">
                    <label class="form-label">Status Default Insiden</label>
                    <input type="text" name="default_incident_status" class="form-control"
                        value="<?= htmlspecialchars(getSetting('default_incident_status', $settings, $default_settings)) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Max Insiden per Hari</label>
                    <input type="number" name="max_incidents_per_day" class="form-control"
                        value="<?= htmlspecialchars(getSetting('max_incidents_per_day', $settings, $default_settings)) ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Pendaftaran Diperbolehkan</label>
                    <select name="registration_enabled" class="form-select">
                        <option value="true" <?= getSetting('registration_enabled', $settings, $default_settings) === 'true' ? 'selected' : '' ?>>Ya</option>
                        <option value="false" <?= getSetting('registration_enabled', $settings, $default_settings) === 'false' ? 'selected' : '' ?>>Tidak</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Notifikasi ke Admin Saat Ada Insiden Baru</label>
                    <select name="notify_admin_on_new_incident" class="form-select">
                        <option value="true" <?= getSetting('notify_admin_on_new_incident', $settings, $default_settings) === 'true' ? 'selected' : '' ?>>Aktif</option>
                        <option value="false" <?= getSetting('notify_admin_on_new_incident', $settings, $default_settings) === 'false' ? 'selected' : '' ?>>Nonaktif</option>
                    </select>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-pink px-4">Simpan Pengaturan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
