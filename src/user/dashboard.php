<?php
require_once '../includes/user_check.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Cek kalau role bukan user/pelapor, redirect
if ($_SESSION['role'] !== 'user' && $_SESSION['role'] !== 'pelapor') {
    redirect('../auth/logout.php');
}

// Ambil total insiden yang dilaporkan user ini
$user_id = $_SESSION['user_id'];
$result = $conn->prepare("SELECT COUNT(*) AS total FROM incidents WHERE reported_by = ?");
$result->bind_param("i", $user_id);
$result->execute();
$total = $result->get_result()->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Pengguna - Insidentia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        /* Gaya tambahan untuk mempercantik tampilan dashboard pengguna */
        .welcome-card-user {
            background: linear-gradient(to right, #812874ff, #fad0c4); /* Gradien pink yang cantik dan cerah */
            color: white;
            border-radius: 15px;
            padding: 20px; /* Dikecilkan */
            text-align: center;
            margin-bottom: 1.5rem; /* Dikecilkan */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); /* Bayangan lebih dalam */
            transition: transform 0.3s ease-in-out;
        }
        .welcome-card-user:hover {
            transform: translateY(-8px); /* Efek angkat saat hover */
        }
        .welcome-card-user h2 {
            font-size: 1.8rem; /* Dikecilkan */
            font-weight: bold;
            margin-bottom: 8px; /* Dikecilkan */
        }
        .welcome-card-user p {
            font-size: 1rem; /* Dikecilkan */
            opacity: 0.9;
        }
        .card-summary-user {
            border-radius: 15px;
            padding: 20px; /* Dikecilkan */
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            transition: transform 0.2s ease-in-out;
            border: 1px solid #eee; /* Border tipis */
        }
        .card-summary-user:hover {
            transform: translateY(-5px);
        }
        .card-summary-user h5 {
            font-size: 1.1rem; /* Dikecilkan */
            font-weight: bold;
            color: #6c757d; /* Warna teks sekunder */
            margin-bottom: 12px; /* Dikecilkan */
        }
        .card-summary-user h3 {
            font-size: 2.5rem; /* Dikecilkan */
            font-weight: bold;
            color: #ff69b4; /* Warna pink untuk angka */
            margin-top: 8px; /* Dikecilkan */
        }
        .btn-pink-custom { /* Custom button style for user dashboard */
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
        .btn-outline-pink-custom {
            border: 2px solid #ff69b4;
            color: #ff69b4;
            background-color: transparent;
            font-weight: bold;
            transition: 0.3s ease;
            border-radius: 8px;
            padding: 12px 25px;
            font-size: 1.1rem;
        }
        .btn-outline-pink-custom:hover {
            background-color: #ff69b4;
            color: white;
            transform: translateY(-2px);
        }
        .chart-card-user {
            background-color: white;
            border-radius: 15px;
            padding: 20px; /* Dikecilkan */
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08); /* Bayangan yang lebih jelas */
            border: 1px solid #eee; /* Border tipis */
        }
        #userStatusChart {
            max-height: 250px; /* Dikecilkan */
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
                <a href="report_incident.php" class="nav-link"><i class="bi bi-plus-circle me-2"></i> Laporkan Insiden Baru</a>
                <a href="my_incidents.php" class="nav-link"><i class="bi bi-journal-text me-2"></i> Riwayat Laporan Saya</a>
                <a href="settings.php" class="nav-link"><i class="bi bi-gear me-2"></i> Pengaturan Akun</a>
                <a href="../auth/logout.php" class="nav-link mt-3"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </nav>
        </div>

        <!-- MAIN CONTENT -->
        <div class="col-md-9 p-4">
            <h2 class="text-center mb-4 section-title"><i class="bi bi-person-circle me-2"></i>Dashboard</h2>

            <!-- Welcome Card for User -->
            <div class="welcome-card-user">
                <h2>Halo, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
                <p>Selamat datang di Dashboard Insidentia Anda, Mari kelola insiden Anda dengan mudah.</p>
            </div>

            <div class="row g-4 mb-4">
                <div class="col-md-6"> <!-- Removed mx-auto to allow side-by-side -->
                    <div class="card-summary-user text-center">
                        <h5 class="fw-bold"><i class="bi bi-file-earmark-text me-2"></i>Total Insiden Anda</h5>
                        <h3><?= htmlspecialchars($total) ?></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="chart-card-user">
                        <h5 class="fw-bold text-secondary mb-3"><i class="bi bi-pie-chart-fill me-2"></i>Distribusi Status Insiden Anda</h5>
                        <canvas id="userStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-3 col-md-8 mx-auto mt-5">
                <a href="report_incident.php" class="btn btn-pink-custom"><i class="bi bi-plus-circle me-2"></i>Laporkan Insiden Baru</a>
                <a href="my_incidents.php" class="btn btn-outline-pink-custom"><i class="bi bi-journal-text me-2"></i>Lihat Riwayat Laporan Saya</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const ctxUser = document.getElementById('userStatusChart').getContext('2d');
    const userStatusChart = new Chart(ctxUser, {
        type: 'pie',
        data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Jumlah Insiden',
                data: <?= json_encode($chart_values) ?>,
                backgroundColor: [
                    '#ff69b4', // Pink
                    '#a12175ff', // Light Pink
                    '#ffc0cb', // Cherry Blossom Pink
                    '#db7093', // Pale Violet Red
                    '#ff1493', // Deep Pink
                    '#e0b0ff', // Mauve
                    '#c3aed6'  // Lavender
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#333'
                    }
                }
            }
        }
    });
</script>

</body>
</html>
