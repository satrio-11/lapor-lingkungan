<?php
include "../includes/header.php";
?>

<div class="main-content" style="max-width:820px;margin:46px auto 0;">
    <section class="cara-section" style="background:#f6faff;border-radius:18px;box-shadow:0 4px 16px #c3d7ff36;padding:38px 30px 30px;">
        <h2 style="text-align:center;color:#234b95;font-size:2rem;font-weight:900;letter-spacing:.5px;margin-bottom:12px;">
            Cara Menggunakan <span style="color:var(--secondary);">LaporLingkungan Jogja</span>
        </h2>
        <p style="text-align:center;color:#444;margin-bottom:36px;font-size:1.09rem;">
            Ikuti langkah mudah berikut untuk melaporkan masalah lingkungan di sekitarmu.<br>
            <span style="color:var(--secondary);font-weight:700;">Aksi kecilmu, dampak besar untuk Jogja!</span>
        </p>
        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:26px;max-width:720px;margin:0 auto;">
            <!-- Step 1 -->
            <div class="fitur-card">
                <div style="font-weight:800;font-size:1.13rem;color:#223b7d;margin-bottom:8px;">1. Buka Formulir</div>
                <div style="color:#444;font-size:1.05rem;">
                    Klik tombol <b>Isi Formulir Pengaduan</b> di halaman utama atau menu Formulir.<br>
                    Lengkapi data diri dan deskripsi masalah lingkungan.
                </div>
            </div>
            <!-- Step 2 -->
            <div class="fitur-card">
                <div style="font-weight:800;font-size:1.13rem;color:#223b7d;margin-bottom:8px;">2. Upload Bukti</div>
                <div style="color:#444;font-size:1.05rem;">
                    (Opsional) Upload foto/lampiran pendukung.<br>
                    Pastikan file jelas dan relevan.
                </div>
            </div>
            <!-- Step 3 -->
            <div class="fitur-card">
                <div style="font-weight:800;font-size:1.13rem;color:#223b7d;margin-bottom:8px;">3. Kirim & Lacak</div>
                <div style="color:#444;font-size:1.05rem;">
                    Setelah submit, kamu akan dapat <b>kode pengaduan</b>.<br>
                    Gunakan fitur <b>Lacak Aduan</b> untuk memantau status laporanmu.
                </div>
            </div>
        </div>
        <div style="margin-top:38px;text-align:center;">
            <a href="/pages/pengaduan_form.php" class="btn orange" style="font-size:1.09rem;padding:13px 34px;">
                Isi Formulir Pengaduan Sekarang
            </a>
        </div>
    </section>
</div>

<?php include "../includes/footer.php"; ?>
