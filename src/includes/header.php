<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inisidentia</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-light" style="background-color: #ffc0cb;">
    <div class="container">
        <a class="navbar-brand fw-bold text-white" href="#">Inisidentia</a>
        <div class="d-flex">
            <span class="navbar-text me-3"><?= $_SESSION['name'] ?? '' ?> (<?= $_SESSION['role'] ?? '' ?>)</span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>
<div class="container mt-4">
