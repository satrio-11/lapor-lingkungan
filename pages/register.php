<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();

include "../includes/header.php";

// Redirect jika sudah login (user tidak bisa daftar ulang)
if (isset($_SESSION['user'])) {
    header("Location: " . ($_SESSION['user']['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_user.php'));
    exit;
}
?>

<div class="form-card">
    <h2>Daftar Akun Baru</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div id="formAlert"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>

    <form action="../proses/proses_register.php" method="POST" autocomplete="off">
        <label for="nama_lengkap">Nama Lengkap</label>
        <input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Nama lengkap" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Alamat email aktif" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username unik" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>

        <label for="konfirmasi">Konfirmasi Password</label>
        <input type="password" id="konfirmasi" name="konfirmasi" placeholder="Konfirmasi Password" required>

        <button type="submit" class="btn orange">Daftar</button>
    </form>

    <div style="text-align:center; margin-top: 20px;">
        Sudah punya akun?
        <a href="/pages/login.php" style="color:var(--secondary);font-weight:600;">
            Login di sini
        </a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
