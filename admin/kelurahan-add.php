<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

if (!isSuperAdmin()) {
    setFlash('error', 'Halaman ini hanya dapat diakses oleh Superadmin.');
    redirect(SITE_URL . '/admin/index.php');
}

$pageTitle = 'Tambah Kelurahan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $ketua_pkk  = trim($_POST['ketua_pkk'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $inovasi    = trim($_POST['inovasi'] ?? '');
    $jumlah_rw  = (int)($_POST['jumlah_rw'] ?? 0);
    $jumlah_rt  = (int)($_POST['jumlah_rt'] ?? 0);
    $penduduk   = (int)($_POST['penduduk'] ?? 0);
    $penduduk_l = (int)($_POST['penduduk_l'] ?? 0);
    $penduduk_p = (int)($_POST['penduduk_p'] ?? 0);
    $jumlah_link = (int)($_POST['jumlah_link'] ?? 0);
    $jumlah_krt = (int)($_POST['jumlah_krt'] ?? 0);
    $jumlah_kk  = (int)($_POST['jumlah_kk'] ?? 0);
    $dasa_wisma = (int)($_POST['dasa_wisma'] ?? 0);
    $ibu_hamil  = (int)($_POST['ibu_hamil'] ?? 0);
    $ibu_menyusui = (int)($_POST['ibu_menyusui'] ?? 0);
    $ibu_melahirkan = (int)($_POST['ibu_melahirkan'] ?? 0);
    $ibu_nifas = (int)($_POST['ibu_nifas'] ?? 0);
    $ibu_meninggal = (int)($_POST['ibu_meninggal'] ?? 0);
    $pus        = (int)($_POST['pus'] ?? 0);
    $wus        = (int)($_POST['wus'] ?? 0);
    $lansia     = (int)($_POST['lansia'] ?? 0);
    $buta       = (int)($_POST['buta'] ?? 0);
    $bayi_lahir_l = (int)($_POST['bayi_lahir_l'] ?? 0);
    $bayi_lahir_p = (int)($_POST['bayi_lahir_p'] ?? 0);
    $akte_ada = (int)($_POST['akte_ada'] ?? 0);
    $akte_tidak = (int)($_POST['akte_tidak'] ?? 0);
    $bayi_meninggal_l = (int)($_POST['bayi_meninggal_l'] ?? 0);
    $bayi_meninggal_p = (int)($_POST['bayi_meninggal_p'] ?? 0);
    $balita_meninggal_l = (int)($_POST['balita_meninggal_l'] ?? 0);
    $balita_meninggal_p = (int)($_POST['balita_meninggal_p'] ?? 0);
    $rumah_sehat = (int)($_POST['rumah_sehat'] ?? 0);
    $rumah_kurang_sehat = (int)($_POST['rumah_kurang_sehat'] ?? 0);
    $sampah     = (int)($_POST['sampah'] ?? 0);
    $jamban     = (int)($_POST['jamban'] ?? 0);
    $air_bersih = (int)($_POST['air_bersih'] ?? 0);
    $makanan_pokok = trim($_POST['makanan_pokok'] ?? '');

    $sekretaris_pkk = trim($_POST['sekretaris_pkk'] ?? '');
    $bendahara_pkk  = trim($_POST['bendahara_pkk'] ?? '');
    $pokja1_pkk     = trim($_POST['pokja1_pkk'] ?? '');
    $pokja2_pkk     = trim($_POST['pokja2_pkk'] ?? '');
    $pokja3_pkk     = trim($_POST['pokja3_pkk'] ?? '');
    $pokja4_pkk     = trim($_POST['pokja4_pkk'] ?? '');

    if ($penduduk === 0 && ($penduduk_l > 0 || $penduduk_p > 0)) {
        $penduduk = $penduduk_l + $penduduk_p;
    }

    if (empty($nama)) {
        $error = 'Nama kelurahan wajib diisi!';
    } else {
        $gambar = '';
        $foto_ketua = '';
        $foto_sekretaris = '';
        $foto_bendahara = '';
        $foto_pokja1 = '';
        $foto_pokja2 = '';
        $foto_pokja3 = '';
        $foto_pokja4 = '';

        $uploads = [
            'gambar' => ['dir' => '../uploads/kegiatan'],
            'foto_ketua' => ['dir' => '../uploads/bidang'],
            'foto_sekretaris' => ['dir' => '../uploads/bidang'],
            'foto_bendahara' => ['dir' => '../uploads/bidang'],
            'foto_pokja1' => ['dir' => '../uploads/bidang'],
            'foto_pokja2' => ['dir' => '../uploads/bidang'],
            'foto_pokja3' => ['dir' => '../uploads/bidang'],
            'foto_pokja4' => ['dir' => '../uploads/bidang']
        ];

        foreach($uploads as $key => $upInfo) {
            if(!empty($_FILES[$key]['tmp_name'])){
                $res = uploadFile($_FILES[$key], $upInfo['dir']);
                if(is_array($res) && isset($res['error'])){
                    $error = $res['error']; break;
                } else {
                    ${$key} = $res;
                }
            }
        }

        if(empty($error)){
            $sql = "INSERT INTO kelurahan (
                nama, ketua_pkk, sekretaris_pkk, bendahara_pkk, 
                pokja1_pkk, pokja2_pkk, pokja3_pkk, pokja4_pkk,
                foto_ketua, foto_sekretaris, foto_bendahara, 
                foto_pokja1, foto_pokja2, foto_pokja3, foto_pokja4,
                deskripsi, inovasi, gambar, makanan_pokok,
                jumlah_rw, jumlah_rt, penduduk, penduduk_l, penduduk_p, jumlah_link, dasa_wisma,
                jumlah_krt, jumlah_kk, pus, wus, lansia, buta,
                ibu_hamil, ibu_menyusui, ibu_melahirkan, ibu_nifas, ibu_meninggal,
                bayi_lahir_l, bayi_lahir_p, akte_ada, akte_tidak,
                bayi_meninggal_l, bayi_meninggal_p, balita_meninggal_l, balita_meninggal_p,
                rumah_sehat, rumah_kurang_sehat, sampah, jamban, air_bersih
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('sssssssssssssssssssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiii', 
                    $nama, $ketua_pkk, $sekretaris_pkk, $bendahara_pkk,
                    $pokja1_pkk, $pokja2_pkk, $pokja3_pkk, $pokja4_pkk,
                    $foto_ketua, $foto_sekretaris, $foto_bendahara,
                    $foto_pokja1, $foto_pokja2, $foto_pokja3, $foto_pokja4,
                    $deskripsi, $inovasi, $gambar, $makanan_pokok,
                    $jumlah_rw, $jumlah_rt, $penduduk, $penduduk_l, $penduduk_p, $jumlah_link, $dasa_wisma,
                    $jumlah_krt, $jumlah_kk, $pus, $wus, $lansia, $buta,
                    $ibu_hamil, $ibu_menyusui, $ibu_melahirkan, $ibu_nifas, $ibu_meninggal,
                    $bayi_lahir_l, $bayi_lahir_p, $akte_ada, $akte_tidak,
                    $bayi_meninggal_l, $bayi_meninggal_p, $balita_meninggal_l, $balita_meninggal_p,
                    $rumah_sehat, $rumah_kurang_sehat, $sampah, $jamban, $air_bersih);
                
                if ($stmt->execute()) {
                    setFlash('success', 'Data kelurahan berhasil ditambahkan!');
                    redirect(SITE_URL . '/admin/kelurahan.php');
                } else {
                    $error = 'Gagal menyimpan: ' . $stmt->error;
                }
            } else {
                $error = 'Query Error: ' . $conn->error;
            }
        }
    }
}
include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Wilayah</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Tambah Kelurahan / RW</h2>
        <p class="text-gray-400 text-sm mt-1">Lengkapi data profil dan statistik wilayah baru</p>
    </div>
    <a href="kelurahan.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
        <i class="fas fa-arrow-left text-xs text-accent"></i> Kembali
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6 text-sm font-bold">
    <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
    <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="space-y-8">
    
    <!-- SEKSI 1: IDENTITAS & PENGURUS -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-darkblue_alt px-8 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-id-card"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Identitas & Pengurus PKK</h3>
                <p class="text-gray-400 text-xs mt-0.5">Data dasar wilayah dan personil inti</p>
            </div>
        </div>
        
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Nama Kelurahan / RW <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" required value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Contoh: RW 01 Kampung Merak"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all font-bold">
                </div>

                <!-- Pengurus PKK -->
                <?php 
                $pengurus = [
                    ['ketua_pkk', 'foto_ketua', 'Ketua TP PKK'],
                    ['sekretaris_pkk', 'foto_sekretaris', 'Sekretaris PKK'],
                    ['bendahara_pkk', 'foto_bendahara', 'Bendahara PKK'],
                    ['pokja1_pkk', 'foto_pokja1', 'Ketua POKJA I'],
                    ['pokja2_pkk', 'foto_pokja2', 'Ketua POKJA II'],
                    ['pokja3_pkk', 'foto_pokja3', 'Ketua POKJA III'],
                    ['pokja4_pkk', 'foto_pokja4', 'Ketua POKJA IV'],
                ];
                foreach($pengurus as [$name, $file, $label]): ?>
                <div class="bg-softgray/50 rounded-2xl p-5 border border-gray-100 group hover:border-accent transition-all">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3"><?= $label ?></label>
                    <input type="text" name="<?= $name ?>" value="<?= e($_POST[$name] ?? '') ?>" placeholder="Nama Lengkap"
                           class="w-full bg-white border border-gray-200 text-darkblue_alt rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-accent transition-all mb-3">
                    <div class="relative">
                        <input type="file" name="<?= $file ?>" accept="image/*" class="text-[10px] text-gray-400 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-accent/10 file:text-accent hover:file:bg-accent/20 cursor-pointer w-full">
                    </div>
                </div>
                <?php endforeach; ?>

                <div class="bg-softgray/50 rounded-2xl p-5 border border-gray-100 md:col-span-2">
                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-3">Foto Wilayah / Kantor</label>
                    <input type="file" name="gambar" accept="image/*" class="text-[10px] text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-[11px] file:font-bold file:bg-darkblue_alt file:text-white hover:file:bg-accent cursor-pointer w-full">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Deskripsi Wilayah</label>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan profil singkat wilayah, sejarah, atau keunggulan..."
                              class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all resize-none"><?= e($_POST['deskripsi'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- SEKSI 2: DATA STATISTIK UMUM -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-darkblue_alt px-8 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-chart-line"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Statistik & Kependudukan</h3>
                <p class="text-gray-400 text-xs mt-0.5">Data kuantitatif warga dan rumah tangga</p>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php 
                $stats_umum = [
                    ['jumlah_rw', 'Jumlah RW', 'city'],
                    ['jumlah_rt', 'Jumlah RT', 'home'],
                    ['jumlah_link', 'Lingkungan', 'map-marker-alt'],
                    ['dasa_wisma', 'Dasawisma', 'users'],
                    ['jumlah_krt', 'Jumlah KRT', 'house-user'],
                    ['jumlah_kk', 'Jumlah KK', 'address-card'],
                    ['penduduk_l', 'Warga (L)', 'male'],
                    ['penduduk_p', 'Warga (P)', 'female'],
                ];
                foreach($stats_umum as [$name, $label, $icon]): ?>
                <div class="bg-softgray/30 rounded-2xl p-4 border border-gray-50 group hover:bg-white hover:border-accent transition-all">
                    <div class="flex items-center gap-2 mb-2 text-gray-400 group-hover:text-accent transition-colors">
                        <i class="fas fa-<?= $icon ?> text-[10px]"></i>
                        <label class="text-[10px] font-bold uppercase tracking-wider"><?= $label ?></label>
                    </div>
                    <input type="number" name="<?= $name ?>" value="<?= e($_POST[$name] ?? 0) ?>"
                           class="w-full bg-transparent border-b-2 border-gray-100 text-darkblue_alt text-lg font-black focus:outline-none focus:border-accent transition-all py-1">
                </div>
                <?php endforeach; ?>
                
                <div class="bg-accent/5 rounded-2xl p-4 border border-accent/20 md:col-span-2">
                    <div class="flex items-center gap-2 mb-2 text-accent">
                        <i class="fas fa-calculator text-[10px]"></i>
                        <label class="text-[10px] font-bold uppercase tracking-wider">Total Penduduk (Otomatis)</label>
                    </div>
                    <input type="number" name="penduduk" value="<?= e($_POST['penduduk'] ?? 0) ?>" placeholder="Biarkan 0 untuk hitung otomatis"
                           class="w-full bg-transparent border-b-2 border-accent/30 text-darkblue_alt text-2xl font-black focus:outline-none focus:border-accent transition-all py-1">
                </div>
            </div>
        </div>
    </div>

    <!-- SEKSI 3: KESEHATAN & KELUARGA -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-darkblue_alt px-8 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-pink-500 flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-heartbeat"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Kesehatan & Keluarga</h3>
                <p class="text-gray-400 text-xs mt-0.5">Data PUS, WUS, Lansia, dan Ibu</p>
            </div>
        </div>
        <div class="p-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <?php 
                $stats_health = [
                    ['pus', 'PUS', 'venus-mars'],
                    ['wus', 'WUS', 'venus'],
                    ['lansia', 'Lansia', 'blind'],
                    ['buta', '3 Buta', 'book-dead'],
                    ['ibu_hamil', 'Ibu Hamil', 'baby-carriage'],
                    ['ibu_menyusui', 'Ibu Menyusui', 'baby'],
                    ['ibu_melahirkan', 'Ibu Melahirkan', 'hospital'],
                    ['ibu_nifas', 'Ibu Nifas', 'bed'],
                    ['ibu_meninggal', 'Ibu Meninggal', 'cross'],
                    ['bayi_lahir_l', 'Lahir (L)', 'baby'],
                    ['bayi_lahir_p', 'Lahir (P)', 'baby'],
                    ['akte_ada', 'Akte (Ada)', 'file-alt'],
                    ['akte_tidak', 'Akte (N/A)', 'file-excel'],
                    ['bayi_meninggal_l', 'Bayi Men (L)', 'skull'],
                    ['bayi_meninggal_p', 'Bayi Men (P)', 'skull'],
                    ['balita_meninggal_l', 'Balita Men (L)', 'skull-crossbones'],
                    ['balita_meninggal_p', 'Balita Men (P)', 'skull-crossbones'],
                ];
                foreach($stats_health as [$name, $label, $icon]): ?>
                <div class="bg-softgray/30 rounded-2xl p-4 border border-gray-50 group hover:bg-white hover:border-pink-400 transition-all">
                    <div class="flex items-center gap-2 mb-2 text-gray-400 group-hover:text-pink-400 transition-colors">
                        <i class="fas fa-<?= $icon ?> text-[10px]"></i>
                        <label class="text-[10px] font-bold uppercase tracking-wider"><?= $label ?></label>
                    </div>
                    <input type="number" name="<?= $name ?>" value="<?= e($_POST[$name] ?? 0) ?>"
                           class="w-full bg-transparent border-b-2 border-gray-100 text-darkblue_alt text-lg font-black focus:outline-none focus:border-pink-400 transition-all py-1">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- SEKSI 4: LINGKUNGAN & INOVASI -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-darkblue_alt px-8 py-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-leaf"></i>
            </div>
            <div>
                <h3 class="text-white font-bold text-lg">Lingkungan & Inovasi</h3>
                <p class="text-gray-400 text-xs mt-0.5">Data rumah sehat dan program unggulan</p>
            </div>
        </div>
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-5 gap-6">
                <?php 
                $stats_env = [
                    ['rumah_sehat', 'Rumah Sehat', 'check-circle'],
                    ['rumah_kurang_sehat', 'Kurang Sehat', 'times-circle'],
                    ['sampah', 'Bak Sampah', 'trash'],
                    ['jamban', 'Jamban', 'toilet'],
                    ['air_bersih', 'Air Bersih', 'tint'],
                ];
                foreach($stats_env as [$name, $label, $icon]): ?>
                <div class="bg-softgray/30 rounded-2xl p-4 border border-gray-50 group hover:bg-white hover:border-amber-400 transition-all">
                    <div class="flex items-center gap-2 mb-2 text-gray-400 group-hover:text-amber-400 transition-colors">
                        <i class="fas fa-<?= $icon ?> text-[10px]"></i>
                        <label class="text-[10px] font-bold uppercase tracking-wider"><?= $label ?></label>
                    </div>
                    <input type="number" name="<?= $name ?>" value="<?= e($_POST[$name] ?? 0) ?>"
                           class="w-full bg-transparent border-b-2 border-gray-100 text-darkblue_alt text-lg font-black focus:outline-none focus:border-amber-400 transition-all py-1">
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Makanan Pokok</label>
                    <input type="text" name="makanan_pokok" value="<?= e($_POST['makanan_pokok'] ?? '') ?>" placeholder="Contoh: Beras, Jagung..."
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Inovasi Unggulan</label>
                    <textarea name="inovasi" rows="4" placeholder="Ceritakan program inovasi atau prestasi wilayah ini..."
                              class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent transition-all resize-none"><?= e($_POST['inovasi'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center gap-4 bg-white/50 backdrop-blur-sm sticky bottom-8 p-4 rounded-3xl border border-white shadow-2xl z-10">
        <button type="submit" class="flex-1 flex items-center justify-center gap-3 bg-accent hover:bg-darkblue text-white font-black px-8 py-5 rounded-2xl transition-all shadow-xl shadow-accent/20 uppercase tracking-widest text-sm">
            <i class="fas fa-save"></i> Simpan Seluruh Data
        </button>
        <a href="kelurahan.php" class="bg-white hover:bg-gray-50 text-gray-500 font-bold px-8 py-5 rounded-2xl border border-gray-200 transition-all text-sm uppercase tracking-widest">
            Batal
        </a>
    </div>

</form>

<div class="h-20 lg:h-0"></div>

<?php include 'footer.php'; ?>
