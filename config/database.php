<?php
// Konfigurasi database
$host = "localhost";
$user = "root";
$pass = ""; // ganti dengan password database MySQL kamu jika ada
$db   = "laporlingkungan"; // pastikan sudah buat database ini

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// OPTIONAL: set charset ke utf8 biar aman karakter Indonesia
mysqli_set_charset($conn, "utf8");
?>
