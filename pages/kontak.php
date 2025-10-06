<?php
include "../includes/header.php";
require_once "../config/database.php";

$pesanTerkirim = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama  = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');

    if ($nama && $email && $pesan) {
        // Amankan input ke DB
        $nama_db = mysqli_real_escape_string($conn, $nama);
        $email_db = mysqli_real_escape_string($conn, $email);
        $pesan_db = mysqli_real_escape_string($conn, $pesan);

        // Simpan ke tabel inbox dengan status 'baru' dan waktu sekarang
        $sql = "INSERT INTO inbox (nama, email, pesan, status, tanggal) 
                VALUES ('$nama_db', '$email_db', '$pesan_db', 'baru', NOW())";

        if (mysqli_query($conn, $sql)) {
            $pesanTerkirim = true;
        } else {
            echo "<div style='color:red; text-align:center;'>Terjadi kesalahan: " . mysqli_error($conn) . "</div>";
        }
    } else {
        echo "<div style='color:red; text-align:center;'>Semua kolom wajib diisi.</div>";
    }
}
?>

<div class="main-content" style="max-width:700px;margin:46px auto 0;">
    <section class="kontak-section" style="background:#f6faff;border-radius:18px;box-shadow:0 4px 16px #c3d7ff36;padding:38px 30px 30px;">
        <h2 style="text-align:center;color:#234b95;font-size:2rem;font-weight:900;letter-spacing:.5px;margin-bottom:14px;">
            Hubungi Kami
        </h2>
        <p style="text-align:center;color:#444;margin-bottom:34px;">
            Punya pertanyaan, kritik, atau saran? Tim <b>LaporLingkungan Jogja</b> siap membantu!<br>
            Silakan kirim pesan lewat form di bawah atau kontak resmi kami.
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:28px;">
            <!-- Form Kontak -->
            <form action="" method="post" style="display:flex;flex-direction:column;gap:14px;">
                <input type="text" name="nama" placeholder="Nama Anda" required style="padding:11px 12px;border-radius:9px;border:1.5px solid #b6bfe2;">
                <input type="email" name="email" placeholder="Email Aktif" required style="padding:11px 12px;border-radius:9px;border:1.5px solid #b6bfe2;">
                <textarea name="pesan" placeholder="Tulis pesan, kritik, atau saran..." rows="5" required style="padding:12px;border-radius:11px;border:1.5px solid #b6bfe2;resize:vertical;"></textarea>
                <button type="submit" class="btn orange" style="width:140px;margin-top:7px;font-size:1.07rem;">Kirim Pesan</button>
            </form>
            <!-- Kontak & Sosial -->
            <div style="padding:10px 0 0 6px;">
                <div style="margin-bottom:19px;">
                    <span style="font-weight:600;color:#233b7b;">Email Resmi:</span><br>
                    <a href="mailto:laporlingkungan.jogja@gmail.com" style="color:var(--secondary);text-decoration:none;font-weight:600;">laporlingkungan.jogja@gmail.com</a>
                </div>
                <div style="margin-bottom:19px;">
                    <span style="font-weight:600;color:#233b7b;">Telepon/WA:</span><br>
                    <a href="https://wa.me/6281234567890" style="color:var(--secondary);text-decoration:none;font-weight:600;">+62 812-3456-7890</a>
                </div>
                <div style="margin-bottom:21px;">
                    <span style="font-weight:600;color:#233b7b;">Instagram:</span><br>
                    <a href="https://instagram.com/laporlingkungan.jogja" target="_blank" style="color:var(--secondary);text-decoration:none;font-weight:600;">@laporlingkungan.jogja</a>
                </div>
                <div>
                    <span style="font-weight:600;color:#233b7b;">Alamat:</span><br>
                    <span style="color:#444;">Jl. Contoh Raya No. 123, Sleman, Yogyakarta</span>
                </div>
            </div>
        </div>
        <?php if ($pesanTerkirim): ?>
        <div style="margin-top:26px;text-align:center;">
            <div class="auto-hide-alert" style="display:inline-block;background:#e9fbe6;color:#198754;padding:12px 30px;border-radius:10px;font-weight:700;box-shadow:0 2px 9px #d7ffd042;">
                Terima kasih, pesan Anda telah dikirim!
            </div>
        </div>
        <?php endif; ?>
    </section>
</div>

<?php include "../includes/footer.php"; ?>
