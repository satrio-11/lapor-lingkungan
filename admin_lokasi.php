<?php
include "includes/header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
require_once "config/database.php";

// Edit Lokasi
if (isset($_POST['edit_id']) && isset($_POST['edit_nama'])) {
    $id = intval($_POST['edit_id']);
    $nama = mysqli_real_escape_string($conn, $_POST['edit_nama']);
    mysqli_query($conn, "UPDATE lokasi SET nama='$nama' WHERE id=$id");
    header("Location: admin_lokasi.php?msg=edited");
    exit;
}

// Tambah Lokasi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nama']) && $_POST['nama'] != '' && !isset($_POST['edit_id'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    mysqli_query($conn, "INSERT INTO lokasi (nama) VALUES ('$nama')");
    header("Location: admin_lokasi.php?msg=added");
    exit;
}

// Hapus Lokasi
if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM lokasi WHERE id=$id");
    header("Location: admin_lokasi.php?msg=deleted");
    exit;
}

// Ambil data untuk edit jika ada ?edit
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_nama = '';
if ($edit_id) {
    $rowEdit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM lokasi WHERE id=$edit_id"));
    if ($rowEdit) $edit_nama = $rowEdit['nama'];
}
?>

<div class="main-content" style="max-width: 600px; margin: 50px auto;">
    <div style="background: #fff; border-radius: 18px; box-shadow: 0 2px 16px #b3c5ec25; padding: 40px 30px 32px;">
        <h2 style="text-align:center;color:#22314a;font-weight:900;font-size:2rem;margin-bottom:20px;letter-spacing:.5px;">
            Manajemen Lokasi Pengaduan
        </h2>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='added'): ?>
            <div style="background:#eafbe6;padding:10px 17px;border-radius:8px;color:#198754;font-weight:700;margin-bottom:17px;text-align:center;">
                Lokasi berhasil ditambah!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='deleted'): ?>
            <div style="background:#ffeaea;padding:10px 17px;border-radius:8px;color:#d12020;font-weight:700;margin-bottom:17px;text-align:center;">
                Lokasi dihapus!
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg']=='edited'): ?>
            <div style="background:#eafbe6;padding:10px 17px;border-radius:8px;color:#2b8a3e;font-weight:700;margin-bottom:17px;text-align:center;">
                Lokasi berhasil diupdate!
            </div>
        <?php endif; ?>

        <!-- Form Tambah / Edit -->
        <form method="post" style="margin-bottom:22px;display:flex;gap:12px;justify-content:center;">
            <input type="text" name="<?= $edit_id ? 'edit_nama' : 'nama' ?>" placeholder="Lokasi..." required
                style="flex:1;padding:12px 15px;font-size:1.08rem;border-radius:10px;border:1.5px solid #b6bfe2;background:#f7faff;">
            <?php if ($edit_id): ?>
                <input type="hidden" name="edit_id" value="<?= $edit_id ?>">
                <button type="submit" class="btn orange" style="padding:10px 30px;">Update</button>
                <a href="admin_lokasi.php" class="btn" style="background:#bbb;padding:10px 28px;">Batal</a>
            <?php else: ?>
                <button type="submit" class="btn orange" style="padding:10px 36px;">Tambah</button>
            <?php endif; ?>
        </form>

        <div style="overflow-x:auto;">
            <table style="width:100%;margin-top:8px;border-radius:10px;overflow:hidden;box-shadow:0 1px 8px #eaf3fe55;">
                <thead>
                    <tr style="background:#eaf3fe;">
                        <th style="width:72%;padding:13px 8px;text-align:left;font-size:1.06rem;color:#234b95;letter-spacing:.4px;">Nama Lokasi</th>
                        <th style="text-align:center;font-size:1.06rem;color:#234b95;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $q = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY id DESC");
                if (mysqli_num_rows($q) == 0) {
                    echo "<tr><td colspan='2' style='text-align:center;color:#aaa;padding:32px 0;'>Belum ada lokasi.</td></tr>";
                }
                while ($row = mysqli_fetch_assoc($q)) {
                    echo "<tr>
                        <td style='padding:11px 8px;font-weight:600;color:#22314a;'>" . htmlspecialchars($row['nama']) . "</td>
                        <td style='text-align:center;'>
                            <a href='admin_lokasi.php?edit={$row['id']}' style='color:#1577d2;font-weight:700;margin-right:15px;'>Edit</a>
                            <a href='admin_lokasi.php?del={$row['id']}' onclick=\"return confirm('Hapus lokasi?');\" style='color:#d12020;font-weight:700;'>Hapus</a>
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
