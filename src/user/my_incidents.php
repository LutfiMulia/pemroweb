<?php
require_once '../includes/user_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'pelapor') {
    redirect('../auth/logout.php');
}

$user_id = $_SESSION['user_id'];

$query = "SELECT i.id, i.title, i.reported_at, s.name AS status, c.name AS category, p.name AS priority
          FROM incidents i
          LEFT JOIN incident_statuses s ON i.status_id = s.id
          LEFT JOIN incident_categories c ON i.category_id = c.id
          LEFT JOIN incident_priorities p ON i.priority_id = p.id
          WHERE i.reported_by = ?
          ORDER BY i.reported_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Insiden Saya - Insidentia</title>
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
        /* Gaya untuk tabel riwayat laporan */
        .table-container-custom {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            border: 1px solid #eee; /* Border tipis */
        }
        .table-custom thead {
            background-color: #ffb6c1; /* Lighter pink for table header */
            color: white;
        }
        .table-custom thead th {
            padding: 12px 8px; /* Padding lebih besar di header tabel */
        }
        .table-custom tbody tr:hover {
            background-color: #fff5f9; /* Consistent table row hover */
        }
        .badge-status-custom {
            padding: 6px 12px;
            border-radius: 15px; /* Lebih bulat */
            font-weight: bold;
            font-size: 0.85em;
        }
        /* Warna badge status yang lebih bervariasi */
        .badge-status-custom.open { background-color: #ff69b4; color: white; } /* Pink */
        .badge-status-custom.in-progress { background-color: #ffc107; color: #333; } /* Amber */
        .badge-status-custom.resolved { background-color: #28a745; color: white; } /* Green */
        .badge-status-custom.closed { background-color: #6c757d; color: white; } /* Gray */
        .badge-status-custom.pending { background-color: #17a2b8; color: white; } /* Cyan */
        /* Default fallback if status name doesn't match specific classes */
        .badge-status-custom { background-color: #6c757d; color: white; }
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
                <a href="my_incidents.php" class="nav-link fw-bold active"><i class="bi bi-journal-text me-2"></i> Riwayat Laporan Saya</a> <!-- PATH FIXED HERE -->
                <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center mb-4 section-title"><i class="bi bi-journal-text me-2"></i>Riwayat Laporan Saya</h2>

            <div class="table-container-custom">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle table-custom">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Tanggal Lapor</th>
                                <th>Kategori</th>
                                <th>Prioritas</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['title']) ?></td>
                                        <td><?= date('d M Y H:i', strtotime($row['reported_at'])) ?></td>
                                        <td><?= htmlspecialchars($row['category']) ?></td>
                                        <td><?= htmlspecialchars($row['priority']) ?></td>
                                        <td>
                                            <?php
                                                // Menentukan kelas badge berdasarkan nama status
                                                $status_class = strtolower(str_replace(' ', '-', $row['status']));
                                                echo '<span class="badge badge-status-custom ' . $status_class . '">' . htmlspecialchars($row['status']) . '</span>';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center py-4">Belum ada laporan insiden.</td></tr>
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
</body>
</html>
