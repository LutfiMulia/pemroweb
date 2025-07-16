<?php
require_once '../includes/auth_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Hitung total insiden dan total pengguna
$total_insiden = $conn->query("SELECT COUNT(*) FROM incidents")->fetch_row()[0];
$total_pengguna = $conn->query("SELECT COUNT(*) FROM users")->fetch_row()[0];

// Ambil data jumlah insiden berdasarkan status
$status_data = $conn->query("SELECT s.name AS status, COUNT(i.id) AS jumlah FROM incident_statuses s LEFT JOIN incidents i ON s.id = i.status_id GROUP BY s.id");
$chart_labels = [];
$chart_values = [];
while ($row = $status_data->fetch_assoc()) {
    $chart_labels[] = $row['status'];
    $chart_values[] = $row['jumlah'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Insidentia</title>
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
        /* Gaya tambahan untuk mempercantik tampilan dashboard */
        .welcome-card {
            background: linear-gradient(to right, #812874ff, #fad0c4); /* Gradien pink yang cantik */
            color: white;
            border-radius: 15px;
            padding: 25px; /* Disesuaikan: dari 30px menjadi 25px */
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); /* Bayangan lebih dalam */
            transition: transform 0.3s ease-in-out;
        }
        .welcome-card:hover {
            transform: translateY(-8px); /* Efek angkat saat hover */
        }
        .welcome-card h2 {
            font-size: 2.2rem; /* Disesuaikan: dari 2.8rem menjadi 2.2rem */
            font-weight: bold;
            margin-bottom: 8px; /* Disesuaikan: dari 10px menjadi 8px */
        }
        .welcome-card p {
            font-size: 1rem; /* Disesuaikan: dari 1.2rem menjadi 1rem */
            opacity: 0.9;
        }
        .card-summary {
            border-radius: 15px;
            padding: 20px; /* Disesuaikan: dari 25px menjadi 20px */
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            transition: transform 0.2s ease-in-out;
            border: 1px solid #eee; /* Border tipis */
        }
        .card-summary:hover {
            transform: translateY(-5px);
        }
        .card-summary h5 {
            font-size: 1.1rem; /* Disesuaikan: dari 1.2rem menjadi 1.1rem */
            font-weight: bold;
            color: #6c757d; /* Warna teks sekunder */
            margin-bottom: 12px; /* Disesuaikan: dari 15px menjadi 12px */
        }
        .card-summary h3 {
            font-size: 2.5rem; /* Disesuaikan: dari 3rem menjadi 2.5rem */
            font-weight: bold;
            color: #ff69b4; /* Warna pink untuk angka */
            margin-top: 8px; /* Disesuaikan: dari 10px menjadi 8px */
        }
        .chart-card {
            background-color: white;
            border-radius: 15px;
            padding: 20px; /* Disesuaikan: dari 25px menjadi 20px */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            border: 1px solid #eee; /* Border tipis */
        }
        /* Chart.js specific styling */
        #statusChart {
            max-height: 300px; /* Disesuaikan: dari 350px menjadi 300px */
        }
        .chartjs-render-monitor {
            animation: chartjs-render-animation 0.5s forwards; /* Animasi saat chart muncul */
        }
        @keyframes chartjs-render-animation {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                <a href="dashboard.php" class="nav-link fw-bold active"><i class="bi bi-house-door me-2"></i> Dashboard</a>
                <a href="users.php" class="nav-link"><i class="bi bi-people me-2"></i> Pengguna</a>
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
            <!-- Welcome Card -->
            <div class="welcome-card">
                <h2>Selamat Datang, Admin!</h2>
                <p>Setiap Insiden Tercatat, Setiap Solusi Terwujud</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card-summary text-center">
                        <h5 class="fw-bold"><i class="bi bi-journal-text me-2"></i>Total Insiden</h5>
                        <h3><?= htmlspecialchars($total_insiden) ?></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-summary text-center">
                        <h5 class="fw-bold"><i class="bi bi-people me-2"></i>Total Pengguna</h5>
                        <h3><?= htmlspecialchars($total_pengguna) ?></h3>
                    </div>
                </div>
            </div>

            <div class="chart-card">
                <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-graph-up me-2"></i>Grafik Insiden per Status</h5>
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Jumlah Insiden',
                data: <?= json_encode($chart_values) ?>,
                backgroundColor: '#ff69b4', // Warna pink untuk bar
                borderRadius: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Penting untuk kontrol ukuran
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)' // Garis grid lebih terang
                    }
                },
                x: {
                    grid: {
                        display: false // Tanpa garis grid vertikal
                    }
                }
            }
        }
    });
</script>

</body>
</html>
