<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php'; // ✅ PENTING! JANGAN ../

// Tidak perlu "use TCPDF;" karena kita langsung pakai \TCPDF
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fungsi format tanggal Indonesia
function tgl_indo($tanggal) {
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[(int)date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    return "$tgl $bln $thn";
}

if (isset($_POST['export_excel']) || isset($_POST['export_pdf'])) {
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';

    if (!$startDate || !$endDate) {
        die("Tanggal mulai dan akhir harus diisi.");
    }

    $stmt = $conn->prepare("SELECT * FROM pengaduan WHERE DATE(tanggal) BETWEEN ? AND ? ORDER BY tanggal ASC");
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    if (isset($_POST['export_excel'])) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Kode Pengaduan')
              ->setCellValue('B1', 'Kategori')
              ->setCellValue('C1', 'Deskripsi')
              ->setCellValue('D1', 'Lokasi')
              ->setCellValue('E1', 'Status')
              ->setCellValue('F1', 'Tanggal');

        $rowNum = 2;
        while ($row = $result->fetch_assoc()) {
            $sheet->setCellValue('A' . $rowNum, $row['kode_pengaduan']);
            $sheet->setCellValue('B' . $rowNum, $row['kategori']);
            $sheet->setCellValue('C' . $rowNum, $row['deskripsi']);
            $sheet->setCellValue('D' . $rowNum, $row['lokasi']);
            $sheet->setCellValue('E' . $rowNum, ucfirst($row['status']));
            $sheet->setCellValue('F' . $rowNum, date('d-m-Y H:i', strtotime($row['tanggal'])));
            $rowNum++;
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="laporan_pengaduan_'.$startDate.'_sd_'.$endDate.'.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    if (isset($_POST['export_pdf'])) {
        $pdf = new \TCPDF();
        $pdf->SetCreator('LaporLingkungan Jogja');
        $pdf->SetAuthor('LaporLingkungan');
        $pdf->SetTitle('Laporan Pengaduan');
        $pdf->SetMargins(15, 20, 15);
        $pdf->AddPage();

        $html = '<h2 style="text-align:center;">Laporan Pengaduan</h2>';
        $html .= '<p style="text-align:center;">Periode: '.tgl_indo($startDate).' s/d '.tgl_indo($endDate).'</p>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse; width:100%;">';
        $html .= '<thead>
                    <tr style="background-color:#f2f2f2;">
                        <th><b>Kode</b></th>
                        <th><b>Kategori</b></th>
                        <th><b>Deskripsi</b></th>
                        <th><b>Lokasi</b></th>
                        <th><b>Status</b></th>
                        <th><b>Tanggal</b></th>
                    </tr>
                  </thead><tbody>';

        while ($row = $result->fetch_assoc()) {
            $html .= '<tr>
                        <td>'.$row['kode_pengaduan'].'</td>
                        <td>'.$row['kategori'].'</td>
                        <td>'.htmlspecialchars(substr($row['deskripsi'], 0, 50)).'...</td>
                        <td>'.$row['lokasi'].'</td>
                        <td>'.ucfirst($row['status']).'</td>
                        <td>'.date('d-m-Y H:i', strtotime($row['tanggal'])).'</td>
                      </tr>';
        }
        $html .= '</tbody></table>';
        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('laporan_pengaduan_'.$startDate.'_sd_'.$endDate.'.pdf', 'I');
        exit;
    }
}
?>

<!-- Tampilan HTML -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Export Data | LaporLingkungan Jogja</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="admin">
<header class="admin-header">
    <div class="logo">
        <img src="assets/img/logolingkungann.png" alt="Logo LaporLingkungan">
        LaporLingkungan <span>Jogja</span>
    </div>
    <nav>
        <a href="dashboard_admin.php" class="btn orange">Dashboard Admin</a>
        <a href="logout.php" class="btn orange">Logout</a>
    </nav>
</header>

<div class="main-content">
    <section style="background:#f6faff;border-radius:18px;box-shadow:0 4px 16px #c3d7ff36;padding:30px 40px;">
        <h2 style="text-align:center;color:#234b95;font-size:1.9rem;font-weight:800;margin-bottom:22px;">
            Export Data Pengaduan Periode Tanggal
        </h2>
        <form method="post" style="max-width:440px;margin:0 auto; display:flex; flex-wrap:wrap; gap:12px; justify-content:center;">
            <input type="date" name="start_date" required style="padding:11px 13px; border-radius:10px; border:1.5px solid #b6bfe2; font-size:1rem; flex:1;">
            <input type="date" name="end_date" required style="padding:11px 13px; border-radius:10px; border:1.5px solid #b6bfe2; font-size:1rem; flex:1;">
            <button type="submit" name="export_excel" class="btn orange" style="flex-basis: 100%; margin-top: 6px;">Export ke Excel</button>
            <button type="submit" name="export_pdf" class="btn orange" style="flex-basis: 100%;">Export ke PDF</button>
        </form>
    </section>
</div>
</body>
</html>
