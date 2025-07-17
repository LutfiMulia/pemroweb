<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST['email']);
    
    // Check if email exists in database
    $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ? AND status = 'aktif' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Generate unique token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Store token in database
        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) 
                               ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
        $stmt->bind_param("sss", $email, $token, $expires);
        
        if ($stmt->execute()) {
            // In a real application, you would send an email here
            // For now, we'll just show the reset link
            $reset_link = "http://localhost/insidentia/src/auth/reset-password.php?token=" . $token;
            $message = "Link reset password telah dibuat. Silakan klik link berikut untuk mereset password Anda: <br><br>
                      <a href='$reset_link' class='text-blue-500 hover:underline'>$reset_link</a><br><br>
                      Link ini akan kadaluarsa dalam 1 jam.";
        } else {
            $error = "Terjadi kesalahan. Silakan coba lagi.";
        }
    } else {
        $error = "Email tidak ditemukan atau akun tidak aktif.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lupa Password - Insidentia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff0f5; /* Soft pink background */
        }
        /* Custom gradient for the left panel */
        .left-panel-gradient {
            background: linear-gradient(135deg, #ff69b4, #ffb6c1); /* Pink tones */
        }
        /* Custom gradient for the submit button */
        .btn-submit-gradient {
            background: linear-gradient(to right, #ff69b4, #ff1493); /* Vibrant pink */
            transition: all 0.3s ease;
        }
        .btn-submit-gradient:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 105, 180, 0.4);
        }
        .text-accent-pink {
            color: #ff69b4;
        }
        .text-accent-orange {
            color: #FFA500; /* Orange accent */
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
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

    <div class="flex flex-col md:flex-row bg-white rounded-xl shadow-2xl overflow-hidden max-w-4xl w-full">
        <!-- Left Panel (Illustration/Welcome) -->
        <div class="left-panel-gradient p-8 md:w-1/2 flex flex-col items-center justify-center text-white relative">
            <!-- Illustration Area -->
            <div class="flex flex-col items-center justify-center text-center mt-16 md:mt-0">
                <img src="../assets/img/login2.jpg" alt="Illustration" class="w-full max-w-xs md:max-w-sm h-auto mb-5 rounded-lg shadow-lg">
                <h2 class="text-2xl font-bold mb-3">Lupa Password?</h2>
                <p class="text-lg opacity-90 max-w-sm">Jangan khawatir, kami akan membantu Anda mengembalikan akses ke akun Anda</p>
            </div>
        </div>

        <!-- Right Panel (Forgot Password Form) -->
        <div class="p-8 md:w-1/2 bg-white flex flex-col justify-center relative">
            <!-- Logo Insidentia -->
            <div class="absolute top-8 left-1/2 -translate-x-1/2 flex items-center mb-4">
                <img src="../assets/img/logo.png" alt="Insidentia Logo" class="w-8 h-8 rounded-full mr-2">
                <span class="font-bold text-xl text-accent-pink">Insidentia</span>
            </div>

            <div class="max-w-sm mx-auto w-full mt-16">
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Reset Password</h2>
                    <p class="text-gray-500 text-sm">
                        Masukkan email Anda untuk mendapatkan link reset password
                    </p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($message): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $message ?></span>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-4 input-icon-wrapper">
                        <label for="email" class="sr-only">Email</label>
                        <i class="bi bi-envelope-fill text-xl"></i>
                        <input type="email" name="email" id="email" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink focus:border-transparent text-gray-800" required placeholder="Masukkan email Anda" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn-submit-gradient text-white font-bold py-3 px-6 rounded-lg w-full text-lg mb-4">
                        Kirim Link Reset
                    </button>
                </form>

                <div class="text-center">
                    <p class="text-gray-500 text-sm">
                        Ingat password Anda? <a href="login.php" class="text-accent-pink hover:underline font-medium">Kembali ke Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
