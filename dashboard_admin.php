<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once __DIR__ . '/config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin | LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="assets/img/logolingkungan.png" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" />
    <script src="assets/js/main.js" defer></script>
    <style>
        /* tambahan styling agar card statistik dan menu serasi */
        .admin-card {
            background: #f6faff;
            border-radius: 18px;
            box-shadow: 0 4px 16px #c3d7ff36;
            padding: 24px 20px;
            text-align: center;
            font-weight: 700;
            color: #234b95;
            font-size: 1.15rem;
            cursor: pointer;
            user-select: none;
            transition: box-shadow 0.3s ease;
        }
        .admin-card:hover {
            box-shadow: 0 6px 24px #a4c6ff7d;
        }
        .admin-card .icon {
            font-size: 2.6rem;
            margin-bottom: 12px;
        }
        .statistik-container {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 1000px;
            margin: 0 auto;
        }
        .statistik-item {
            background: var(--soft);
            border-radius: 14px;
            padding: 15px 28px;
            text-align: center;
            min-width: 140px;
            font-weight: 700;
            color: #234b95;
            box-shadow: 0 2px 8px #c3d7ff50;
            font-size: 1rem;
        }
        .statistik-item .number {
            font-size: 1.5rem;
            margin-bottom: 6px;
            color: var(--primary);
        }
    </style>
</head>
<body class="admin">

<!-- HEADER ADMIN -->
<header class="admin-header">
    <div class="logo">
        <img src="assets/img/logolingkungann.png" alt="Logo LaporLingkungan" />
        LaporLingkungan <span>Jogja</span>
    </div>
    <nav>
        <a href="logout.php" class="btn orange">Logout</a>
    </nav>
</header>

<div class="main-content">
    <section style="padding: 38px 30px 30px;">
        <h2 style="text-align:center;color:#234b95;font-size:2rem;font-weight:800;letter-spacing:.5px;margin-bottom:16px;">
            Selamat Datang, Admin!
        </h2>
        <p style="text-align:center;color:#444;margin-bottom:36px;">
            Gunakan menu di bawah untuk mengelola data aplikasi.<br>
            Semua perubahan langsung aktif untuk user tanpa perlu ubah kode.
        </p>

        <!-- MENU GRID ADMIN -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:20px;max-width:900px;margin:0 auto 36px;">
            <a href="admin_faq.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">❓</div>
                <div>Kelola FAQ</div>
            </a>
            <a href="admin_kategori.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">📂</div>
                <div>Kelola Kategori</div>
            </a>
            <a href="admin_lokasi.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">📍</div>
                <div>Kelola Lokasi</div>
            </a>
            <a href="kelola_user.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">🧑‍💼</div>
                <div>Kelola User</div>
            </a>
            <a href="kelola_pengaduan.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">📝</div>
                <div>Kelola Pengaduan</div>
            </a>
            <a href="admin_export.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">📥</div>
                <div>Export Data</div>
            </a>
            <a href="admin_inbox.php" class="admin-card" style="text-decoration:none;">
                <div class="icon">✉️</div>
                <div>Pesan / Inbox</div>
            </a>
        </div>

        <!-- STATISTIK SINGKAT -->
        <div class="statistik-container">
            <?php
            // Query hitung semua item statistik
            $totalPengaduan = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM pengaduan"))[0];
            $totalKategori = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM kategori"))[0];
            $totalLokasi = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM lokasi"))[0];
            $totalUser = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM users"))[0];
            $totalFAQ = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM faq"))[0];
            $totalInbox = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM inbox"))[0];
            // Misal export data adalah jumlah file export di folder, contoh hardcode saja
            $totalExport = 5; // Bisa diganti sesuai jumlah file export sesungguhnya
            ?>
            <div class="statistik-item">
                <div class="number"><?= $totalPengaduan ?></div>
                <div>Total Pengaduan</div>
            </div>
            <div class="statistik-item">
                <div class="number"><?= $totalKategori ?></div>
                <div>Kategori</div>
            </div>
            <div class="statistik-item">
                <div class="number"><?= $totalLokasi ?></div>
                <div>Lokasi</div>
            </div>
            <div class="statistik-item">
                <div class="number"><?= $totalUser ?></div>
                <div>User Terdaftar</div>
            </div>
            <div class="statistik-item">
                <div class="number"><?= $totalFAQ ?></div>
                <div>FAQ</div>
            </div>
            <div class="statistik-item">
                <div class="number"><?= $totalInbox ?></div>
                <div>Inbox</div>
            </div>
            <div class="statistik-item">
                <div class="number"><?= $totalExport ?></div>
                <div>Data Export</div>
            </div>
        </div>
    </section>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
