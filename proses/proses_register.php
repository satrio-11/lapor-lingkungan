<?php
session_start();
require_once "../config/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil dan bersihkan data input
    $nama      = trim(mysqli_real_escape_string($conn, $_POST['nama_lengkap'] ?? ''));
    $email     = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
    $username  = trim(mysqli_real_escape_string($conn, $_POST['username'] ?? ''));
    $password  = $_POST['password'] ?? '';
    $konfirmasi= $_POST['konfirmasi'] ?? '';

    // Validasi input kosong
    if ($nama === '' || $email === '' || $username === '' || $password === '' || $konfirmasi === '') {
        header("Location: ../pages/register.php?msg=" . urlencode("Semua field wajib diisi."));
        exit;
    }

    // Validasi format email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../pages/register.php?msg=" . urlencode("Format email tidak valid."));
        exit;
    }

    // Validasi password dan konfirmasi sama
    if ($password !== $konfirmasi) {
        header("Location: ../pages/register.php?msg=" . urlencode("Password dan konfirmasi tidak cocok."));
        exit;
    }

    // Cek apakah username atau email sudah terdaftar
    $cekUser = mysqli_query($conn, "SELECT id FROM users WHERE username='$username' OR email='$email' LIMIT 1");
    if (!$cekUser) {
        // Query error
        header("Location: ../pages/register.php?msg=" . urlencode("Terjadi kesalahan pada sistem, coba lagi."));
        exit;
    }
    if (mysqli_num_rows($cekUser) > 0) {
        header("Location: ../pages/register.php?msg=" . urlencode("Username atau email sudah terdaftar."));
        exit;
    }

    // Hash password dengan bcrypt
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Insert data user baru dengan role 'user'
    $insert = mysqli_query($conn, "INSERT INTO users (nama, email, username, password, role) VALUES ('$nama', '$email', '$username', '$passwordHash', 'user')");

    if ($insert) {
        // Registrasi berhasil, arahkan ke halaman login
        header("Location: ../pages/login.php?msg=" . urlencode("Registrasi berhasil! Silakan login."));
        exit;
    } else {
        // Gagal insert data
        header("Location: ../pages/register.php?msg=" . urlencode("Gagal registrasi, coba lagi."));
        exit;
    }
} else {
    // Jika bukan metode POST, redirect ke halaman register
    header("Location: ../pages/register.php");
    exit;
}
