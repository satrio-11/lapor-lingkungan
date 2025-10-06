<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once "config/database.php";

// Proses aktif/nonaktif user
if (isset($_GET['toggle']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $getUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT status FROM users WHERE id=$id"));
    if ($getUser) {
        $newStatus = ($getUser['status'] === 'aktif') ? 'nonaktif' : 'aktif';
        mysqli_query($conn, "UPDATE users SET status='$newStatus' WHERE id=$id");
        header("Location: kelola_user.php?msg=status");
        exit;
    }
}

// (Opsional) Hapus User
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM users WHERE id=$id");
    header("Location: kelola_user.php?msg=deleted");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Manajemen User | Admin - LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="assets/img/logolingkungan.png" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body class="admin">

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
        <h2 style="text-align:center;color:#22314a;font-weight:900;font-size:2rem;margin-bottom:20px;letter-spacing:.5px;">
            Manajemen User
        </h2>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='status'): ?>
            <div class="alert-kat sukses">Status user berhasil diubah.</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
            <div class="alert-kat hapus">User dihapus!</div>
        <?php endif; ?>

        <div style="overflow-x:auto;">
            <table class="kategori-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="text-align:center;width:160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM users ORDER BY id DESC");
                $no = 1;
                if (mysqli_num_rows($q)==0) {
                    echo "<tr><td colspan='6' style='text-align:center;color:#aaa;padding:30px 0;'>Belum ada user.</td></tr>";
                }
                while ($row = mysqli_fetch_assoc($q)) {
                    $statusLabel = $row['status'] == 'aktif'
                        ? "<span class='badge' style='background:#eafbe6;color:#208d44;'>Aktif</span>"
                        : "<span class='badge' style='background:#ffeaea;color:#c84b4b;'>Nonaktif</span>";

                    // Tombol toggle status
                    $toggleBtn = $row['status'] == 'aktif'
                        ? "<a href='kelola_user.php?toggle=1&id={$row['id']}' class='btn' style='background:#c84b4b;color:#fff;font-weight:700;padding:7px 19px;border-radius:8px;font-size:0.97rem;margin-right:9px;' onclick=\"return confirm('Nonaktifkan user ini?');\">Nonaktifkan</a>"
                        : "<a href='kelola_user.php?toggle=1&id={$row['id']}' class='btn orange' style='font-size:0.97rem;font-weight:700;padding:7px 19px;margin-right:9px;' onclick=\"return confirm('Aktifkan kembali user ini?');\">Aktifkan</a>";

                    // Tombol hapus (opsional, bisa dihilangkan)
                    $delBtn = "<a href='kelola_user.php?del={$row['id']}' class='btn' style='background:#bbb;color:#22314a;padding:7px 17px;border-radius:8px;font-size:0.97rem;' onclick=\"return confirm('Hapus user ini?');\">Hapus</a>";

                    echo "<tr>
                        <td>{$no}</td>
                        <td>" . htmlspecialchars($row['nama']) . "</td>
                        <td>" . htmlspecialchars($row['email']) . "</td>
                        <td style='text-transform:capitalize;'>" . htmlspecialchars($row['role']) . "</td>
                        <td>$statusLabel</td>
                        <td style='text-align:center;'>{$toggleBtn}{$delBtn}</td>
                    </tr>";
                    $no++;
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
</body>
</html>
