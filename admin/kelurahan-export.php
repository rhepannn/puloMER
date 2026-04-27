<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    if (isSuperAdmin()) {
        // Jika superadmin tidak pilih ID, redirect ke daftar
        redirect(SITE_URL . '/admin/kelurahan.php');
    } else {
        $id = getKelurahanId();
    }
}

// Cek Kepemilikan
checkOwnership($id);

$stmt = $conn->prepare("SELECT * FROM kelurahan WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$kel = $stmt->get_result()->fetch_assoc();

if (!$kel) {
    setFlash('error', 'Data tidak ditemukan.');
    redirect(SITE_URL . '/admin/index.php');
}

$format = $_GET['format'] ?? 'pdf';

// ============================================================
// EXCEL EXPORT (CSV/Tab Delimited)
// ============================================================
if ($format === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_PKK_" . str_replace(' ', '_', $kel['nama']) . "_" . date('Y-m-d') . ".xls");
    
    echo "LAPORAN REKAPITULASI DATA PKK\n";
    echo "WILAYAH: " . strtoupper($kel['nama']) . "\n";
    echo "TANGGAL CETAK: " . date('d/m/Y') . "\n\n";

    echo "KATEGORI\tINDIKATOR\tLAKI-LAKI\tPEREMPUAN\tTOTAL\n";
    
    // Kependudukan
    echo "Kependudukan\tJumlah Penduduk\t" . ($kel['penduduk_l']??0) . "\t" . ($kel['penduduk_p']??0) . "\t" . ($kel['penduduk']??0) . "\n";
    echo "Wilayah\tJumlah RW\t-\t-\t" . ($kel['jumlah_rw']??0) . "\n";
    echo "Wilayah\tJumlah RT\t-\t-\t" . ($kel['jumlah_rt']??0) . "\n";
    echo "Wilayah\tJumlah Link\t-\t-\t" . ($kel['jumlah_link']??0) . "\n";
    echo "Wilayah\tDasa Wisma\t-\t-\t" . ($kel['dasa_wisma']??0) . "\n";
    echo "Rumah Tangga\tJumlah KRT\t-\t-\t" . ($kel['jumlah_krt']??0) . "\n";
    echo "Rumah Tangga\tJumlah KK\t-\t-\t" . ($kel['jumlah_kk']??0) . "\n\n";

    // Kesehatan & Kependudukan
    echo "Kesehatan\tIbu Hamil\t-\t-\t" . ($kel['ibu_hamil']??0) . "\n";
    echo "Kesehatan\tIbu Menyusui\t-\t-\t" . ($kel['ibu_menyusui']??0) . "\n";
    echo "Kesehatan\tIbu Melahirkan\t-\t-\t" . ($kel['ibu_melahirkan']??0) . "\n";
    echo "Kesehatan\tIbu Nifas\t-\t-\t" . ($kel['ibu_nifas']??0) . "\n";
    echo "Kesehatan\tIbu Meninggal\t-\t-\t" . ($kel['ibu_meninggal']??0) . "\n";
    echo "Kependudukan\tPUS\t-\t-\t" . ($kel['pus']??0) . "\n";
    echo "Kependudukan\tWUS\t-\t-\t" . ($kel['wus']??0) . "\n";
    echo "Kependudukan\tLansia\t-\t-\t" . ($kel['lansia']??0) . "\n";
    echo "Kependudukan\t3 Buta\t-\t-\t" . ($kel['buta']??0) . "\n\n";

    // Anak & Kematian
    echo "Anak\tBayi Lahir\t" . ($kel['bayi_lahir_l']??0) . "\t" . ($kel['bayi_lahir_p']??0) . "\t" . (($kel['bayi_lahir_l']??0)+($kel['bayi_lahir_p']??0)) . "\n";
    echo "Anak\tAkte Ada\t-\t-\t" . ($kel['akte_ada']??0) . "\n";
    echo "Anak\tAkte Tidak Ada\t-\t-\t" . ($kel['akte_tidak']??0) . "\n";
    echo "Kematian\tBayi Meninggal (0-1 Th)\t" . ($kel['bayi_meninggal_l']??0) . "\t" . ($kel['bayi_meninggal_p']??0) . "\t" . (($kel['bayi_meninggal_l']??0)+($kel['bayi_meninggal_p']??0)) . "\n";
    echo "Kematian\tBalita Meninggal (1-5 Th)\t" . ($kel['balita_meninggal_l']??0) . "\t" . ($kel['balita_meninggal_p']??0) . "\t" . (($kel['balita_meninggal_l']??0)+($kel['balita_meninggal_p']??0)) . "\n\n";

    // Lingkungan
    echo "Lingkungan\tRumah Sehat\t-\t-\t" . ($kel['rumah_sehat']??0) . "\n";
    echo "Lingkungan\tRumah Kurang Sehat\t-\t-\t" . ($kel['rumah_kurang_sehat']??0) . "\n";
    echo "Lingkungan\tTempat Sampah\t-\t-\t" . ($kel['sampah']??0) . "\n";
    echo "Lingkungan\tJamban\t-\t-\t" . ($kel['jamban']??0) . "\n";
    echo "Lingkungan\tAir Bersih\t-\t-\t" . ($kel['air_bersih']??0) . "\n";
    echo "Lingkungan\tMakanan Pokok\t-\t-\t" . ($kel['makanan_pokok']?:"-") . "\n";
    
    exit;
}

// ============================================================
// PDF VIEW (HTML Print Optimized)
// ============================================================
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan PKK - <?= e($kel['nama']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --primary: #1e3a8a; --secondary: #f1c40f; }
        body { font-family: 'Times New Roman', Times, serif; color: #333; line-height: 1.6; margin: 0; padding: 40px; background: #f1f5f9; }
        .page { background: white; padding: 60px; max-width: 900px; margin: 0 auto; box-shadow: 0 0 20px rgba(0,0,0,0.1); position: relative; }
        
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 20px; margin-bottom: 30px; position: relative; }
        .header h1 { margin: 0; font-size: 1.6rem; text-transform: uppercase; }
        .header h2 { margin: 5px 0 0; font-size: 1.3rem; text-transform: uppercase; color: var(--primary); }
        .header p { margin: 5px 0 0; font-size: 0.95rem; font-style: italic; }
        
        .logo { position: absolute; top: 0; left: 0; width: 80px; }
        
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px; font-size: 1rem; }
        .info-table td.label { width: 150px; font-weight: bold; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 12px 15px; text-align: left; }
        .data-table th { background: #f8fafc; font-weight: bold; text-transform: uppercase; font-size: 0.9rem; }
        .data-table tr.group { background: #eff6ff; font-weight: bold; }
        .data-table .text-center { text-align: center; }
        
        .footer-sign { margin-top: 60px; display: flex; justify-content: flex-end; }
        .sign-box { text-align: center; width: 250px; }
        .sign-space { height: 80px; }
        
        .no-print { position: fixed; top: 20px; right: 20px; z-index: 100; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
        .btn-print { background: var(--primary); color: white; }
        .btn-excel { background: #166534; color: white; }
        .btn-back { background: #64748b; color: white; }

        @media print {
            body { background: white; padding: 0; }
            .page { box-shadow: none; padding: 0; max-width: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="kelurahan.php" class="btn btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
    <a href="?id=<?= $id ?>&format=excel" class="btn btn-excel"><i class="fas fa-file-excel"></i> Download Excel</a>
    <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak ke PDF</button>
</div>

<div class="page">
    <div class="header">
        <img src="<?= SITE_URL ?>/assets/img/pkk_logo.png" class="logo" alt="PKK Logo">
        <h1>TIM PENGGERAK PKK KECAMATAN PULOMERAK</h1>
        <h2>LAPORAN DATA REKAPITULASI WILAYAH <?= strtoupper($kel['nama']) ?></h2>
        <p>Alamat: Jl. Raya Merak No. 1, Pulomerak, Kota Cilegon</p>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Wilayah</td>
            <td>: Kelurahan <?= e($kel['nama']) ?></td>
            <td class="label">Tanggal Laporan</td>
            <td>: <?= date('d F Y') ?></td>
        </tr>
        <tr>
            <td class="label">Ketua TP PKK</td>
            <td>: <?= e($kel['ketua_pkk'] ?: '-') ?></td>
            <td class="label">Periode</td>
            <td>: <?= date('Y') ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" class="text-center" style="width: 50px;">No</th>
                <th rowspan="2">Indikator Laporan</th>
                <th colspan="2" class="text-center">Jenis Kelamin</th>
                <th rowspan="2" class="text-center">Total</th>
            </tr>
            <tr>
                <th class="text-center">L</th>
                <th class="text-center">P</th>
            </tr>
        </thead>
        <tbody>
            <!-- DATA UMUM -->
            <tr class="group">
                <td class="text-center">I</td>
                <td colspan="4">DATA UMUM & WILAYAH</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Jumlah RW (Lingkungan)</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center"><?= number_format($kel['jumlah_rw']??0) ?></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Jumlah Rukun Tetangga (RT)</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center"><?= number_format($kel['jumlah_rt']??0) ?></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Jumlah Kelompok Dasa Wisma</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center"><?= number_format($kel['dasa_wisma']??0) ?></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Jumlah Penduduk Terdaftar</td>
                <td class="text-center"><?= number_format($kel['penduduk_l']??0) ?></td>
                <td class="text-center"><?= number_format($kel['penduduk_p']??0) ?></td>
                <td class="text-center"><strong><?= number_format($kel['penduduk']??0) ?></strong></td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Jumlah KRT (Kepala Rumah Tangga)</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center"><?= number_format($kel['jumlah_krt']??0) ?></td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td>Jumlah KK (Kepala Keluarga)</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center"><?= number_format($kel['jumlah_kk']??0) ?></td>
            </tr>

            <!-- KESEHATAN & KEPENDUDUKAN -->
            <tr class="group">
                <td class="text-center">II</td>
                <td colspan="4">DATA KESEHATAN & KEPENUDUKAN</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Jumlah Ibu Hamil / Menyusui</td>
                <td class="text-center"><?= number_format($kel['ibu_hamil']??0) ?> <small>Hamil</small></td>
                <td class="text-center"><?= number_format($kel['ibu_menyusui']??0) ?> <small>Susu</small></td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Ibu Nifas / Meninggal</td>
                <td class="text-center"><?= number_format($kel['ibu_nifas']??0) ?> <small>Nifas</small></td>
                <td class="text-center" style="color:red;"><?= number_format($kel['ibu_meninggal']??0) ?> <small>Wafat</small></td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>PUS / WUS</td>
                <td class="text-center"><?= number_format($kel['pus']??0) ?> <small>PUS</small></td>
                <td class="text-center"><?= number_format($kel['wus']??0) ?> <small>WUS</small></td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Lansia / 3 Buta</td>
                <td class="text-center"><?= number_format($kel['lansia']??0) ?> <small>Lansia</small></td>
                <td class="text-center"><?= number_format($kel['buta']??0) ?> <small>3 Buta</small></td>
                <td class="text-center">-</td>
            </tr>

            <!-- ANAK & KEMATIAN -->
            <tr class="group">
                <td class="text-center">III</td>
                <td colspan="4">DATA KELAHIRAN & KEMATIAN ANAK</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Jumlah Kelahiran Bayi</td>
                <td class="text-center"><?= number_format($kel['bayi_lahir_l']??0) ?></td>
                <td class="text-center"><?= number_format($kel['bayi_lahir_p']??0) ?></td>
                <td class="text-center"><?= number_format(($kel['bayi_lahir_l']??0)+($kel['bayi_lahir_p']??0)) ?></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Kepemilikan Akte Kelahiran (Ada)</td>
                <td class="text-center">-</td>
                <td class="text-center">-</td>
                <td class="text-center"><?= number_format($kel['akte_ada']??0) ?></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Kematian Bayi (0 - 1 Tahun)</td>
                <td class="text-center"><?= number_format($kel['bayi_meninggal_l']??0) ?></td>
                <td class="text-center"><?= number_format($kel['bayi_meninggal_p']??0) ?></td>
                <td class="text-center" style="color:red;"><?= number_format(($kel['bayi_meninggal_l']??0)+($kel['bayi_meninggal_p']??0)) ?></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Kematian Balita (1 - 5 Tahun)</td>
                <td class="text-center"><?= number_format($kel['balita_meninggal_l']??0) ?></td>
                <td class="text-center"><?= number_format($kel['balita_meninggal_p']??0) ?></td>
                <td class="text-center" style="color:red;"><?= number_format(($kel['balita_meninggal_l']??0)+($kel['balita_meninggal_p']??0)) ?></td>
            </tr>

            <!-- LINGKUNGAN -->
            <tr class="group">
                <td class="text-center">IV</td>
                <td colspan="4">DATA LINGKUNGAN & KRITERIA RUMAH</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Rumah Sehat / Kurang Sehat</td>
                <td class="text-center"><?= number_format($kel['rumah_sehat']??0) ?> <small>Sehat</small></td>
                <td class="text-center"><?= number_format($kel['rumah_kurang_sehat']??0) ?> <small>Krg</small></td>
                <td class="text-center">-</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Fasilitas (Sampah / Jamban / Air)</td>
                <td class="text-center"><?= number_format($kel['sampah']??0) ?> <small>Smph</small></td>
                <td class="text-center"><?= number_format($kel['jamban']??0) ?> <small>Jmbn</small></td>
                <td class="text-center"><?= number_format($kel['air_bersih']??0) ?> <small>Air</small></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Makanan Pokok</td>
                <td colspan="2">-</td>
                <td class="text-center"><strong><?= e($kel['makanan_pokok']?:"-") ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="footer-sign">
        <div class="sign-box">
            <p>Pulomerak, <?= date('d F Y') ?></p>
            <p>Ketua TP PKK Kelurahan,</p>
            <div class="sign-space"></div>
            <p><strong>( <?= e($kel['ketua_pkk'] ?: '...........................') ?> )</strong></p>
        </div>
    </div>
</div>

</body>
</html>
