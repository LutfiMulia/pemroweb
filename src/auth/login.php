<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT users.*, roles.name AS role_name FROM users 
                            JOIN roles ON users.role_id = roles.id 
                            WHERE email = ? AND status = 'aktif' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role_name'];

            // Arahkan berdasarkan role
            if ($user['role_name'] === 'admin') {
                redirect('../admin/dashboard.php');
            } else {
                redirect('../user/dashboard.php');
            }
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan atau akun nonaktif.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Insidentia</title>
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
        /* Custom gradient for the login button */
        .btn-login-gradient {
            background: linear-gradient(to right, #ff69b4, #ff1493); /* Vibrant pink */
            transition: all 0.3s ease;
        }
        .btn-login-gradient:hover {
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
                <img src="../assets/img/login2.jpg" alt="Illustration" class="w-full max-w-xs md:max-w-sm h-auto mb-5 rounded-lg shadow-lg"> <!-- max-w-sm for slightly smaller illustration -->
                <h2 class="text-2xl font-bold mb-3">Welcome Back!</h2>
                <p class="text-lg opacity-90 max-w-sm">Pelaporan Mudah, Pengelolaan Optimal</p>
            </div>

            <!-- Decorative circles removed -->
        </div>

        <!-- Right Panel (Login Form) -->
        <div class="p-8 md:w-1/2 bg-white flex flex-col justify-center relative"> <!-- Added relative for logo positioning -->
            <!-- Logo Insidentia moved here, above "Log In" -->
            <div class="absolute top-8 left-1/2 -translate-x-1/2 flex items-center mb-4"> <!-- Positioned absolutely, centered horizontally -->
                <img src="../assets/img/logo.png" alt="Insidentia Logo" class="w-8 h-8 rounded-full mr-2">
                <span class="font-bold text-xl text-accent-pink">Insidentia</span>
            </div>

            <div class="max-w-sm mx-auto w-full mt-16"> <!-- Adjusted margin-top to make space for logo -->
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">Log In</h2>
                    <p class="text-gray-500 text-sm">
                        Don't have an account? <a href="register.php" class="text-accent-pink hover:underline font-medium">Create an account</a>
                        <br>It will take less than a minute.
                    </p>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div class="mb-4 input-icon-wrapper">
                        <label for="email" class="sr-only">Email</label>
                        <i class="bi bi-person-fill text-xl"></i>
                        <input type="email" name="email" id="email" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink focus:border-transparent text-gray-800" required placeholder="admin@insidentia.local" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="mb-4 input-icon-wrapper">
                        <label for="password" class="sr-only">Password</label>
                        <i class="bi bi-lock-fill text-xl"></i>
                        <input type="password" name="password" id="password" class="form-control w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-pink focus:border-transparent text-gray-800" required placeholder="********">
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-accent-pink border-gray-300 rounded focus:ring-accent-pink">
                            <label for="remember-me" class="ml-2 block text-sm text-gray-900">
                                Remember password
                            </label>
                        </div>
                        <div class="text-sm">
                            <a href="#" class="font-medium text-accent-pink hover:underline">
                                Forgot your password?
                            </a>
                        </div>
                    </div>

                    <button type="submit" class="btn-login-gradient text-white font-bold py-3 px-6 rounded-lg w-full text-lg">
                        Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
