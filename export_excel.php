<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once '../config/database.php';
require_once '../vendor/autoload.php'; // pastikan path ini benar

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Ambil data dari database
$query = "SELECT * FROM pengaduan ORDER BY tanggal DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die('Query Error: ' . mysqli_error($conn));
}

// Buat Spreadsheet baru
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set judul kolom
$sheet->setCellValue('A1', 'Kode Pengaduan')
      ->setCellValue('B1', 'Kategori')
      ->setCellValue('C1', 'Deskripsi')
      ->setCellValue('D1', 'Lokasi')
      ->setCellValue('E1', 'Status')
      ->setCellValue('F1', 'Tanggal');

// Isi data dari DB
$rowNum = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNum, $row['kode_pengaduan']);
    $sheet->setCellValue('B' . $rowNum, $row['kategori']);
    $sheet->setCellValue('C' . $rowNum, substr($row['deskripsi'], 0, 100));
    $sheet->setCellValue('D' . $rowNum, $row['lokasi']);
    $sheet->setCellValue('E' . $rowNum, ucfirst($row['status']));
    $sheet->setCellValue('F' . $rowNum, date('d/m/Y H:i', strtotime($row['tanggal'])));
    $rowNum++;
}

// Atur header untuk output Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="laporan_pengaduan.xlsx"');
header('Cache-Control: max-age=0');

// Tulis file ke output
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
