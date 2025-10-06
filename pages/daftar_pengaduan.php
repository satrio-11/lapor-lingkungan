<?php
include "../includes/header.php";
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
require_once "../config/database.php";

// Ambil data user dari session
$user = $_SESSION['user'];
$isAdmin = ($user['role'] === 'admin');
?>

<div class="main-content" style="max-width:980px;margin:38px auto 0;">
    <section class="daftar-aduan">
        <h2>Daftar Pengaduan <?= $isAdmin ? 'Seluruh User' : 'Saya' ?></h2>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <?php if ($isAdmin): ?>
                            <th>User</th>
                        <?php endif; ?>
                        <th>Kategori</th>
                        <th>Deskripsi</th>
                        <th>Lokasi</th>
                        <th>Lampiran</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($isAdmin) {
                    $sql = "SELECT p.*, u.nama, u.username FROM pengaduan p JOIN users u ON p.user_id=u.id ORDER BY p.id DESC";
                } else {
                    $uid = mysqli_real_escape_string($conn, $user['id']);
                    $sql = "SELECT * FROM pengaduan WHERE user_id='$uid' ORDER BY id DESC";
                }
                $q = mysqli_query($conn, $sql);
                if (mysqli_num_rows($q) > 0) {
                    while ($row = mysqli_fetch_assoc($q)) {
                        echo "<tr>";
                        // Kode sebagai link ke detail
                        echo "<td>
                            <a href='detail_pengaduan.php?kode={$row['kode_pengaduan']}' style='color:#2851b4;font-weight:600;text-decoration:none;'>
                                {$row['kode_pengaduan']}
                            </a>
                        </td>";
                        // Kolom user khusus admin
                        if ($isAdmin) {
                            echo "<td><div style='font-weight:600;'>{$row['nama']}</div><div style='font-size:0.9rem;color:#666;'>{$row['username']}</div></td>";
                        }
                        echo "<td>" . htmlspecialchars($row['kategori']) . "</td>";
                        echo "<td style='max-width:250px;white-space:pre-wrap;'>" . htmlspecialchars($row['deskripsi']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lokasi']) . "</td>";
                        echo "<td>";
                        if (!empty($row['lampiran'])) {
                            echo "<a href='../uploads/{$row['lampiran']}' target='_blank' style='color:var(--primary);font-weight:600;'>Lihat</a>";
                        } else {
                            echo "-";
                        }
                        echo "</td>";
                        echo "<td><span class='" . ($row['status'] == 'Selesai' ? 'status-selesai' : 'status-belum') . "'>{$row['status']}</span></td>";
                        echo "<td>" . date('d/m/Y', strtotime($row['created_at'])) . "</td>";
                        // Kolom Aksi (Tombol Detail)
                        echo "<td>
                            <a href='pages/detail_pengaduan.php?kode={$row['kode_pengaduan']}' class='btn orange' style='padding:2px 13px;font-size:.93rem;'>Detail</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    $colspan = $isAdmin ? 9 : 8;
                    echo "<tr><td colspan='$colspan' style='text-align:center;color:#888;'>Belum ada pengaduan.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>
</div>

<?php include "../includes/footer.php"; ?>
