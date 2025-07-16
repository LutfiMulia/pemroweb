<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Ambil daftar status untuk dropdown
$status_q = $conn->query("SELECT id, name FROM incident_statuses");
$status_list = [];
while ($row = $status_q->fetch_assoc()) {
    $status_list[] = $row;
}

// Proses perubahan status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['incident_id'], $_POST['status_id'])) {
    $incident_id = intval($_POST['incident_id']);
    $status_id = intval($_POST['status_id']);

    $stmt = $conn->prepare("UPDATE incidents SET status_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $status_id, $incident_id);
    $stmt->execute();
    redirect('incidents.php');
}

// Ambil data semua insiden
$query = "
    SELECT i.id, i.title, i.description, u.name AS pelapor, c.name AS kategori, 
           p.name AS prioritas, d.name AS lokasi, s.name AS status, i.status_id
    FROM incidents i
    JOIN users u ON i.reported_by = u.id
    LEFT JOIN incident_categories c ON i.category_id = c.id
    LEFT JOIN incident_priorities p ON i.priority_id = p.id
    LEFT JOIN departments d ON i.department_id = d.id
    LEFT JOIN incident_statuses s ON i.status_id = s.id
    ORDER BY i.reported_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Insiden - Insidentia</title>
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
        /* Enhanced styles for table containers */
        .table-container {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Consistent table container shadow */
            border: 1px solid #eee; /* Border tipis */
        }
        .btn-pink { /* Consistent button style */
            background-color: #ff69b4;
            color: white;
            border: none;
            font-weight: bold;
            transition: 0.3s ease;
            border-radius: 8px; /* Rounded corners for buttons */
            padding: 8px 15px; /* Lebih banyak padding */
        }
        .btn-pink:hover {
            background-color: #ff1493;
            color: white;
            transform: translateY(-2px); /* Efek angkat saat hover */
        }
        .table-hover tbody tr:hover {
            background-color: #fff5f9; /* Consistent table row hover */
        }
        .action-btn i {
            font-size: 1rem;
        }
        /* Specific styling for the badges and table header in incidents table */
        .table-container .table thead.table-danger {
            background-color: #ffb6c1; /* Lighter pink for table header */
            color: white;
        }
        .table-container .table thead th {
            padding: 12px 8px; /* Padding lebih besar di header tabel */
        }
        .table-container .table tbody tr td .badge {
            background-color: #ffb6c1 !important; /* Lighter pink for general badges */
            color: #333;
            font-weight: normal;
        }
        /* Specific badge colors for priority and status if needed, otherwise default to .badge */
        /* Keeping original warning/info colors for distinct visual cues if desired */
        .table-container .table tbody tr td .badge.bg-warning {
            background-color: #ffc107 !important; /* Original Bootstrap warning yellow */
            color: #212529 !important;
        }
        .table-container .table tbody tr td .badge.bg-info {
            background-color: #0dcaf0 !important; /* Original Bootstrap info cyan */
            color: #212529 !important;
        }
        .form-select-sm {
            border-radius: 8px; /* Rounded corners for small select */
            border: 1px solid #ddd; /* Lighter border */
            background-color: #f8f9fa; /* Light background */
            color: #333; /* Dark text */
        }
        .form-select-sm:focus {
            border-color: #ff69b4;
            box-shadow: 0 0 0 0.2rem rgba(255, 105, 180, 0.25);
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
                <a href="categories.php" class="nav-link"><i class="bi bi-tags me-2"></i> Kategori</a>
                <a href="priorities.php" class="nav-link"><i class="bi bi-exclamation-triangle me-2"></i> Prioritas</a>
                <a href="statuses.php" class="nav-link"><i class="bi bi-check2-circle me-2"></i> Status</a>
                <a href="locations.php" class="nav-link"><i class="bi bi-geo-alt me-2"></i> Lokasi</a>
                <a href="incidents.php" class="nav-link fw-bold active"><i class="bi bi-journal-text me-2"></i> Insiden</a>
                <a href="reports.php" class="nav-link"><i class="bi bi-bar-chart-line me-2"></i> Laporan</a>
                <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center mb-4 section-title"><i class="bi bi-journal-text me-2"></i>Manajemen Insiden</h2>

            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-danger text-center">
                            <tr>
                                <th>Judul</th>
                                <th>Pelapor</th>
                                <th>Kategori</th>
                                <th>Prioritas</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th style="width: 150px;">Ubah Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['title']) ?></td>
                                    <td><?= htmlspecialchars($row['pelapor']) ?></td>
                                    <td><span class="badge bg-pink text-dark"><?= htmlspecialchars($row['kategori']) ?></span></td>
                                    <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($row['prioritas']) ?></span></td>
                                    <td><span class="badge bg-pink text-dark"><?= htmlspecialchars($row['lokasi']) ?></span></td>
                                    <td><span class="badge bg-info text-dark"><?= htmlspecialchars($row['status']) ?></span></td>
                                    <td>
                                        <form method="post" class="d-flex align-items-center">
                                            <input type="hidden" name="incident_id" value="<?= $row['id'] ?>">
                                            <select name="status_id" class="form-select form-select-sm me-2">
                                                <?php foreach ($status_list as $status): ?>
                                                    <option value="<?= $status['id'] ?>" <?= $status['id'] == $row['status_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($status['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-pink">Simpan</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php if ($result->num_rows == 0): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Belum ada insiden dilaporkan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));
</script>

</body>
</html>
