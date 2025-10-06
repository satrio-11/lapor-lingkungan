<?php
session_start();
require_once "../config/database.php";

// Pastikan user login dan role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../pages/login.php?msg=" . urlencode("Akses ditolak."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $currentStatus = $_POST['status'] ?? '';

    // Tentukan status baru (toggle)
    $newStatus = ($currentStatus === 'Selesai') ? 'Belum Ditanggapi' : 'Selesai';

    // Update status di database
    $update = mysqli_query($conn, "UPDATE pengaduan SET status='$newStatus' WHERE id='$id'");

    if ($update) {
        header("Location: ../pages/dashboard_admin.php?msg=" . urlencode("Status berhasil diubah."));
        exit;
    } else {
        header("Location: ../pages/dashboard_admin.php?msg=" . urlencode("Gagal mengubah status."));
        exit;
    }
} else {
    header("Location: ../pages/dashboard_admin.php");
    exit;
}
