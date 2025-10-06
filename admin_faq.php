<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once "config/database.php";

// Edit FAQ
if (isset($_POST['edit_id']) && isset($_POST['edit_pertanyaan']) && isset($_POST['edit_jawaban'])) {
    $id = intval($_POST['edit_id']);
    $pertanyaan = mysqli_real_escape_string($conn, $_POST['edit_pertanyaan']);
    $jawaban = mysqli_real_escape_string($conn, $_POST['edit_jawaban']);
    mysqli_query($conn, "UPDATE faq SET pertanyaan='$pertanyaan', jawaban='$jawaban' WHERE id=$id");
    header("Location: admin_faq.php?msg=edited");
    exit;
}

// Tambah FAQ
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pertanyaan']) && isset($_POST['jawaban']) && $_POST['pertanyaan'] != '' && $_POST['jawaban'] != '' && !isset($_POST['edit_id'])) {
    $pertanyaan = mysqli_real_escape_string($conn, $_POST['pertanyaan']);
    $jawaban = mysqli_real_escape_string($conn, $_POST['jawaban']);
    mysqli_query($conn, "INSERT INTO faq (pertanyaan, jawaban) VALUES ('$pertanyaan','$jawaban')");
    header("Location: admin_faq.php?msg=added");
    exit;
}

// Hapus FAQ
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM faq WHERE id=$id");
    header("Location: admin_faq.php?msg=deleted");
    exit;
}

// Ambil data untuk edit jika ada ?edit
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_pertanyaan = $edit_jawaban = '';
if ($edit_id) {
    $rowEdit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM faq WHERE id=$edit_id"));
    if ($rowEdit) {
        $edit_pertanyaan = $rowEdit['pertanyaan'];
        $edit_jawaban = $rowEdit['jawaban'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Manajemen FAQ | Admin - LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="assets/img/logolingkungan.png" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" />
    <!-- Kalau ada, gunakan style_admin_faq.css, atau merge ke style.css -->
    <!-- <link rel="stylesheet" href="assets/css/style_admin_faq.css" /> -->
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
    <div class="kategori-card"><!-- Pakai card biar seragam -->
        <h2 style="text-align:center;color:#22314a;font-weight:900;font-size:2rem;margin-bottom:20px;letter-spacing:.5px;">
            Manajemen FAQ
        </h2>

        <!-- Alert sukses/gagal -->
        <?php if (isset($_GET['msg']) && $_GET['msg']=='added'): ?>
            <div class="alert-kat sukses">FAQ berhasil ditambah!</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
            <div class="alert-kat hapus">FAQ dihapus!</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='edited'): ?>
            <div class="alert-kat sukses">FAQ berhasil diupdate!</div>
        <?php endif; ?>

        <!-- Form Tambah / Edit FAQ -->
        <form method="post" class="kategori-form" style="flex-direction:column;gap:13px;align-items:stretch;">
            <input type="text" name="<?= $edit_id ? 'edit_pertanyaan' : 'pertanyaan' ?>" placeholder="Pertanyaan..." required value="<?= htmlspecialchars($edit_pertanyaan) ?>">
            <input type="text" name="<?= $edit_id ? 'edit_jawaban' : 'jawaban' ?>" placeholder="Jawaban..." required value="<?= htmlspecialchars($edit_jawaban) ?>">
            <div style="display:flex;gap:13px;">
                <?php if ($edit_id): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                    <button type="submit" class="btn orange">Update</button>
                    <a href="admin_faq.php" class="btn batal" style="background:#bbb;">Batal</a>
                <?php else: ?>
                    <button type="submit" class="btn orange">Tambah FAQ</button>
                <?php endif; ?>
            </div>
        </form>

        <div style="overflow-x:auto;">
            <table class="kategori-table">
                <thead>
                    <tr>
                        <th style="width:40%">Pertanyaan</th>
                        <th>Jawaban</th>
                        <th style="text-align:center;width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM faq ORDER BY id DESC");
                if (mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='3' style='text-align:center;color:#aaa;padding:30px 0;'>Belum ada FAQ.</td></tr>";
                }
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['pertanyaan']) . "</td>
                        <td>" . htmlspecialchars($row['jawaban']) . "</td>
                        <td class='aksi-link' style='text-align:center'>
                            <a href='admin_faq.php?edit={$row['id']}' class='edit'>Edit</a>
                            <a href='admin_faq.php?del={$row['id']}' class='hapus' onclick=\"return confirm('Hapus FAQ?');\">Hapus</a>
                        </td>
                    </tr>";
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
