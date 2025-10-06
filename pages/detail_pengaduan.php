<?php 
$hideHeader = true;  // tanda supaya header tidak muncul
include "../includes/header.php";

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

require_once "../config/database.php";

// Ambil kode pengaduan dari URL dan amankan inputnya
$kode = isset($_GET['kode']) ? mysqli_real_escape_string($conn, $_GET['kode']) : '';
if (!$kode) {
    echo "<div style='text-align:center;margin-top:50px;color:red'>Kode pengaduan tidak ditemukan.</div>";
    include "../includes/footer.php";
    exit;
}

$user = $_SESSION['user'];
$user_id = $user['id'];
$user_role = $user['role']; // misal: 'admin' atau 'user'

// Buat query pengambilan data
if ($user_role === 'admin') {
    // Admin bisa lihat semua pengaduan tanpa filter user_id
    $sql = "SELECT * FROM pengaduan WHERE kode_pengaduan='$kode' LIMIT 1";
} else {
    // User biasa hanya bisa lihat pengaduan miliknya sendiri
    $sql = "SELECT * FROM pengaduan WHERE kode_pengaduan='$kode' AND user_id='$user_id' LIMIT 1";
}

$res = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($res);

if (!$data) {
    echo "<div style='text-align:center;margin-top:50px;color:red'>Data pengaduan tidak ditemukan atau akses ditolak.</div>";
    include "../includes/footer.php";
    exit;
}

// Tentukan link kembali berdasarkan role user
$backLink = ($user_role === 'admin') ? '/kelola_pengaduan.php' : 'pages/daftar_pengaduan.php';

?>

<link rel="stylesheet" href="../assets/css/style_detail_pengaduan.css">

<div class="detail-container">
    <h2 class="detail-title">Detail Pengaduan Anda</h2>
    <table class="detail-table" cellspacing="0" cellpadding="0">
        <tr>
            <td>Kode</td>
            <td><?= htmlspecialchars($data['kode_pengaduan']) ?></td>
        </tr>
        <tr>
            <td>Kategori</td>
            <td><?= htmlspecialchars($data['kategori']) ?></td>
        </tr>
        <tr>
            <td>Deskripsi</td>
            <td><?= nl2br(htmlspecialchars($data['deskripsi'])) ?></td>
        </tr>
        <tr>
            <td>Lokasi</td>
            <td><?= htmlspecialchars($data['lokasi']) ?></td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td><?= date('d/m/Y H:i', strtotime($data['tanggal'])) ?></td>
        </tr>
        <tr>
            <td>Status</td>
            <td><span class="status-badge"><?= strtoupper($data['status']) ?></span></td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>
                <?php if ($data['lampiran']): ?>
                    <a href="../uploads/<?= htmlspecialchars($data['lampiran']) ?>" target="_blank" class="link-lampiran">Lihat Lampiran</a>
                    <?php
                    $ext = strtolower(pathinfo($data['lampiran'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg','jpeg','png'])):
                    ?>
                        <br>
                        <img src="../uploads/<?= htmlspecialchars($data['lampiran']) ?>" alt="Lampiran" class="lampiran-img">
                    <?php endif; ?>
                <?php else: ?>
                    <em style="color:#666;">Tidak ada lampiran</em>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    <a href="<?= $backLink ?>" class="btn-kembali">Kembali</a>
</div>

<?php include "../includes/footer.php"; ?>
