<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;

// Fungsi untuk menandai menu aktif
$current = basename($_SERVER['SCRIPT_NAME']);
function navActive($page) {
    global $current;
    return $current == $page ? 'active' : '';
}

// Deteksi halaman admin (file diawali admin_ atau dashboard_admin.php di root)
$isAdminPage = preg_match('/^(admin_.*|dashboard_admin\.php)$/', $current);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Base path supaya asset & link tetap aman tanpa folder project -->
    <base href="/" />

    <!-- Favicon -->
    <link rel="icon" href="assets/img/logolingkungan.png" type="image/png" />

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">

    <!-- CSS utama -->
    <link rel="stylesheet" href="assets/css/style.css" />

    <!-- JS utama -->
    <script src="assets/js/main.js" defer></script>
</head>
<body<?= (isset($hideHeader) && $hideHeader === true) ? ' class="hide-header"' : '' ?>>

<?php if ($isAdminPage && $isLoggedIn && $user['role'] === 'admin'): ?>
    <!-- HEADER KHUSUS ADMIN -->
    <header class="admin-header">
        <div class="logo">
            <img src="assets/img/logolingkungann.png" alt="Logo LaporLingkungan" />
            LaporLingkungan <span>Jogja</span>
        </div>
        <nav>
            <a href="/dashboard_admin.php" class="btn orange" style="margin-right:18px;<?= navActive('dashboard_admin.php') ? ' font-weight:800;' : '' ?>">Dashboard Admin</a>
            <a href="logout.php" class="btn orange">Logout</a>
        </nav>
    </header>
<?php else: ?>
    <!-- HEADER UNTUK USER / UMUM -->
    <header>
        <div class="logo">
            <img src="assets/img/logolingkungann.png" alt="Logo LaporLingkungan" />
            LaporLingkungan <span>Jogja</span>
        </div>
        <nav>
            <a href="index.php" class="<?= navActive('index.php') ?>">Home</a>
            <a href="pages/pengaduan_form.php" class="<?= navActive('pengaduan_form.php') ?>">Formulir</a>
            <a href="pages/daftar_pengaduan.php" class="<?= navActive('daftar_pengaduan.php') ?>">Daftar</a>
            <a href="pages/kontak.php" class="<?= navActive('kontak.php') ?>">Kontak</a>
            <a href="pages/faq.php" class="<?= navActive('faq.php') ?>">FAQ</a>
            <a href="pages/cara.php" class="<?= navActive('cara.php') ?>">Cara</a>
            
            <?php if ($isLoggedIn): ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="/dashboard_admin.php" class="<?= navActive('dashboard_admin.php') ?>">Dashboard Admin</a>
                <?php else: ?>
                    <a href="pages/dashboard_user.php" class="<?= navActive('dashboard_user.php') ?>">Dashboard</a>
                <?php endif; ?>

                <a href="logout.php" class="btn orange nav-btn">Logout</a>
                <div class="profile-menu" tabindex="0">
                    <div class="dropdown-content">
                        <a href="pages/profile.php">Profil Saya</a>
                        <a href="logout.php" class="btn orange nav-btn">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="pages/login.php" class="btn orange">Login / Register</a>
            <?php endif; ?>
        </nav>
    </header>
<?php endif; ?>
