<?php
$host = "localhost";
$dbname = "inisidentia_db";
$user = "root";
$pass = "";

// Buat koneksi
$conn = new mysqli($host, $user, $pass, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
