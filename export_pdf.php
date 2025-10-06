<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../vendor/autoload.php';  // pastikan path autoload benar

use TCPDF;

// Ambil data pengaduan dari DB (semua atau bisa filter tanggal)
$query = "SELECT * FROM pengaduan ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($conn));
}

// Buat objek PDF baru
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('LaporLingkungan Jogja');
$pdf->SetTitle('Laporan Pengaduan');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 15, 'Laporan Pengaduan Lingkungan', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 12);
$pdf->Ln(4);

// Buat header tabel
$tbl_header = '<table border="1" cellpadding="4">
<tr bgcolor="#cccccc">
    <th width="15%">Kode</th>
    <th width="20%">Kategori</th>
    <th width="25%">Deskripsi</th>
    <th width="20%">Lokasi</th>
    <th width="10%">Status</th>
    <th width="10%">Tanggal</th>
</tr>';

// Isi data tabel
$tbl_rows = '';
while ($row = mysqli_fetch_assoc($result)) {
    $tbl_rows .= '<tr>
        <td>' . htmlspecialchars($row['kode_pengaduan']) . '</td>
        <td>' . htmlspecialchars($row['kategori']) . '</td>
        <td>' . htmlspecialchars(substr($row['deskripsi'], 0, 50)) . '...</td>
        <td>' . htmlspecialchars($row['lokasi']) . '</td>
        <td>' . htmlspecialchars(ucfirst($row['status'])) . '</td>
        <td>' . date('d/m/Y', strtotime($row['tanggal'])) . '</td>
    </tr>';
}

$tbl_footer = '</table>';

$pdf->writeHTML($tbl_header . $tbl_rows . $tbl_footer, true, false, false, false, '');

// Output file PDF (1 = tampil di browser, 0 = download)
$pdf->Output('laporan_pengaduan.pdf', 'I');
