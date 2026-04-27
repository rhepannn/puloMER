<?php
require_once 'include/config.php';
require_once 'include/functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL.'/kelurahan.php');

$stmt = $conn->prepare("SELECT * FROM kelurahan WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$kel = $stmt->get_result()->fetch_assoc();
if (!$kel) redirect(SITE_URL.'/kelurahan.php');

$pageTitle = 'Profil Kelurahan ' . $kel['nama'];

include 'include/header.php';

$hero_img = getImg($kel['foto'] ?? '', 'kelurahan');
?>

<!-- HERO SECTION -->
<section class="relative w-full py-20 md:py-28 flex items-center justify-center overflow-hidden bg-darkblue hide-on-print">
    <div class="absolute inset-0 w-full h-full opacity-40">
        <img src="<?= $hero_img ?>" alt="<?= e($kel['nama']) ?>" class="w-full h-full object-cover object-center mix-blend-overlay blur-sm scale-105">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <!-- Breadcrumbs -->
        <div class="flex items-center justify-center gap-2 text-sm text-gray-300 mb-6 font-medium">
            <a href="<?= SITE_URL ?>/" class="hover:text-white transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
            <a href="<?= SITE_URL ?>/kelurahan.php" class="hover:text-white transition-colors">Kelurahan</a>
            <i class="fas fa-chevron-right text-[10px] opacity-50"></i>
            <span class="text-white"><?= e($kel['nama']) ?></span>
        </div>

        <span class="inline-block py-1 px-4 rounded-full bg-accent/20 text-white border border-accent/40 text-xs font-semibold tracking-wider uppercase mb-4 backdrop-blur-md">
            Profil Kelurahan
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">
            <?= e($kel['nama']) ?>
        </h1>
    </div>
</section>

<section class="py-16 md:py-24 bg-softgray relative -mt-6">
    <div class="container mx-auto px-4 md:px-6">
        
        <!-- SECTION 1: PENJELASAN & INFO WILAYAH -->
        <div class="max-w-5xl mx-auto mb-20 scroll-reveal">
            
            <div class="bg-white rounded-[2rem] p-8 md:p-12 shadow-sm border border-gray-100 mb-10 relative overflow-hidden">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-accent/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-accent/10 text-accent font-semibold text-sm mb-6">
                        <i class="fas fa-feather-alt"></i> Selayang Pandang
                    </div>
                    <h2 class="text-3xl font-bold text-darkblue mb-8">Tentang <span class="text-accent">Kelurahan <?= e($kel['nama']) ?></span></h2>
                    
                    <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed text-justify">
                        <p><?= nl2br(e($kel['deskripsi'] ?: 'Informasi deskripsi kelurahan belum ditambahkan oleh administrator.')) ?></p>
                    </div>
                </div>
            </div>

            <!-- Statistik Cepat -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                <?php
                $quickStats = [
                    ['label' => 'Jumlah RW', 'val' => ($kel['jumlah_rw']??0) . ' RW', 'icon' => 'fa-layer-group', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50', 'border' => 'group-hover:border-blue-200'],
                    ['label' => 'Jumlah RT', 'val' => ($kel['jumlah_rt']??0) . ' RT', 'icon' => 'fa-home', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50', 'border' => 'group-hover:border-amber-200'],
                    ['label' => 'Laki-laki', 'val' => number_format($kel['penduduk_l']??0) . ' Jiwa', 'icon' => 'fa-mars', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'border' => 'group-hover:border-emerald-200'],
                    ['label' => 'Perempuan', 'val' => number_format($kel['penduduk_p']??0) . ' Jiwa', 'icon' => 'fa-venus', 'color' => 'text-rose-600', 'bg' => 'bg-rose-50', 'border' => 'group-hover:border-rose-200'],
                ];
                foreach($quickStats as $s):
                ?>
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm text-center group hover:-translate-y-1 transition-all duration-300 <?= $s['border'] ?>">
                    <div class="w-14 h-14 mx-auto rounded-2xl <?= $s['bg'] ?> <?= $s['color'] ?> flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                        <i class="fas <?= $s['icon'] ?>"></i>
                    </div>
                    <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1"><?= $s['label'] ?></div>
                    <div class="text-lg font-bold text-darkblue"><?= e($s['val']) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (!empty($kel['inovasi'])): ?>
            <div class="mt-8 bg-gradient-to-r from-yellow-50 to-white p-6 md:p-8 rounded-3xl border border-yellow-100 flex flex-col md:flex-row items-center md:items-start gap-6 shadow-sm">
                <div class="w-16 h-16 rounded-full bg-yellow-400/20 text-yellow-600 flex items-center justify-center text-2xl flex-shrink-0">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="text-center md:text-left">
                    <h4 class="text-xl font-bold text-darkblue mb-2">Inovasi Unggulan</h4>
                    <p class="text-gray-700 italic font-serif text-lg leading-relaxed">"<?= e($kel['inovasi']) ?>"</p>
                </div>
            </div>
            <?php endif; ?>

        </div>

        <!-- SECTION 2: STRUKTUR KEPENGURUSAN -->
        <div class="max-w-6xl mx-auto mb-24 scroll-reveal hide-on-print">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-darkblue_alt/10 text-darkblue_alt font-semibold text-sm mb-4">
                    <i class="fas fa-sitemap"></i> Kepengurusan
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-darkblue mb-4">Struktur <span class="text-accent">Pengurus TP PKK</span></h2>
                <p class="text-gray-600">Organisasi Pemberdayaan dan Kesejahteraan Keluarga Kelurahan <?= e($kel['nama']) ?>.</p>
            </div>

            <div class="flex flex-col items-center gap-12">
                <!-- KETUA -->
                <div class="bg-white rounded-xl p-6 shadow-xl border border-gray-100 text-center w-full max-w-[240px] relative transform hover:-translate-y-2 transition-all duration-300">
                    <!-- Accent Line -->
                    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-16 h-1 bg-accent rounded-b-sm"></div>
                    
                    <div class="w-20 h-20 mx-auto rounded-lg p-0.5 bg-gradient-to-br from-accent to-darkblue mb-4">
                        <div class="w-full h-full rounded-[calc(0.5rem-2px)] overflow-hidden border-2 border-white bg-softgray flex items-center justify-center">
                            <?php if(!empty($kel['foto_ketua'])): ?>
                                <img src="<?= getImg($kel['foto_ketua'], 'bidang') ?>" alt="Ketua" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user text-3xl text-gray-300"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Ketua TP PKK</div>
                    <h3 class="text-lg font-bold text-darkblue"><?= e($kel['ketua_pkk'] ?: '[Belum Diatur]') ?></h3>
                </div>

                <!-- SEKRETARIS & BENDAHARA -->
                <div class="w-full max-w-4xl grid grid-cols-2 gap-4 md:gap-6 relative">
                    <!-- Connecting line -->
                    <div class="absolute -top-12 left-1/2 w-0.5 h-12 bg-gray-200 hidden md:block"></div>
                    <div class="absolute -top-6 left-1/4 right-1/4 h-0.5 bg-gray-200 hidden md:block"></div>
                    <div class="absolute -top-6 left-1/4 w-0.5 h-6 bg-gray-200 hidden md:block"></div>
                    <div class="absolute -top-6 right-1/4 w-0.5 h-6 bg-gray-200 hidden md:block"></div>

                    <?php 
                    $staff = [
                        ['nama' => $kel['sekretaris_pkk'], 'foto' => $kel['foto_sekretaris'], 'label' => 'Sekretaris', 'icon' => 'fa-pen-nib', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                        ['nama' => $kel['bendahara_pkk'],  'foto' => $kel['foto_bendahara'],  'label' => 'Bendahara', 'icon' => 'fa-wallet', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                    ];
                    foreach($staff as $s):
                    ?>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex flex-col items-center text-center gap-3 hover:border-accent/30 hover:shadow-md transition-all">
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-md overflow-hidden bg-softgray border-2 border-white shadow-sm flex-shrink-0 flex items-center justify-center">
                            <?php if(!empty($s['foto'])): ?>
                                <img src="<?= getImg($s['foto'], 'bidang') ?>" alt="<?= $s['label'] ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user text-xl md:text-2xl text-gray-300"></i>
                            <?php endif; ?>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <i class="fas <?= $s['icon'] ?> <?= $s['color'] ?> text-[8px] md:text-[10px]"></i>
                                <span class="text-[8px] md:text-[10px] font-bold text-gray-500 uppercase tracking-widest"><?= $s['label'] ?></span>
                            </div>
                            <h4 class="text-xs md:text-lg font-bold text-darkblue truncate"><?= e($s['nama'] ?: '[Belum Diatur]') ?></h4>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- POKJA 1-4 -->
                <div class="w-full grid grid-cols-2 gap-4 md:gap-6 relative mt-6 max-w-4xl">
                    <?php for($i=1; $i<=4; $i++): ?>
                    <div class="bg-white p-4 md:p-6 rounded-xl border border-gray-100 shadow-sm flex flex-col items-center text-center hover:bg-darkblue hover:text-white transition-all duration-300 group">
                        <div class="w-12 h-12 md:w-14 md:h-14 rounded-md overflow-hidden bg-softgray border-2 border-white shadow-sm mb-3 md:mb-4 flex items-center justify-center group-hover:border-darkblue_alt">
                            <?php if(!empty($kel["foto_pokja{$i}"])): ?>
                                <img src="<?= getImg($kel["foto_pokja{$i}"], 'bidang') ?>" alt="Pokja <?= $i ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <i class="fas fa-user text-xl md:text-2xl text-gray-300"></i>
                            <?php endif; ?>
                        </div>
                        <div class="text-[8px] md:text-[10px] font-bold text-accent uppercase tracking-widest mb-1 group-hover:text-accent/80">Anggota Pokja <?= $i ?></div>
                        <h4 class="text-xs md:text-base font-bold text-darkblue group-hover:text-white line-clamp-2"><?= e($kel["pokja{$i}_pkk"] ?: '[Belum Diatur]') ?></h4>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <!-- SECTION 3: STATISTICAL TABLE -->
        <div class="max-w-6xl mx-auto pb-20 scroll-reveal">
            
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-10">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 font-semibold text-sm mb-3">
                        <i class="fas fa-chart-bar"></i> Statistik Data
                    </div>
                    <h2 class="text-3xl font-bold text-darkblue">Laporan <span class="text-accent">Rekapitulasi Tahunan</span></h2>
                </div>
            </div>

            <div class="bg-white rounded-3xl overflow-hidden shadow-lg border border-gray-100">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[800px]">
                        <thead>
                            <tr class="bg-gradient-to-r from-darkblue to-darkblue_alt text-white">
                                <th class="p-6 font-bold w-2/5 border-r border-white/10">Kategori Indikator</th>
                                <th class="p-6 text-center font-bold w-1/5 border-r border-white/10">Laki-Laki</th>
                                <th class="p-6 text-center font-bold w-1/5 border-r border-white/10">Perempuan</th>
                                <th class="p-6 text-center font-bold w-1/5">Total / Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            
                            <!-- KELOMPOK 1: KEPENDUDUKAN -->
                            <tr class="bg-softgray/80 border-y border-gray-200">
                                <td colspan="4" class="px-6 py-4 font-bold text-darkblue uppercase tracking-wider text-xs">
                                    <i class="fas fa-users-cog text-accent mr-2"></i> Informasi Kependudukan & Keluarga
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Jumlah Penduduk Terdata</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-gray-800 text-lg"><?= number_format($kel['penduduk_l']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Laki-laki</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-gray-800 text-lg"><?= number_format($kel['penduduk_p']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Perempuan</span>
                                </td>
                                <td class="px-6 py-5 text-center bg-blue-50/50 border-l border-gray-100">
                                    <span class="font-black text-blue-700 text-xl"><?= number_format($kel['penduduk']??0) ?></span>
                                    <span class="block text-[10px] text-blue-500 mt-1 uppercase font-bold">Total Jiwa</span>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Statistik Rumah Tangga</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-darkblue text-lg"><?= number_format($kel['jumlah_krt']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">KRT</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-darkblue text-lg"><?= number_format($kel['jumlah_kk']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">KK</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-darkblue text-lg"><?= number_format($kel['dasa_wisma']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Dasawisma</span>
                                </td>
                            </tr>

                            <!-- KELOMPOK 2: KESEHATAN -->
                            <tr class="bg-rose-50/50 border-y border-rose-100">
                                <td colspan="4" class="px-6 py-4 font-bold text-rose-800 uppercase tracking-wider text-xs">
                                    <i class="fas fa-heartbeat text-rose-500 mr-2"></i> Kesehatan & Kependudukan
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-rose-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Kondisi Ibu Hamil & Menyusui</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['ibu_hamil']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Ibu Hamil</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['ibu_menyusui']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Ibu Menyusui</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['ibu_nifas']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Ibu Nifas</span>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-rose-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Pasangan & Wanita Usia Subur</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['pus']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">PUS</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['wus']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">WUS</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100 text-gray-300">-</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-rose-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Lansia & Buta Aksara</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['lansia']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Lansia</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['buta']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">3 Buta</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100 bg-rose-50/30">
                                    <span class="font-black text-rose-700 text-xl"><?= number_format($kel['ibu_meninggal']??0) ?></span>
                                    <span class="block text-[10px] text-rose-500 mt-1 uppercase font-bold">Ibu Wafat</span>
                                </td>
                            </tr>

                            <!-- KELOMPOK 3: ANAK -->
                            <tr class="bg-emerald-50/50 border-y border-emerald-100">
                                <td colspan="4" class="px-6 py-4 font-bold text-emerald-800 uppercase tracking-wider text-xs">
                                    <i class="fas fa-baby-carriage text-emerald-500 mr-2"></i> Kelahiran & Pertumbuhan Anak
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-emerald-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Jumlah Kelahiran Bayi</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-gray-800 text-lg"><?= number_format($kel['bayi_lahir_l']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Laki-laki</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-gray-800 text-lg"><?= number_format($kel['bayi_lahir_p']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Perempuan</span>
                                </td>
                                <td class="px-6 py-5 text-center bg-emerald-50/50 border-l border-gray-100">
                                    <span class="font-black text-emerald-700 text-xl"><?= number_format(($kel['bayi_lahir_l']??0) + ($kel['bayi_lahir_p']??0)) ?></span>
                                    <span class="block text-[10px] text-emerald-600 mt-1 uppercase font-bold">Total Lahir</span>
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-emerald-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Kepemilikan Akte Kelahiran</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-emerald-600 text-lg"><?= number_format($kel['akte_ada']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Ada Akte</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['akte_tidak']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Tidak Ada</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100 text-gray-300">-</td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-rose-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Angka Kematian Bayi & Balita</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format(($kel['bayi_meninggal_l']??0) + ($kel['bayi_meninggal_p']??0)) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Bayi (0-1 Th)</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format(($kel['balita_meninggal_l']??0) + ($kel['balita_meninggal_p']??0)) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Balita (1-5 Th)</span>
                                </td>
                                <td class="px-6 py-5 text-center bg-rose-50/50 border-l border-gray-100">
                                    <span class="font-black text-rose-700 text-xl"><?= number_format(($kel['bayi_meninggal_l']??0) + ($kel['bayi_meninggal_p']??0) + ($kel['balita_meninggal_l']??0) + ($kel['balita_meninggal_p']??0)) ?></span>
                                    <span class="block text-[10px] text-rose-600 mt-1 uppercase font-bold">Total Wafat</span>
                                </td>
                            </tr>

                            <!-- KELOMPOK 4: LINGKUNGAN -->
                            <tr class="bg-amber-50/50 border-y border-amber-100">
                                <td colspan="4" class="px-6 py-4 font-bold text-amber-800 uppercase tracking-wider text-xs">
                                    <i class="fas fa-leaf text-amber-500 mr-2"></i> Lingkungan & Kriteria Rumah
                                </td>
                            </tr>
                            <tr class="border-b border-gray-100 hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Kondisi Rumah Sehat</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-emerald-600 text-lg"><?= number_format($kel['rumah_sehat']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Rumah Sehat</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-rose-600 text-lg"><?= number_format($kel['rumah_kurang_sehat']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Kurang Sehat</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-amber-700 text-lg"><?= e($kel['makanan_pokok'] ?: '-') ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Mkn Pokok</span>
                                </td>
                            </tr>
                            <tr class="hover:bg-amber-50/30 transition-colors">
                                <td class="px-6 py-5 font-semibold text-gray-700">Fasilitas Lingkungan (Miliki)</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="font-bold text-amber-700 text-lg"><?= number_format($kel['sampah']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Tpt Sampah</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-amber-700 text-lg"><?= number_format($kel['jamban']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Jamban</span>
                                </td>
                                <td class="px-6 py-5 text-center border-l border-gray-100">
                                    <span class="font-bold text-amber-700 text-lg"><?= number_format($kel['air_bersih']??0) ?></span>
                                    <span class="block text-[10px] text-gray-400 mt-1 uppercase">Air Bersih</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 text-center hide-on-print">
                <a href="kelurahan.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-darkblue font-semibold transition-colors">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Wilayah
                </a>
            </div>

        </div>

    </div>
</section>

<!-- Custom Print Styles -->
<style>
@media print {
    .hide-on-print { display: none !important; }
    body, .bg-softgray { background: white !important; }
    .container { max-width: 100% !important; padding: 0 !important; }
    .shadow-lg, .shadow-sm, .shadow-md { box-shadow: none !important; }
    table, th, td { border: 1px solid #ddd !important; }
    th { background: #f3f4f6 !important; color: #000 !important; }
    .bg-gradient-to-r { background: #f3f4f6 !important; }
    * { color: #000 !important; }
}
</style>

<?php include 'include/footer.php'; ?>
