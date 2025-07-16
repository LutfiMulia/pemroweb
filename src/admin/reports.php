<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Ambil data status dan hitung jumlah insiden per status
$result = $conn->query("SELECT s.name AS status, COUNT(i.id) AS total
                        FROM incident_statuses s
                        LEFT JOIN incidents i ON s.id = i.status_id
                        GROUP BY s.id");

$report_data = [];
$total_insiden = 0;
while ($row = $result->fetch_assoc()) {
    $report_data[] = $row;
    $total_insiden += $row['total'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Ringkasan Insiden - Insidentia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../assets/css/style.css">
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
        /* Enhanced styles for table and chart containers */
        .table-container {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Consistent table container shadow */
            border: 1px solid #eee; /* Border tipis */
        }
        .btn-pink { /* Consistent button style (not directly used in reports, but good to have) */
            background-color: #ff69b4;
            color: white;
            border: none;
            font-weight: bold;
            transition: 0.3s ease;
            border-radius: 8px;
            padding: 10px 20px;
        }
        .btn-pink:hover {
            background-color: #ff1493;
            color: white;
            transform: translateY(-2px);
        }
        .table-hover tbody tr:hover {
            background-color: #fff5f9; /* Consistent table row hover */
        }
        .action-btn i {
            font-size: 1rem;
        }
        /* Specific styling for the table header in reports table */
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
        .chart-container {
            width: 100%;
            max-width: 800px; /* Increased max-width for chart and legend side-by-side */
            margin: auto;
            background-color: white; /* Added background for chart container */
            border-radius: 15px; /* Consistent border-radius */
            padding: 20px; /* Consistent padding */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Consistent shadow */
            border: 1px solid #eee; /* Border tipis */
            display: flex; /* Use flexbox for chart and legend layout */
            flex-direction: row; /* Arrange items in a row */
            align-items: center; /* Center items vertically */
            justify-content: center; /* Center items horizontally */
        }
        /* Chart.js specific styling */
        #statusChart {
            max-height: 300px; /* Batasi tinggi chart agar ada ruang untuk legenda di samping */
            max-width: 300px; /* Batasi lebar chart */
        }
        .chartjs-render-monitor {
            animation: chartjs-render-animation 0.5s forwards; /* Animasi saat chart muncul */
        }
        @keyframes chartjs-render-animation {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .chart-legend-container {
            margin-left: 20px; /* Spasi antara chart dan legenda */
            text-align: left; /* Teks legenda rata kiri */
        }
        .chart-legend-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .chart-legend-color-box {
            width: 20px;
            height: 20px;
            border-radius: 5px;
            margin-right: 10px;
            border: 1px solid rgba(0,0,0,0.1); /* Border tipis untuk kotak warna */
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
                <a href="incidents.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> Insiden</a>
                <a href="reports.php" class="nav-link fw-bold active"><i class="bi bi-bar-chart-line me-2"></i> Laporan</a>
                <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center fw-bold section-title mb-4"><i class="bi bi-bar-chart-line me-2"></i>Laporan Ringkasan Insiden Berdasarkan Status</h2>

            <div class="table-container mb-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle table-hover">
                        <thead class="table-danger text-center">
                            <tr>
                                <th>Status</th>
                                <th>Jumlah Insiden</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report_data as $row): ?>
                                <tr>
                                    <td><span class="badge bg-pink text-dark"><?= htmlspecialchars($row['status']) ?></span></td>
                                    <td><?= htmlspecialchars($row['total']) ?></td>
                                    <td><?= $total_insiden > 0 ? round(($row['total'] / $total_insiden) * 100, 2) . '%' : '0%' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr class="fw-bold">
                                <td>Total</td>
                                <td><?= htmlspecialchars($total_insiden) ?></td>
                                <td>100%</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="chart-container">
                <div class="d-flex flex-column align-items-center">
                    <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-pie-chart-fill me-2"></i>Distribusi Insiden Berdasarkan Status</h5>
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="chart-legend-container">
                    <?php
                    // Define a different set of colors for the pie chart
                    $pie_chart_colors = [
                        '#66c2a5', // Soft Green
                        '#fc8d62', // Orange
                        '#8da0cb', // Light Blue/Purple
                        '#e78ac3', // Pinkish Purple
                        '#a6d854', // Lime Green
                        '#ffd92f', // Yellow
                        '#e5c494'  // Light Brown
                    ];
                    $color_index = 0;
                    foreach ($report_data as $row):
                        $color = $pie_chart_colors[$color_index % count($pie_chart_colors)];
                        $color_index++;
                    ?>
                        <div class="chart-legend-item">
                            <div class="chart-legend-color-box" style="background-color: <?= $color ?>;"></div>
                            <span><?= htmlspecialchars($row['status']) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?= json_encode(array_column($report_data, 'status')) ?>,
            datasets: [{
                label: 'Distribusi Status Insiden',
                data: <?= json_encode(array_column($report_data, 'total')) ?>,
                backgroundColor: [
                    '#66c2a5', // Soft Green
                    '#fc8d62', // Orange
                    '#8da0cb', // Light Blue/Purple
                    '#e78ac3', // Pinkish Purple
                    '#a6d854', // Lime Green
                    '#ffd92f', // Yellow
                    '#e5c494'  // Light Brown
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true, // Set to true for responsiveness
            maintainAspectRatio: false, // Penting untuk kontrol ukuran
            plugins: {
                legend: {
                    display: false // Sembunyikan legenda bawaan Chart.js
                }
            }
        }
    });
</script>

</body>
</html>
