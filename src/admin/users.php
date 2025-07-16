<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Tambah Pengguna
if (isset($_POST['add_user'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = (int)$_POST['role_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $name, $email, $password, $role_id, $status);
    $stmt->execute();
    redirect('users.php');
}

// Hapus Pengguna
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM users WHERE id = $id");
    redirect('users.php');
}

// Ambil data pengguna untuk edit jika ada parameter edit
$edit_user = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM users WHERE id = $id LIMIT 1");
    $edit_user = $res->fetch_assoc();
}

// Update pengguna
if (isset($_POST['update_user'])) {
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $role_id = (int)$_POST['role_id'];
    $status = $_POST['status'];

    $sql = "UPDATE users SET name=?, email=?, role_id=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $name, $email, $role_id, $status, $id);
    $stmt->execute();
    redirect('users.php');
}

// Ambil semua user dari database
$result = $conn->query("SELECT users.*, roles.name AS role_name FROM users JOIN roles ON users.role_id = roles.id ORDER BY users.created_at DESC");
$roles = $conn->query("SELECT * FROM roles");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Pengguna - Insidentia</title>
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
        /* Enhanced styles for form and table containers */
        .card-form {
            background: linear-gradient(to right, #ff9a9e, #fad0c4); /* Gradien pink yang cantik */
            color: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan lebih jelas */
            transition: transform 0.2s ease-in-out;
            border: 1px solid #eee; /* Border tipis */
        }
        .card-form:hover {
            transform: translateY(-5px); /* Efek angkat saat hover */
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.5); /* Border input lebih transparan */
            background-color: rgba(255, 255, 255, 0.2); /* Latar belakang input transparan */
            color: white; /* Warna teks input */
        }
        .form-control::placeholder { /* Placeholder color */
            color: rgba(249, 99, 187, 0.7);
        }
        .form-control:focus, .form-select:focus {
            border-color: #ff1493; /* Border fokus lebih gelap pink */
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.4); /* Bayangan fokus lebih kuat */
            background-color: rgba(255, 255, 255, 0.3); /* Latar belakang input fokus */
            color: white;
        }
        .form-label {
            color: white; /* Label warna putih */
            font-weight: bold;
        }
        .table-container {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan lebih jelas */
            border: 1px solid #eee; /* Border tipis */
        }
        .btn-pink {
            background-color: #ff69b4;
            color: white;
            border: none;
            font-weight: bold;
            transition: 0.3s ease;
            border-radius: 8px; /* Rounded corners for buttons */
            padding: 10px 20px; /* Lebih banyak padding */
        }
        .btn-pink:hover {
            background-color: #ff1493;
            color: white;
            transform: translateY(-2px); /* Efek angkat saat hover */
        }
        .table-hover tbody tr:hover {
            background-color: #fff5f9;
        }
        .action-btn i {
            font-size: 1rem;
        }
        /* Specific styling for user table roles and names */
        .table-container .table tbody tr td:nth-child(1) { /* Targets the first <td> (Name) */
            font-weight: bold;
            color: #ff69b4; /* Warna pink untuk nama pengguna */
        }
        .table-container .table tbody tr td:nth-child(3) .badge { /* Targets the badge within the third <td> (Role) */
            background-color: #ffb6c1 !important; /* Lighter pink for the role badge */
            color: #333;
            font-weight: normal;
        }
        .table-container .table tbody tr td .badge.bg-success {
            background-color: #28a745 !important; /* Default Bootstrap success green */
            color: white !important;
        }
        .table-container .table tbody tr td .badge.bg-secondary {
            background-color: #6c757d !important; /* Default Bootstrap secondary grey */
            color: white !important;
        }
        .table-container .table thead.table-danger {
            background-color: #ffb6c1; /* Warna header tabel konsisten */
            color: white;
        }
        .table-container .table thead th {
            padding: 12px 8px; /* Padding lebih besar di header tabel */
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
                <a href="users.php" class="nav-link fw-bold active"><i class="bi bi-people me-2"></i> Pengguna</a>
                <a href="categories.php" class="nav-link"><i class="bi bi-tags me-2"></i> Kategori</a>
                <a href="priorities.php" class="nav-link"><i class="bi bi-exclamation-triangle me-2"></i> Prioritas</a>
                <a href="statuses.php" class="nav-link"><i class="bi bi-check2-circle me-2"></i> Status</a>
                <a href="locations.php" class="nav-link"><i class="bi bi-geo-alt me-2"></i> Lokasi</a>
                <a href="incidents.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> Insiden</a>
                <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line me-2"></i> Laporan</a>
                <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center mb-4 section-title"><i class="bi bi-people me-2"></i>Manajemen Pengguna</h2>

            <div class="row g-4">
                <!-- Form Section -->
                <div class="col-md-4">
                    <div class="card-form shadow">
                        <h5 class="text-white"><?= $edit_user ? 'Edit Pengguna' : 'Tambah Pengguna' ?></h5>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $edit_user['id'] ?? '' ?>">
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input type="text" name="name" class="form-control" required value="<?= $edit_user['name'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" required value="<?= $edit_user['email'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Peran</label>
                                <select name="role_id" class="form-select" required>
                                    <?php
                                    // Reset roles result pointer to the beginning if it was consumed by the form select
                                    if ($roles->num_rows > 0) {
                                        $roles->data_seek(0);
                                    }
                                    while($role = $roles->fetch_assoc()): ?>
                                        <option value="<?= $role['id'] ?>" <?= (isset($edit_user) && $edit_user['role_id'] == $role['id']) ? 'selected' : '' ?>><?= htmlspecialchars($role['name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="aktif" <?= (isset($edit_user) && $edit_user['status'] == 'aktif') ? 'selected' : '' ?>>Aktif</option>
                                    <option value="nonaktif" <?= (isset($edit_user) && $edit_user['status'] == 'nonaktif') ? 'selected' : '' ?>>Nonaktif</option>
                                </select>
                            </div>
                            <?php if (!$edit_user): ?>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                            <?php endif; ?>
                            <button type="submit" name="<?= $edit_user ? 'update_user' : 'add_user' ?>" class="btn btn-pink w-100">
                                <?= $edit_user ? 'Update' : 'Tambah' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="col-md-8">
                    <div class="table-container">
                        <h5 class="fw-bold text-danger mb-3"><i class="bi bi-list-ul me-2"></i>Daftar Pengguna</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-danger text-center">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Email</th>
                                        <th>Peran</th>
                                        <th>Status</th>
                                        <th>Dibuat</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Reset roles result pointer to the beginning if it was consumed by the form select
                                    if ($roles->num_rows > 0) {
                                        $roles->data_seek(0); // Ensure roles are available for the table if needed
                                    }
                                    while($user = $result->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><span class="badge bg-pink text-dark"><?= htmlspecialchars($user['role_name']) ?></span></td>
                                            <td><?= $user['status'] === 'aktif' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Nonaktif</span>' ?></td>
                                            <td><?= date('d M Y H:i', strtotime($user['created_at'])) ?></td>
                                            <td class="text-center">
                                                <a href="users.php?edit=<?= $user['id'] ?>" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="users.php?delete=<?= $user['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pengguna ini?')" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash-fill"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));
</script>

</body>
</html>
