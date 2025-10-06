<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();

include "../includes/header.php";

// Jika sudah login, langsung redirect
if (isset($_SESSION['user'])) {
    header("Location: " . ($_SESSION['user']['role'] === 'admin' ? 'dashboard_admin.php' : 'dashboard_user.php'));
    exit;
}
?>

<div class="form-card">
    <h2>Login Akun</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div id="formAlert" style="margin-bottom:15px; color: #b23b3b; font-weight:700;">
            <?= htmlspecialchars($_GET['msg']) ?>
        </div>
    <?php endif; ?>

    <form action="../proses/proses_login.php" method="post" autocomplete="off">
        <label for="username">Username / Email</label>
        <input type="text" name="username" id="username" placeholder="Masukkan username/email" required>

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="Masukkan password" required>

        <button type="submit" class="btn orange">Login</button>
    </form>

    <div style="text-align:center; margin-top: 20px;">
        Belum punya akun?
        <a href="/pages/register.php" style="color:var(--secondary);font-weight:600;">
            Daftar di sini
        </a>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
