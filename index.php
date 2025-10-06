
<?php
include "includes/header.php"; // Sudah otomatis memuat <header> dan navbar
require_once "config/database.php";
?>

<div class="hero">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <h1 style="margin-bottom:8px;">
    Jogja LaporLingkungan!
    </h1>
    <p>Portal Pengaduan Lingkungan Yogyakarta<br>
        Laporkan masalah seperti <b>pencemaran</b>, <b>sampah</b>, atau <b>penebangan liar</b>, langsung dari warga untuk instansi terkait.<br>
        <span style="color:var(--secondary);font-weight:700;">Jaga Jogja, Mulai Dari Aksi Kecilmu!</span>
    </p>
    <div class="track-section">
        <form action="index.php" method="get" style="display:contents;" autocomplete="off">
            <input type="text" name="kode" id="trackInput" placeholder="Masukkan Kode Pengaduan (misal: YGL123456)">
            <button class="btn orange" type="submit">Lacak Aduan</button>
        </form>
    </div>
    <?php if (isset($_GET['kode']) && trim($_GET['kode']) != ""): ?>
        <div id="trackResult" style="margin-top:20px;">
            <?php
            $kode = mysqli_real_escape_string($conn, $_GET['kode']);
            $q = mysqli_query($conn, "SELECT * FROM pengaduan WHERE kode_pengaduan='$kode' LIMIT 1");
            if (mysqli_num_rows($q) > 0) {
                $d = mysqli_fetch_assoc($q);
                echo "<div style=\"background:#eef4ff;border-radius:14px;padding:16px 26px;display:inline-block;border:1px solid #b6bfe2;max-width:360px;text-align:left;margin:12px auto;font-size:1.06rem;\">
                        <div style='font-size:1.1rem;color:#2851b4;font-weight:600;margin-bottom:7px;'>Kode: {$d['kode_pengaduan']}</div>
                        <b>Kategori:</b> " . htmlspecialchars($d['kategori']) . "<br>
                        <b>Deskripsi:</b> " . htmlspecialchars($d['deskripsi']) . "<br>
                        <b>Lokasi:</b> " . htmlspecialchars($d['lokasi']) . "<br>
                        <b>Status:</b> 
                            <span class=\"" . ($d['status']=='Selesai'?'status-selesai':'status-belum') . "\" 
                                style='padding:2px 13px;border-radius:7px;font-weight:600;"
                                . ($d['status']=='Selesai'?'background:#56efb829;color:#198754;':'background:#ffd19e;color:#d18500;') . "'>
                                {$d['status']}
                            </span>
                        <br>
                        <b>Tanggal:</b> " . date('d/m/Y', strtotime($d['created_at'])) . "<br>";
                if (!empty($d['lampiran'])) {
                    echo "<b>Lampiran:</b><br><img src='uploads/{$d['lampiran']}' alt='Lampiran Aduan' style='max-width:130px;border-radius:9px;margin:6px 0 2px;box-shadow:0 1px 6px #bcc9e966;'><br>";
                }
                echo "</div>";
            } else {
                echo "<span style=\"color:var(--secondary);font-weight:600;background:#f6d6d6;padding:8px 17px;border-radius:9px;\">Kode pengaduan tidak ditemukan.</span>";
            }
            ?>
        </div>
    <?php endif; ?>
</div>

<div class="main-content">
    <!-- Tentang -->
    <section class="pengaduan-form" style="margin-top:38px;">
        <h2>Apa Itu LaporLingkungan?</h2>
        <p style="text-align:center;color:#435;">
            <b>LaporLingkungan Jogja</b> adalah platform terbuka bagi warga untuk melaporkan isu lingkungan di Yogyakarta. Semua laporan langsung diteruskan ke dinas terkait secara transparan dan cepat.  
        </p>
<div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:22px;max-width:930px;margin:36px auto 0;">
    <div class="fitur-card">
        <img src="https://cdn-icons-png.flaticon.com/512/1532/1532876.png" alt="Kategori">
        <div style="font-weight:700;font-size:1.08rem;margin:14px 0 7px;">Kategori Masalah Lengkap</div>
        <div style="color:#555;">Sampah, pencemaran, penebangan liar, dan lainnya.</div>
    </div>
    <div class="fitur-card">
        <img src="https://cdn-icons-png.flaticon.com/512/2060/2060411.png" alt="Formulir">
        <div style="font-weight:700;font-size:1.08rem;margin:14px 0 7px;">Formulir Pengaduan Mudah</div>
        <div style="color:#555;">Isi laporan tanpa ribet. Bisa tambah foto/lampiran.</div>
    </div>
    <div class="fitur-card">
        <img src="https://cdn-icons-png.flaticon.com/512/709/709579.png" alt="Tracking">
        <div style="font-weight:700;font-size:1.08rem;margin:14px 0 7px;">Lacak Status Aduan</div>
        <div style="color:#555;">Setiap laporan ada status: belum, diproses, atau selesai.</div>
    </div>
</div>

    </section>
    <!-- Formulir Pengaduan -->
    <section class="pengaduan-form" style="margin-top:34px;">
        <h2>Mulai Aksi: Laporkan Masalah!</h2>
        <p style="text-align:center;color:#435;">
            Klik tombol di bawah untuk mengisi formulir pengaduan lingkungan.<br>
            <a href="pages/pengaduan_form.php" class="btn orange" style="margin-top:16px;">Isi Formulir Pengaduan</a>
        </p>
    </section>
<!-- Statistik Laporan dengan Chart -->
<section class="daftar-aduan" style="margin-top:40px;">
    <h2>Statistik Laporan</h2>
    <div style="display:flex;gap:28px;flex-wrap:wrap;justify-content:center;">
        <!-- Pie/Donut Chart Selesai vs Belum -->
        <div style="background:#f6faff;padding:22px 18px;border-radius:14px;text-align:center;min-width:320px;box-shadow:0 2px 8px #e7f2ff90;">
            <canvas id="pieChart" width="240" height="240"></canvas>
            <div style="margin-top:10px;">
                <span style="display:inline-block;width:14px;height:14px;background:#44e87e1a;border-radius:4px;border:1px solid #44e87e;margin-right:7px;"></span> Selesai: <b style="color:#189451;"><?php
                    $pieSelesai = mysqli_query($conn, "SELECT COUNT(*) as jml FROM pengaduan WHERE status='Selesai'");
                    $pieSelesaiCount = mysqli_fetch_assoc($pieSelesai)['jml'];
                    echo $pieSelesaiCount;
                ?></b>
                <br>
                <span style="display:inline-block;width:14px;height:14px;background:#ffd19e;border-radius:4px;border:1px solid #ffb347;margin-right:7px;"></span> Belum Selesai: <b style="color:#e39b15;"><?php
                    $pieBelum = mysqli_query($conn, "SELECT COUNT(*) as jml FROM pengaduan WHERE status!='Selesai'");
                    $pieBelumCount = mysqli_fetch_assoc($pieBelum)['jml'];
                    echo $pieBelumCount;
                ?></b>
            </div>
        </div>
        <!-- Bar Chart Per Tanggal -->
        <div style="background:#f7f9fa;padding:22px 18px;border-radius:14px;min-width:390px;box-shadow:0 2px 8px #e7f2ff80;">
            <canvas id="barChart" width="350" height="240"></canvas>
            <div style="font-size:.99rem;margin-top:8px;color:#555;">Jumlah laporan masuk per tanggal</div>
        </div>
    </div>
</section>

<?php
// Data laporan per tanggal (bar chart)
$barQuery = mysqli_query($conn, "SELECT DATE(created_at) as tgl, COUNT(*) as jml FROM pengaduan GROUP BY DATE(created_at) ORDER BY tgl ASC");
$barLabels = [];
$barData = [];
while ($r = mysqli_fetch_assoc($barQuery)) {
    $barLabels[] = date('d/m/Y', strtotime($r['tgl']));
    $barData[] = $r['jml'];
}
?>
<script>
    // Pie Chart Selesai vs Belum
    const pieData = {
        labels: ["Selesai", "Belum Selesai"],
        datasets: [{
            data: [<?= $pieSelesaiCount ?>, <?= $pieBelumCount ?>],
            backgroundColor: [
                "#44e87e", // Selesai
                "#ffb347"  // Belum
            ],
            borderWidth: 2,
            borderColor: "#fff"
        }]
    };
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: pieData,
        options: {
            cutout: "65%",
            plugins: {
                legend: { display: false }
            }
        }
    });

    // Bar Chart Laporan per Tanggal
    const barLabels = <?= json_encode($barLabels) ?>;
    const barData = <?= json_encode($barData) ?>;
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: barLabels,
            datasets: [{
                label: "Jumlah Laporan",
                data: barData,
                backgroundColor: "#3c85e6cc",
                borderRadius: 10,
            }]
        },
        options: {
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { grid: { display: false }, title: { display: false }},
                y: { beginAtZero:true, ticks: { stepSize: 1 }, title: { display: false }}
            }
        }
    });
</script>

<?php include "includes/footer.php"; ?>
