<?php
include "../includes/header.php";
require_once "../config/database.php";

// Query FAQ dari database (tabel faq)
$q = mysqli_query($conn, "SELECT * FROM faq ORDER BY id ASC");
?>

<div class="main-content" style="max-width:700px;margin:46px auto 0;">
    <section class="faq-section" style="background:#f6faff;border-radius:18px;box-shadow:0 4px 16px #c3d7ff36;padding:38px 30px 30px;">
        <script src="assets/js/main.js" defer></script>
    <h2 style="text-align:center;color:#234b95;font-size:2rem;font-weight:800;letter-spacing:.5px;margin-bottom:12px;">
            FAQ - Pertanyaan yang Sering Ditanyakan
        </h2>
        <p style="text-align:center;color:#444;margin-bottom:30px;">
            Temukan jawaban atas pertanyaan umum seputar <b>LaporLingkungan Jogja</b> di sini.<br>
            Jika masih ada yang ingin ditanyakan, silakan hubungi kami melalui halaman <a href="/pages/kontak.php" style="color:#2851b4;font-weight:600;">Kontak</a>.
        </p>

        <div class="faq-list" style="margin:0 auto;max-width:570px;">
            <?php
            if (mysqli_num_rows($q) === 0) {
                echo '<div style="text-align:center;color:#c15d00;font-weight:600;">Belum ada FAQ ditambahkan oleh admin.</div>';
            } else {
                while ($faq = mysqli_fetch_assoc($q)) {
                    echo '
                    <div class="faq-item" style="margin-bottom:14px;">
                        <button class="faq-question" style="width:100%;text-align:left;padding:15px 17px;border-radius:9px;background:#eaf3fe;font-size:1.09rem;color:#223b7d;font-weight:700;border:none;cursor:pointer;outline:none;transition:background .22s;">
                            ' . htmlspecialchars($faq['pertanyaan']) . '
                        </button>
                        <div class="faq-answer" style="display:none;padding:10px 18px 16px 22px;color:#444;font-size:1.04rem;background:#fff;border-radius:0 0 9px 9px;">
                            ' . nl2br(htmlspecialchars($faq['jawaban'])) . '
                        </div>
                    </div>
                    ';
                }
            }
            ?>
        </div>
    </section>
</div>

<?php include "../includes/footer.php"; ?>
