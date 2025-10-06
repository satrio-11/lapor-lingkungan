<?php
session_start();
require_once "../config/database.php";

// Pastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../pages/login.php?msg=" . urlencode("Silakan login terlebih dahulu."));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = $_SESSION['user']['id'];
    $kategori = mysqli_real_escape_string($conn, trim($_POST['kategori'] ?? ''));
    $deskripsi = mysqli_real_escape_string($conn, trim($_POST['deskripsi'] ?? ''));
    $lokasi   = mysqli_real_escape_string($conn, trim($_POST['lokasi'] ?? ''));

    // Validasi input wajib
    if (empty($kategori) || empty($deskripsi) || empty($lokasi)) {
        header("Location: ../pages/pengaduan_form.php?msg=" . urlencode("Semua field wajib diisi."));
        exit;
    }

    // Generate kode unik pengaduan (YGL + 6 digit random)
    function generateKode() {
        return "YGL" . strval(rand(100000, 999999));
    }
    $kode = generateKode();

    // Pastikan kode unik di DB
    $cekKode = mysqli_query($conn, "SELECT kode_pengaduan FROM pengaduan WHERE kode_pengaduan='$kode'");
    while (mysqli_num_rows($cekKode) > 0) {
        $kode = generateKode();
        $cekKode = mysqli_query($conn, "SELECT kode_pengaduan FROM pengaduan WHERE kode_pengaduan='$kode'");
    }

    // Handle upload lampiran (opsional)
    $lampiran = "";
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['lampiran']['tmp_name'];
        $fileName = $_FILES['lampiran']['name'];
        $fileSize = $_FILES['lampiran']['size'];
        $fileType = $_FILES['lampiran']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];

        if (!in_array($fileExtension, $allowedExt)) {
            header("Location: ../pages/pengaduan_form.php?msg=" . urlencode("Jenis file lampiran tidak diizinkan."));
            exit;
        }
        // Batasi ukuran file (misal 2MB)
        if ($fileSize > 2 * 1024 * 1024) {
            header("Location: ../pages/pengaduan_form.php?msg=" . urlencode("Ukuran file lampiran maksimal 2MB."));
            exit;
        }

        // Nama file unik
        $newFileName = $kode . '_' . time() . '.' . $fileExtension;

        $uploadFileDir = "../uploads/";
        $destPath = $uploadFileDir . $newFileName;

        if (move_uploaded_file($fileTmpPath, $destPath)) {
            $lampiran = $newFileName;
        } else {
            header("Location: ../pages/pengaduan_form.php?msg=" . urlencode("Gagal mengupload lampiran."));
            exit;
        }
    }

    // Simpan data pengaduan ke database
    $sql = "INSERT INTO pengaduan (user_id, kode_pengaduan, kategori, deskripsi, lokasi, lampiran, status, created_at) VALUES 
            ('$user_id', '$kode', '$kategori', '$deskripsi', '$lokasi', '$lampiran', 'Belum Ditanggapi', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../pages/pengaduan_form.php?msg=" . urlencode("Pengaduan berhasil dikirim! Kode Anda: $kode"));
        exit;
    } else {
        header("Location: ../pages/pengaduan_form.php?msg=" . urlencode("Gagal mengirim pengaduan, coba lagi."));
        exit;
    }
} else {
    header("Location: ../pages/pengaduan_form.php");
    exit;
}
