<?php
require_once '../includes/user_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Cek kalau bukan user/pelapor
if ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'pelapor') {
    redirect('../auth/logout.php');
}

$user_id = $_SESSION['user_id'];
$alert = "";
$alert_class = "";

// Ambil data user saat ini
$stmt = $conn->prepare("SELECT name, email, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$current_name = $user['name'];
$current_email = $user['email'];
$current_hashed_password = $user['password'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];

    // Cek apakah email sudah digunakan oleh user lain
    $email_check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $email_check->bind_param("si", $email, $user_id);
    $email_check->execute();
    $email_check_result = $email_check->get_result();

    if ($email_check_result->num_rows > 0) {
        $alert = "Email sudah digunakan oleh pengguna lain.";
        $alert_class = "danger";
    } else {
        // Cek jika password ingin diubah
        if (!empty($old_password) && !empty($new_password)) {
            if (password_verify($old_password, $current_hashed_password)) {
                $hashed_new_pw = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $hashed_new_pw, $user_id);
            } else {
                $alert = "Password lama salah.";
                $alert_class = "danger";
            }
        }

        // Jika tidak ingin ubah password
        if (empty($alert)) {
            if (empty($old_password) && empty($new_password)) {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $email, $user_id);
            }

            // Jalankan update
            if (isset($stmt)) {
                if ($stmt->execute()) {
                    $_SESSION['name'] = $name;
                    $alert = "Perubahan berhasil disimpan.";
                    $alert_class = "success";
                } else {
                    $alert = "Gagal menyimpan perubahan.";
                    $alert_class = "danger";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pengaturan Akun - Insidentia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Pastikan path ini benar -->
    <style>
        body {
            background-color: #fff0f5; /* Warna latar belakang konsisten */
        }
        .sidebar {
            background: linear-gradient(135deg, #ffb6c1, #ff69b4); /* Gradien sidebar konsisten */
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
            color: #ff69b4; /* Warna judul bagian konsisten */
            font-weight: bold;
        }
        /* Gaya untuk form pengaturan akun */
        .settings-form-card {
            background-color: white;
            border-radius: 15px;
            padding: 30px; /* Padding lebih besar untuk form */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            border: 1px solid #eee; /* Border tipis */
            max-width: 700px; /* Batasi lebar form */
            margin: 0 auto; /* Tengah form */
        }
        .form-control { /* Styling konsisten untuk input */
            border-radius: 10px;
            border: 1px solid #ddd; /* Light gray border */
            background-color: #f8f9fa; /* Very light gray background */
            color: #333; /* Dark text color */
            padding-left: 40px !important; /* Ruang untuk ikon */
        }
        .form-control:focus { /* Focus styles */
            border-color: #ff69b4; /* Pink border on focus */
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.25); /* Pink shadow on focus */
            background-color: white; /* White background on focus */
            color: #333;
        }
        .form-label {
            font-weight: bold;
            color: #555; /* Darker text for labels */
            margin-bottom: 8px; /* Sedikit spasi di bawah label */
        }
        .input-icon-wrapper {
            position: relative;
            margin-bottom: 1rem; /* Spasi antar field */
        }
        .input-icon-wrapper .bi {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af; /* Gray icon color */
            pointer-events: none; /* Pastikan ikon tidak mengganggu klik input */
        }
        .btn-pink-custom { /* Custom button style */
            background-color: #ff69b4;
            color: white;
            border: none;
            font-weight: bold;
            transition: 0.3s ease;
            border-radius: 8px; /* Rounded corners for buttons */
            padding: 12px 25px; /* Lebih banyak padding */
            font-size: 1.1rem;
        }
        .btn-pink-custom:hover {
            background-color: #ff1493;
            color: white;
            transform: translateY(-2px);
        }
        .text-muted-custom {
            color: #888; /* Slightly darker muted text */
            font-size: 0.9em;
            margin-top: -0.5rem; /* Adjust spacing */
            margin-bottom: 1rem;
        }
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
                <a href="report_incident.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i> Laporkan Insiden Baru</a>
                <a href="my_incidents.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> Riwayat Laporan Saya</a>
                <a href="settings.php" class="nav-link fw-bold active"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center mb-4 section-title"><i class="bi bi-gear me-2"></i>Pengaturan Akun</h2>

            <?php if (!empty($alert)): ?>
                <div class="alert alert-<?= $alert_class ?> text-center mb-4" role="alert">
                    <?= $alert ?>
                </div>
            <?php endif; ?>

            <div class="settings-form-card">
                <form method="post">
                    <div class="mb-3 input-icon-wrapper">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <i class="bi bi-person-fill text-xl"></i>
                        <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($current_name) ?>" required>
                    </div>
                    <div class="mb-3 input-icon-wrapper">
                        <label for="email" class="form-label">Email / Username</label>
                        <i class="bi bi-envelope-fill text-xl"></i>
                        <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($current_email) ?>" required>
                    </div>
                    <hr class="my-4">
                    <p class="text-muted-custom text-center">Kosongkan bidang password jika tidak ingin mengganti password Anda.</p>
                    <div class="mb-3 input-icon-wrapper">
                        <label for="old_password" class="form-label">Password Lama</label>
                        <i class="bi bi-lock-fill text-xl"></i>
                        <input type="password" name="old_password" id="old_password" class="form-control" placeholder="Masukkan password lama">
                    </div>
                    <div class="mb-4 input-icon-wrapper">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <i class="bi bi-key-fill text-xl"></i>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Masukkan password baru">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-pink-custom">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
