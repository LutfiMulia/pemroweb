<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Tambah kategori
if (isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO incident_categories (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();
    redirect('categories.php');
}

// Hapus kategori
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM incident_categories WHERE id = $id");
    redirect('categories.php');
}

// Ambil data kategori untuk edit jika ada
$edit_category = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $res = $conn->query("SELECT * FROM incident_categories WHERE id = $id LIMIT 1");
    $edit_category = $res->fetch_assoc();
}

// Update kategori
if (isset($_POST['update_category'])) {
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);

    $stmt = $conn->prepare("UPDATE incident_categories SET name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $description, $id);
    $stmt->execute();
    redirect('categories.php');
}

$result = $conn->query("SELECT * FROM incident_categories ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Kategori Insiden - Insidentia</title>
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
        /* Enhanced styles for form and table containers, copied from users.php enhanced */
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
            color: rgba(255, 255, 255, 0.7);
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
        /* Specific styling for the badge in categories table */
        .table-container .table tbody tr td:first-child .badge { /* Targets the badge within the first <td> (Name) */
            background-color: #ffb6c1 !important; /* Lighter pink for the category name badge */
            color: #333;
            font-weight: normal;
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
                <a href="users.php" class="nav-link"><i class="bi bi-people me-2"></i> Pengguna</a>
                <a href="categories.php" class="nav-link fw-bold active"><i class="bi bi-tags me-2"></i> Kategori</a>
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
            <h2 class="text-center mb-4 section-title"><i class="bi bi-tags me-2"></i>Manajemen Kategori Insiden</h2>

            <div class="row g-4">
                <!-- Form Section -->
                <div class="col-md-4">
                    <div class="card-form shadow">
                        <h5 class="text-white"><?= $edit_category ? 'Edit Kategori' : 'Tambah Kategori' ?></h5>
                        <form method="post">
                            <input type="hidden" name="id" value="<?= $edit_category['id'] ?? '' ?>">
                            <div class="mb-3">
                                <label class="form-label">Nama Kategori</label>
                                <input type="text" name="name" class="form-control" required value="<?= $edit_category['name'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3"><?= $edit_category['description'] ?? '' ?></textarea>
                            </div>
                            <button type="submit" name="<?= $edit_category ? 'update_category' : 'add_category' ?>" class="btn btn-pink w-100">
                                <?= $edit_category ? 'Update' : 'Tambah' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="col-md-8">
                    <div class="table-container">
                        <h5 class="fw-bold text-danger mb-3"><i class="bi bi-list-ul me-2"></i>Daftar Kategori</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle">
                                <thead class="table-danger text-center">
                                    <tr>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><span class="badge bg-pink text-dark"><?= htmlspecialchars($row['name']) ?></span></td>
                                            <td><?= htmlspecialchars($row['description']) ?></td>
                                            <td class="text-center">
                                                <a href="categories.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning me-1" data-bs-toggle="tooltip" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                                <a href="categories.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kategori ini?')" data-bs-toggle="tooltip" title="Hapus"><i class="bi bi-trash-fill"></i></a>
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
