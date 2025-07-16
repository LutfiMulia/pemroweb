<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SESSION['role'] !== 'pelapor') {
    redirect('../auth/logout.php');
}

$success = "";
$error = "";

// Ambil pilihan dropdown dari DB
$categories = $conn->query("SELECT id, name FROM incident_categories");
$priorities = $conn->query("SELECT id, name FROM incident_priorities");
$departments = $conn->query("SELECT id, name FROM departments");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $category = $_POST['category_id'];
    $priority = $_POST['priority_id'];
    $department = $_POST['department_id'];
    $user_id = $_SESSION['user_id'];

    if ($title && $category && $priority && $department) {
        $stmt = $conn->prepare("INSERT INTO incidents (title, description, reported_by, category_id, priority_id, status_id, department_id) 
                                VALUES (?, ?, ?, ?, ?, 1, ?)");
        $stmt->bind_param("ssiiii", $title, $description, $user_id, $category, $priority, $department);
        if ($stmt->execute()) {
            $success = "Laporan berhasil dikirim!";
        } else {
            $error = "Gagal menyimpan laporan.";
        }
    } else {
        $error = "Semua bidang wajib diisi.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporkan Insiden - Insidentia</title>
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
        /* Gaya untuk form laporan insiden */
        .report-form-card {
            background-color: white;
            border-radius: 15px;
            padding: 30px; /* Padding lebih besar untuk form */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            border: 1px solid #eee; /* Border tipis */
            max-width: 700px; /* Batasi lebar form */
            margin: 0 auto; /* Tengah form */
        }
        .form-control, .form-select, .form-control-textarea { /* Styling konsisten untuk input, select, textarea */
            border-radius: 10px;
            border: 1px solid #ddd; /* Light gray border */
            background-color: #f8f9fa; /* Very light gray background */
            color: #333; /* Dark text color */
            padding-left: 40px !important; /* Ruang untuk ikon */
        }
        .form-control:focus, .form-select:focus, .form-control-textarea:focus { /* Focus styles */
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
        .input-icon-wrapper textarea.form-control {
            padding-top: 12px; /* Adjust padding for textarea with icon */
            min-height: 100px; /* Min height for textarea */
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
                <a href="report_incident.php" class="nav-link fw-bold active"><i class="bi bi-plus-circle me-2"></i> Laporkan Insiden Baru</a>
                <a href="my_incidents.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> Riwayat Laporan Saya</a>
                <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center mb-4 section-title"><i class="bi bi-plus-circle me-2"></i>Laporkan Insiden Baru</h2>

            <?php if ($success): ?>
                <div class="alert alert-success text-center mb-4" role="alert">
                    <?= $success ?>
                </div>
            <?php elseif ($error): ?>
                <div class="alert alert-danger text-center mb-4" role="alert">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="post" class="report-form-card">
                <div class="mb-3 input-icon-wrapper">
                    <label for="title" class="form-label">Judul Insiden</label>
                    <i class="bi bi-pencil-square text-xl"></i>
                    <input type="text" class="form-control" name="title" id="title" required placeholder="Contoh: Lampu Rusak di Lantai 2">
                </div>

                <div class="mb-3 input-icon-wrapper">
                    <label for="description" class="form-label">Deskripsi</label>
                    <i class="bi bi-file-earmark-text text-xl" style="top: 20px; transform: translateY(0);"></i> <!-- Adjusted icon position for textarea -->
                    <textarea class="form-control form-control-textarea" name="description" id="description" rows="4" placeholder="Jelaskan detail insiden secara singkat dan jelas."></textarea>
                </div>

                <div class="mb-3 input-icon-wrapper">
                    <label class="form-label">Kategori</label>
                    <i class="bi bi-tags-fill text-xl"></i>
                    <select class="form-select" name="category_id" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3 input-icon-wrapper">
                    <label class="form-label">Prioritas</label>
                    <i class="bi bi-exclamation-triangle-fill text-xl"></i>
                    <select class="form-select" name="priority_id" required>
                        <option value="">-- Pilih Prioritas --</option>
                        <?php while ($pri = $priorities->fetch_assoc()): ?>
                            <option value="<?= $pri['id'] ?>"><?= htmlspecialchars($pri['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-4 input-icon-wrapper">
                    <label class="form-label">Lokasi/Departemen</label>
                    <i class="bi bi-geo-alt-fill text-xl"></i>
                    <select class="form-select" name="department_id" required>
                        <option value="">-- Pilih Departemen --</option>
                        <?php while ($dep = $departments->fetch_assoc()): ?>
                            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-pink-custom">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
