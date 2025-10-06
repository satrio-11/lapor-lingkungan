<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once "config/database.php";

// Edit Kategori
if (isset($_POST['edit_id']) && isset($_POST['edit_nama'])) {
    $id = intval($_POST['edit_id']);
    $nama = mysqli_real_escape_string($conn, $_POST['edit_nama']);
    mysqli_query($conn, "UPDATE kategori SET nama='$nama' WHERE id=$id");
    header("Location: admin_kategori.php?msg=edited");
    exit;
}

// Tambah Kategori
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama']) && $_POST['nama'] != '' && !isset($_POST['edit_id'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    mysqli_query($conn, "INSERT INTO kategori (nama) VALUES ('$nama')");
    header("Location: admin_kategori.php?msg=added");
    exit;
}

// Hapus Kategori
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM kategori WHERE id=$id");
    header("Location: admin_kategori.php?msg=deleted");
    exit;
}

// Ambil data untuk edit jika ada ?edit
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_nama = '';
if ($edit_id) {
    $rowEdit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM kategori WHERE id=$edit_id"));
    if ($rowEdit) $edit_nama = $rowEdit['nama'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Manajemen Kategori | Admin - LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="assets/img/logolingkungan.png" type="image/png" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/style_admin_kategori.css" />
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
            Manajemen Kategori Pengaduan
        </h2>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='added'): ?>
          <div class="alert-kat sukses">Kategori berhasil ditambah!</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
          <div class="alert-kat hapus">Kategori dihapus!</div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='edited'): ?>
          <div class="alert-kat sukses">Kategori berhasil diupdate!</div>
        <?php endif; ?>

        <!-- Form Tambah / Edit -->
        <form method="post" class="kategori-form">
            <input type="text" name="<?= $edit_id ? 'edit_nama' : 'nama' ?>" placeholder="Kategori..." required value="<?= htmlspecialchars($edit_nama) ?>">
            <?php if ($edit_id): ?>
                <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                <button type="submit" class="btn orange">Update</button>
                <a href="admin_kategori.php" class="btn batal">Batal</a>
            <?php else: ?>
                <button type="submit" class="btn orange">Tambah</button>
            <?php endif; ?>
        </form>

        <div style="overflow-x:auto">
            <table class="kategori-table">
                <thead>
                    <tr>
                        <th>Nama Kategori</th>
                        <th style="text-align:center;width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id DESC");
                if (mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='2' style='text-align:center;color:#aaa;padding:30px 0;'>Belum ada kategori.</td></tr>";
                }
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['nama']) . "</td>
                        <td class='aksi-link' style='text-align:center'>
                          <a href='admin_kategori.php?edit={$row['id']}' class='edit'>Edit</a>
                          <a href='admin_kategori.php?del={$row['id']}' class='hapus' onclick=\"return confirm('Hapus kategori?');\">Hapus</a>
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
