<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";
$message = "";

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Check if token is valid
    $stmt = $conn->prepare("SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW() LIMIT 1");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $email = $user['email'];
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $new_password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update the user's password
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? LIMIT 1");
                $stmt->bind_param("ss", $hashed_password, $email);
                
                if ($stmt->execute()) {
                    // Delete the token from the database
                    $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    
                    $message = "Password berhasil direset. Anda sekarang dapat <a href='login.php'>login</a> dengan password baru.";
                } else {
                    $error = "Terjadi kesalahan saat memperbarui password. Silakan coba lagi.";
                }
            } else {
                $error = "Password baru dan konfirmasi password tidak cocok.";
            }
        }
        
    } else {
        $error = "Token tidak valid atau telah kedaluwarsa.";
    }
} else {
    $error = "Token tidak ditemukan.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Insidentia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff0f5;
        }
        .btn-reset-gradient {
            background: linear-gradient(to right, #ff69b4, #ff1493);
            transition: all 0.3s ease;
        }
        .btn-reset-gradient:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(255, 105, 180, 0.4);
        }
        .text-accent-pink {
            color: #ff69b4;
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4">

<div class="flex flex-col bg-white rounded-xl shadow-2xl max-w-lg w-full p-6">
    <div class="text-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Setel Ulang Password</h2>
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
    <?php else: ?>

        <form method="post">
            <div class="mb-4">
                <label for="password" class="sr-only">Password Baru</label>
                <input type="password" name="password" id="password" class="form-control" required placeholder="Password Baru">
            </div>
            <div class="mb-4">
                <label for="confirm_password" class="sr-only">Konfirmasi Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required placeholder="Konfirmasi Password">
            </div>

            <button type="submit" class="btn-reset-gradient text-white font-bold py-2 px-4 rounded-lg w-full">
                Setel Ulang Password
            </button>
        </form>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
