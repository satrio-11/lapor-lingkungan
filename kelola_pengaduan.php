<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

// Filter status
$status_filter = $_GET['status'] ?? '';
$where = "";
if (in_array($status_filter, ['belum', 'proses', 'selesai'])) {
    $status_filter_esc = mysqli_real_escape_string($conn, $status_filter);
    $where = "WHERE status='$status_filter_esc'";
}

// Proses ubah status
if (isset($_GET['status_change']) && isset($_GET['to'])) {
    $id = intval($_GET['status_change']);
    $to = $_GET['to'];

    if (in_array($to, ['belum', 'proses', 'selesai'])) {
        $to_esc = mysqli_real_escape_string($conn, $to);
        $id_esc = $id;
        $update_sql = "UPDATE pengaduan SET status='$to_esc' WHERE id=$id_esc";
        $res = mysqli_query($conn, $update_sql);
        if (!$res) {
            die("Gagal update status: " . mysqli_error($conn));
        }
        header("Location: kelola_pengaduan.php?msg=status");
        exit;
    }
}

// Hapus pengaduan
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $id_esc = $id;
    $res = mysqli_query($conn, "DELETE FROM pengaduan WHERE id=$id_esc");
    if (!$res) {
        die("Gagal hapus pengaduan: " . mysqli_error($conn));
    }
    header("Location: kelola_pengaduan.php?msg=deleted");
    exit;
}

$query = "SELECT * FROM pengaduan $where ORDER BY tanggal DESC";
$q = mysqli_query($conn, $query);
if (!$q) {
    die("Query error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Kelola Pengaduan | Admin - LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>

<header class="admin-header">
    <div class="logo">
        <img src="assets/img/logolingkungann.png" alt="Logo LaporLingkungan" />
        LaporLingkungan <span>Jogja</span>
    </div>
    <nav>
        <a href="dashboard_admin.php" class="btn orange">Dashboard Admin</a>
        <a href="logout.php" class="btn orange">Logout</a>
    </nav>
</header>

<div class="main-content">
    <div class="kategori-card">
        <h2>Manajemen Pengaduan</h2>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <div class="alert-kat hapus">Pengaduan dihapus!</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'status'): ?>
            <div class="alert-kat sukses">Status pengaduan berhasil diubah!</div>
        <?php endif; ?>

        <!-- Filter Status -->
        <form method="get" class="kategori-form" aria-label="Filter status pengaduan">
            <select name="status" aria-label="Pilih status pengaduan">
                <option value="" <?= $status_filter === '' ? 'selected' : '' ?>>Semua Status</option>
                <option value="belum" <?= $status_filter === 'belum' ? 'selected' : '' ?>>Belum Diproses</option>
                <option value="proses" <?= $status_filter === 'proses' ? 'selected' : '' ?>>Proses</option>
                <option value="selesai" <?= $status_filter === 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
            <button type="submit" class="btn orange">Filter</button>
        </form>

        <table class="kategori-table" role="table" aria-label="Daftar pengaduan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php if (mysqli_num_rows($q) === 0): ?>
                <tr><td colspan="6" class="no-data">Belum ada pengaduan.</td></tr>
            <?php else: ?>
                <?php
                $no = 1;
                while ($row = mysqli_fetch_assoc($q)):
                    $status = $row['status'];
                    $statusClass = "";
                    $btnText = "";
                    $nextStatus = "";

                    if ($status === "belum") {
                        $statusClass = "status-belum";
                        $btnText = "Proses";
                        $nextStatus = "proses";
                    } elseif ($status === "proses") {
                        $statusClass = "status-proses";
                        $btnText = "Tandai Selesai";
                        $nextStatus = "selesai";
                    } else { // selesai
                        $statusClass = "status-selesai";
                        $btnText = "Kembalikan Proses";
                        $nextStatus = "proses";
                    }
                ?>
                <tr>
                    <td data-label="No"><?= $no++ ?></td>
                    <td data-label="Kategori"><?= htmlspecialchars($row['kategori']) ?></td>
                    <td data-label="Lokasi"><?= htmlspecialchars($row['lokasi']) ?></td>
                    <td data-label="Status">
                        <span class="status-badge <?= $statusClass ?>"><?= strtoupper($status) ?></span>
                    </td>
                    <td data-label="Tanggal"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td data-label="Aksi" class="aksi-link">
                        <a href="pages/detail_pengaduan.php?kode=<?= urlencode($row['kode_pengaduan']) ?>" class="lihat" target="_blank" rel="noopener noreferrer">Lihat</a>
                        <a href="kelola_pengaduan.php?status_change=<?= $row['id'] ?>&to=<?= $nextStatus ?>" class="status-toggle <?= $status === "proses" ? "proses" : "selesai" ?>" 
                           onclick="return confirm('Ubah status pengaduan ini?');"><?= $btnText ?></a>
                        <a href="kelola_pengaduan.php?del=<?= $row['id'] ?>" class="hapus" onclick="return confirm('Hapus pengaduan ini?');">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
