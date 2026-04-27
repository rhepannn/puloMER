<?php
require_once 'include/config.php';
require_once 'include/functions.php';

$pageTitle = 'Data Umum PKK Pulomerak';
include 'include/header.php';

$query = "SELECT * FROM kelurahan ORDER BY nama ASC";
$result = $conn->query($query);
?>

<!-- PAGE HERO -->
<section class="hero compact" style="background-image: url('<?= SITE_URL ?>/assets/img/1.png') !important; background-size: cover; background-position: center;">
    <div class="hero-overlay" style="background: linear-gradient(90deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.4) 100%);"></div>
    <div class="container hero-content animate-fade-up">
        <div class="breadcrumb" style="margin-bottom: 5px; font-size: 0.8rem; display: flex; align-items: center; gap: 8px;">
            <a href="<?= SITE_URL ?>/" style="color: rgba(255,255,255,0.8);">Beranda</a> <i class="fas fa-chevron-right" style="font-size: 0.6rem; color: rgba(255,255,255,0.5);"></i>
            <span style="color: #fff; font-weight: 600;">Data Umum</span>
        </div>
        <h1 style="color: white; font-size: 2.2rem; font-weight: 800; margin: 0;">Data <span>Umum & Statistik</span></h1>
        <p style="color: rgba(255,255,255,0.9); max-width: 600px; margin-top: 10px;">Rekapitulasi data kependudukan dan organisasi PKK se-Kecamatan Pulomerak tahun 2025.</p>
    </div>
</section>

<section class="section" style="background: #f8fafc; padding-top: 40px;">
    <div class="container">
        <!-- TABS NAV -->
        <div style="display: flex; gap: 10px; margin-bottom: 30px; overflow-x: auto; padding-bottom: 10px;">
            <button onclick="switchTab('umum')" class="tab-btn active" id="btn-umum"><i class="fas fa-users"></i> Data Umum</button>
            <button onclick="switchTab('kesehatan')" class="tab-btn" id="btn-kesehatan"><i class="fas fa-heartbeat"></i> Kesehatan</button>
            <button onclick="switchTab('lingkungan')" class="tab-btn" id="btn-lingkungan"><i class="fas fa-leaf"></i> Lingkungan</button>
        </div>

        <div style="background: white; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; overflow: hidden;" class="reveal">
            <div style="padding: 25px 30px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; background: #fff;">
                <h3 style="margin: 0; color: var(--primary); font-weight: 800;" id="table-title"><i class="fas fa-table" style="margin-right: 10px;"></i> Tabel Rekapitulasi Wilayah</h3>
            </div>
            
            <!-- TAB: UMUM -->
            <div id="tab-umum" class="tab-content active">
                <div style="overflow-x: auto;">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama Lingkungan / RW</th>
                                <th colspan="3">Wilayah / Organisasi</th>
                                <th colspan="2">Rumah Tangga</th>
                                <th colspan="3">Jumlah Penduduk (Jiwa)</th>
                            </tr>
                            <tr>
                                <th>Lingk</th>
                                <th>RT</th>
                                <th>Dasa Wisma</th>
                                <th>KRT</th>
                                <th>KK</th>
                                <th>L</th>
                                <th>P</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $result->data_seek(0);
                            $no = 1;
                            $totals = ['link' => 0, 'rt' => 0, 'dw' => 0, 'krt' => 0, 'kk' => 0, 'l' => 0, 'p' => 0, 'tot' => 0];
                            while($row = $result->fetch_assoc()): 
                                $totals['link'] += $row['jumlah_link']; $totals['rt'] += $row['jumlah_rt']; $totals['dw'] += $row['dasa_wisma'];
                                $totals['krt'] += $row['jumlah_krt']; $totals['kk'] += $row['jumlah_kk'];
                                $totals['l'] += $row['penduduk_l']; $totals['p'] += $row['penduduk_p']; $totals['tot'] += $row['penduduk'];
                            ?>
                            <tr>
                                <td style="text-align: center; color: #64748b; font-weight: 600;"><?= $no++ ?></td>
                                <td style="font-weight: 800; color: var(--primary);"><?= e($row['nama']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['jumlah_link']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['jumlah_rt']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['dasa_wisma']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['jumlah_krt']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['jumlah_kk']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['penduduk_l']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['penduduk_p']) ?></td>
                                <td style="text-align: center; font-weight: 900; background: #f8fafc; color: var(--primary);"><?= number_format($row['penduduk']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot style="background: #f1f5f9; font-weight: 900; color: var(--primary);">
                            <tr>
                                <td colspan="2" style="text-align: center;">TOTAL KECAMATAN</td>
                                <td style="text-align: center;"><?= number_format($totals['link']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals['rt']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals['dw']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals['krt']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals['kk']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals['l']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals['p']) ?></td>
                                <td style="text-align: center; background: var(--primary); color: white;"><?= number_format($totals['tot']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- TAB: KESEHATAN -->
            <div id="tab-kesehatan" class="tab-content">
                <div style="overflow-x: auto;">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th rowspan="2">No</th>
                                <th rowspan="2">Nama Lingkungan / RW</th>
                                <th colspan="3">Kondisi Ibu</th>
                                <th colspan="3">Status Anak</th>
                                <th colspan="3">Lain-lain</th>
                            </tr>
                            <tr>
                                <th>Hamil</th>
                                <th>Menyusui</th>
                                <th>Nifas</th>
                                <th>Lahir</th>
                                <th>Meninggal</th>
                                <th>Akte Ada</th>
                                <th>PUS</th>
                                <th>WUS</th>
                                <th>Lansia</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $result->data_seek(0);
                            $no = 1;
                            $totals_kes = ['hamil'=>0, 'susu'=>0, 'nifas'=>0, 'lahir'=>0, 'mati'=>0, 'akte'=>0, 'pus'=>0, 'wus'=>0, 'lansia'=>0];
                            while($row = $result->fetch_assoc()): 
                                $lahir = ($row['bayi_lahir_l']+$row['bayi_lahir_p']);
                                $mati = ($row['bayi_meninggal_l']+$row['bayi_meninggal_p']+$row['balita_meninggal_l']+$row['balita_meninggal_p']);
                                $totals_kes['hamil'] += $row['ibu_hamil']; $totals_kes['susu'] += $row['ibu_menyusui']; $totals_kes['nifas'] += $row['ibu_nifas'];
                                $totals_kes['lahir'] += $lahir; $totals_kes['mati'] += $mati; $totals_kes['akte'] += $row['akte_ada'];
                                $totals_kes['pus'] += $row['pus']; $totals_kes['wus'] += $row['wus']; $totals_kes['lansia'] += $row['lansia'];
                            ?>
                            <tr>
                                <td style="text-align: center; color: #64748b;"><?= $no++ ?></td>
                                <td style="font-weight: 800; color: var(--primary);"><?= e($row['nama']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['ibu_hamil']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['ibu_menyusui']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['ibu_nifas']) ?></td>
                                <td style="text-align: center;"><?= number_format($lahir) ?></td>
                                <td style="text-align: center; color: #e11d48;"><?= number_format($mati) ?></td>
                                <td style="text-align: center;"><?= number_format($row['akte_ada']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['pus']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['wus']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['lansia']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot style="background: #fff1f2; font-weight: 900; color: #9f1239;">
                            <tr>
                                <td colspan="2" style="text-align: center;">TOTAL KESEHATAN</td>
                                <td style="text-align: center;"><?= number_format($totals_kes['hamil']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['susu']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['nifas']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['lahir']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['mati']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['akte']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['pus']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['wus']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_kes['lansia']) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- TAB: LINGKUNGAN -->
            <div id="tab-lingkungan" class="tab-content">
                <div style="overflow-x: auto;">
                    <table class="premium-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Lingkungan / RW</th>
                                <th>Rumah Sehat</th>
                                <th>R. Krg Sehat</th>
                                <th>Tpt Sampah</th>
                                <th>Jamban</th>
                                <th>Air Bersih</th>
                                <th>Mkn Pokok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $result->data_seek(0);
                            $no = 1;
                            $totals_env = ['sehat'=>0, 'ksehat'=>0, 'sampah'=>0, 'jamban'=>0, 'air'=>0];
                            while($row = $result->fetch_assoc()): 
                                $totals_env['sehat'] += $row['rumah_sehat']; $totals_env['ksehat'] += $row['rumah_kurang_sehat'];
                                $totals_env['sampah'] += $row['sampah']; $totals_env['jamban'] += $row['jamban']; $totals_env['air'] += $row['air_bersih'];
                            ?>
                            <tr>
                                <td style="text-align: center; color: #64748b;"><?= $no++ ?></td>
                                <td style="font-weight: 800; color: var(--primary);"><?= e($row['nama']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['rumah_sehat']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['rumah_kurang_sehat']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['sampah']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['jamban']) ?></td>
                                <td style="text-align: center;"><?= number_format($row['air_bersih']) ?></td>
                                <td style="text-align: center;"><?= e($row['makanan_pokok'] ?: '-') ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot style="background: #f0fdf4; font-weight: 900; color: #166534;">
                            <tr>
                                <td colspan="2" style="text-align: center;">TOTAL LINGKUNGAN</td>
                                <td style="text-align: center;"><?= number_format($totals_env['sehat']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_env['ksehat']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_env['sampah']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_env['jamban']) ?></td>
                                <td style="text-align: center;"><?= number_format($totals_env['air']) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        
        <div style="margin-top: 40px; text-align: center;">
            <p style="color: #64748b; font-size: 0.9rem; font-style: italic;">* Data diperbarui secara berkala oleh masing-masing pengurus Kelurahan/RW.</p>
        </div>
    </div>
</section>

<script>
function switchTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    
    // Show selected
    document.getElementById('tab-' + tabId).classList.add('active');
    document.getElementById('btn-' + tabId).classList.add('active');
    
    // Update title
    const titles = {
        'umum': '<i class="fas fa-users"></i> Tabel Rekapitulasi Umum',
        'kesehatan': '<i class="fas fa-heartbeat"></i> Tabel Rekapitulasi Kesehatan',
        'lingkungan': '<i class="fas fa-leaf"></i> Tabel Rekapitulasi Lingkungan'
    };
    document.getElementById('table-title').innerHTML = titles[tabId];
}
</script>

<style>
.tab-btn { padding: 12px 25px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; color: #64748b; font-weight: 700; cursor: pointer; transition: all 0.3s; white-space: nowrap; }
.tab-btn i { margin-right: 8px; }
.tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 10px 20px rgba(15, 23, 42, 0.1); }
.tab-btn:hover:not(.active) { background: #f1f5f9; }

.tab-content { display: none; }
.tab-content.active { display: block; }

.premium-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
        
        <div style="margin-top: 40px; text-align: center;">
            <p style="color: #64748b; font-size: 0.9rem; font-style: italic;">* Data diperbarui secara berkala oleh masing-masing pengurus Kelurahan/RW.</p>
        </div>
    </div>
</section>

<style>
.premium-table { width: 100%; border-collapse: collapse; min-width: 1000px; }
.premium-table thead th { background: #f8fafc; color: #475569; padding: 15px; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; border: 1px solid #e2e8f0; font-weight: 800; }
.premium-table tbody td { padding: 15px; border: 1px solid #f1f5f9; font-size: 0.95rem; }
.premium-table tbody tr:hover { background: #f1f5f9; }

@media print {
    .page-hero, .mobile-bottom-nav, .footer, .btn-info-sm, .breadcrumb { display: none !important; }
    .section { padding: 0 !important; background: white !important; }
    .container { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
    .premium-table { font-size: 0.7rem !important; }
    .premium-table th, .premium-table td { padding: 8px !important; }
    body { background: white !important; }
}
</style>

<?php include 'include/footer.php'; ?>
