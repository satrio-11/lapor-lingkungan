<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once "config/database.php";

// Tandai pesan sudah dibaca
if (isset($_GET['mark_read'])) {
    $id = intval($_GET['mark_read']);
    mysqli_query($conn, "UPDATE inbox SET status='dibaca' WHERE id=$id");
    header("Location: admin_inbox.php");
    exit;
}

// Ambil semua pesan inbox
$q = mysqli_query($conn, "SELECT * FROM inbox ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Pesan Inbox | Admin - LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/admin_inbox.css" />
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
    <h2>Inbox Pesan</h2>

    <table class="inbox-table" role="table" aria-label="Daftar pesan inbox">
        <thead>
            <tr>
                <th>Pengirim</th>
                <th>Email</th>
                <th>Pesan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($q) === 0): ?>
            <tr><td colspan="6" style="text-align:center;color:#888;">Belum ada pesan.</td></tr>
        <?php else: ?>
            <?php while ($row = mysqli_fetch_assoc($q)): ?>
            <tr>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td style="max-width: 350px; white-space: pre-wrap;"><?= htmlspecialchars($row['pesan']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                <td>
                    <?= $row['status'] === 'baru' 
                        ? '<span class="badge-baru">BARU</span>' 
                        : '<span style="color:#268a54;font-weight:700;">Dibaca</span>' ?>
                </td>
                <td class="aksi-link">
                    <?php if ($row['status'] === 'baru'): ?>
                        <a href="admin_inbox.php?mark_read=<?= $row['id'] ?>" class="btn-mark-read" onclick="return confirm('Tandai pesan sudah dibaca?');">Tandai Dibaca</a>
                    <?php else: ?>
                        <span style="color:#888;font-style:italic;">-</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
