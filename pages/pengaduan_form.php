<?php
include "../includes/header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
require_once "../config/database.php";

// Ambil kategori & lokasi dinamis
$kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY nama ASC");
$lokasi = mysqli_query($conn, "SELECT * FROM lokasi ORDER BY nama ASC");

// Proses submit pengaduan
$pesanSukses = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user']['id'];
    $kode = 'YGL' . rand(100000,999999);
    $kategori_ = mysqli_real_escape_string($conn, $_POST['kategori']);
    $lokasi_   = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status    = 'belum'; // status default pengaduan baru
    $created   = date('Y-m-d H:i:s');
    $lampiran  = '';

    // Upload file jika ada
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['lampiran']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','pdf'];
        if (in_array($ext, $allowed)) {
            $lampiran = uniqid().'.'.$ext;
            move_uploaded_file($_FILES['lampiran']['tmp_name'], "../uploads/".$lampiran);
        }
    }

    // Simpan ke database
    $sql = "INSERT INTO pengaduan (user_id, kode_pengaduan, kategori, lokasi, deskripsi, status, tanggal, lampiran) 
            VALUES ('$user_id', '$kode', '$kategori_', '$lokasi_', '$deskripsi', '$status', '$created', '$lampiran')";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $pesanSukses = "Pengaduan berhasil dikirim! Kode Pengaduan: <b>$kode</b>";
    } else {
        $pesanSukses = "<span style='color:red'>Terjadi kesalahan, coba lagi.</span>";
    }
}
?>

<div class="main-content" style="max-width:600px;margin:46px auto 0;">
    <section class="pengaduan-form" style="background:#f6faff;border-radius:18px;box-shadow:0 4px 16px #c3d7ff36;padding:38px 30px 30px;">
        <h2 style="text-align:center;color:#234b95;font-size:2rem;font-weight:900;margin-bottom:13px;">Formulir Pengaduan</h2>
        <?php if ($pesanSukses): ?>
        <div class="auto-hide-alert" style="background:#eafbe6;color:#198754;font-weight:700;padding:13px 25px;border-radius:9px;margin-bottom:18px;text-align:center;">
            <?= $pesanSukses ?>
        </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data">
            <div style="margin-bottom:18px;">
                <label for="kategori" style="font-weight:600;color:#223b7d;">Kategori Masalah</label>
                <select name="kategori" id="kategori" required style="width:100%;padding:10px;border-radius:10px;border:1.5px solid #b6bfe2;">
                    <option value="">-- Pilih Kategori --</option>
                    <?php
                    mysqli_data_seek($kategori, 0);
                    while($k = mysqli_fetch_assoc($kategori)): ?>
                        <option value="<?= htmlspecialchars($k['nama']) ?>"><?= htmlspecialchars($k['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="margin-bottom:18px;">
                <label for="lokasi" style="font-weight:600;color:#223b7d;">Lokasi</label>
                <select name="lokasi" id="lokasi" required style="width:100%;padding:10px;border-radius:10px;border:1.5px solid #b6bfe2;">
                    <option value="">-- Pilih Lokasi --</option>
                    <?php
                    mysqli_data_seek($lokasi, 0);
                    while($l = mysqli_fetch_assoc($lokasi)): ?>
                        <option value="<?= htmlspecialchars($l['nama']) ?>"><?= htmlspecialchars($l['nama']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div style="margin-bottom:18px;">
                <label for="deskripsi" style="font-weight:600;color:#223b7d;">Deskripsi Laporan</label>
                <textarea name="deskripsi" id="deskripsi" required rows="4" style="width:100%;padding:10px;border-radius:10px;border:1.5px solid #b6bfe2;"></textarea>
            </div>
            <div style="margin-bottom:18px;">
                <label for="lampiran" style="font-weight:600;color:#223b7d;">Lampiran (opsional)</label><br>
                <input type="file" name="lampiran" id="lampiran" accept=".jpg,.jpeg,.png,.pdf" style="margin-top:6px;">
                <small style="color:#777;">Maks: 2MB. File gambar/pdf saja.</small>
                <div id="lampiranLabel" style="font-size:.95rem;color:#666;margin-top:3px;"></div>
            </div>
            <button type="submit" class="btn orange" style="width:150px;margin-top:10px;font-size:1.11rem;">Kirim Laporan</button>
        </form>
    </section>
</div>

<?php include "../includes/footer.php"; ?>
