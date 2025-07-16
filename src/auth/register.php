<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Konfirmasi password tidak cocok.";
    } else {
        // Cek email sudah ada belum
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            // Hash password dan insert
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role_id = 2; // ID pelapor (pastikan ID-nya sesuai di tabel roles)
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role_id, status) VALUES (?, ?, ?, ?, 'aktif')");
            $stmt->bind_param("sssi", $name, $email, $hash, $role_id);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil. Silakan login.";
            } else {
                $error = "Terjadi kesalahan saat menyimpan.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi - Insidentia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff0f5; /* Soft pink background */
        }
        /* Custom gradient for the left panel - Brighter tone */
        .left-panel-gradient-brighter {
            background: linear-gradient(135deg, #ff99cc, #ffcce6); /* Brighter pink tones */
        }
        /* Custom gradient for the register button - Brighter tone */
        .btn-register-gradient {
            background: linear-gradient(to right, #ff99cc, #ff66b2); /* Brighter vibrant pink */
            transition: all 0.3s ease;
        }
        .btn-register-gradient:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 153, 204, 0.4);
        }
        .text-accent-pink-brighter {
            color: #ff99cc;
        }
        .text-dark-pink { /* New style for dark pink text */
            color: #c2185b; /* A darker shade of pink */
        }
        .input-icon-wrapper {
            position: relative;
        }
        .input-icon-wrapper .bi {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af; /* Gray icon color */
        }
        .input-icon-wrapper input {
            padding-left: 40px !important; /* Make space for the icon */
        }
        /* Custom wave separator */
        .wave-separator {
            position: absolute;
            top: 0;
            left: -50px; /* Adjust as needed */
            width: 100px; /* Width of the wave */
            height: 100%;
            background-color: #fff; /* Color of the right panel */
            border-radius: 50px; /* Half of width for wave effect */
            transform: translateX(50%) rotate(45deg); /* Adjust for wave shape */
            z-index: 10;
        }
        .register-form-container {
            position: relative;
            z-index: 20; /* Ensure form is above wave */
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="flex flex-col md:flex-row bg-white rounded-xl shadow-2xl overflow-hidden max-w-4xl w-full">
        <!-- Left Panel (Illustration/Welcome) -->
        <div class="left-panel-gradient-brighter p-8 md:w-1/2 flex flex-col items-center justify-center text-white relative">
            <!-- Logo Insidentia -->
            <div class="absolute top-4 left-4 flex items-center">
                <img src="../assets/img/logo.png" alt="Insidentia Logo" class="w-8 h-8 rounded-full mr-2">
                <span class="font-bold text-xl text-white">Insidentia</span>
            </div>

            <!-- Illustration Area -->
            <div class="flex flex-col items-center justify-center text-center mt-16 md:mt-0">
               <img src="../assets/img/welcome.jpg" alt="Ilustrasi Selamat Datang" class="w-full max-w-xs md:max-w-md h-auto mb-6 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-3">Bergabunglah Bersama Kami</h2> <!-- Teks Welcome diubah -->
            </div>
        </div>

        <!-- Right Panel (Register Form) -->
        <div class="p-8 md:w-1/2 bg-white flex flex-col justify-center register-form-container">
            <div class="max-w-sm mx-auto w-full">
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-bold text-dark-pink mb-2">Hallo! Selamat Bergabung</h2> <!-- Warna teks diubah -->
                    <p class="text-gray-500 text-sm">
                        Sudah punya akun? <a href="login.php" class="text-accent-pink-brighter hover:underline font-medium">Login di sini</a>
                    </p>
                </div>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $success ?></span>
                    </div>
                <?php elseif ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-4 input-icon-wrapper">
                        <label for="name" class="sr-only">Nama Lengkap</label>
                        <i class="bi bi-person-fill text-xl"></i>
                        <input type="text" name="name" id="name" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink-brighter focus:border-transparent text-gray-800" required placeholder="Nama Lengkap" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="mb-4 input-icon-wrapper">
                        <label for="email" class="sr-only">Email</label>
                        <i class="bi bi-envelope-fill text-xl"></i>
                        <input type="email" name="email" id="email" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink-brighter focus:border-transparent text-gray-800" required placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-4 input-icon-wrapper">
                        <label for="password" class="sr-only">Password</label>
                        <i class="bi bi-lock-fill text-xl"></i>
                        <input type="password" name="password" id="password" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink-brighter focus:border-transparent text-gray-800" required placeholder="Password">
                    </div>
                    <div class="mb-6 input-icon-wrapper">
                        <label for="confirm_password" class="sr-only">Konfirmasi Password</label>
                        <i class="bi bi-lock-fill text-xl"></i>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink-brighter focus:border-transparent text-gray-800" required placeholder="Konfirmasi Password">
                    </div>

                    <button type="submit" class="btn-register-gradient text-white font-bold py-3 px-6 rounded-lg w-full text-lg">
                        Daftar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
