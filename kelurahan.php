<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Kelurahan';

// AMBIL DATA KELURAHAN - SIMPAN KE ARRAY
$kelurahans = [];
$res = $conn->query("SELECT * FROM kelurahan ORDER BY nama ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $kelurahans[] = $row;
    }
}

include 'include/header.php';
?>

<!-- HERO SECTION -->
<section class="relative w-full py-24 md:py-32 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-40">
        <img src="<?= SITE_URL ?>/assets/img/1.png" alt="Wilayah Kelurahan" class="w-full h-full object-cover object-center mix-blend-overlay blur-sm scale-105">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-4 rounded-full bg-accent/20 text-white border border-accent/40 text-xs font-semibold tracking-wider uppercase mb-6 backdrop-blur-md">
            Pemerintahan & Organisasi
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">
            Wilayah <span class="text-accent">Kelurahan</span>
        </h1>
        <p class="text-gray-300 max-w-2xl mx-auto text-base md:text-lg mb-6">
            Daftar wilayah administrasi dan struktur pengurus PKK di lingkungan Kecamatan Pulomerak.
        </p>
        <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mt-2"></div>
    </div>
</section>

<!-- STRUKTUR ORGANISASI -->
<section class="py-16 md:py-24 bg-softgray relative -mt-6">
    <div class="container mx-auto px-4 md:px-6">
        
        <div class="text-center max-w-3xl mx-auto mb-16 scroll-reveal">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-accent/10 text-accent font-semibold text-sm mb-4">
                <i class="fas fa-sitemap"></i> Pengurus Wilayah
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-darkblue mb-4">Struktur <span class="text-accent">TP PKK</span></h2>
            <p class="text-gray-600 text-lg">
                Koordinasi berjenjang dari Tingkat Kecamatan hingga masing-masing Kelurahan.
            </p>
        </div>

        <div class="relative max-w-6xl mx-auto">
            
            <!-- TINGKAT KECAMATAN -->
            <div class="flex justify-center mb-16 scroll-reveal px-4 md:px-0">
                <div class="bg-white rounded-xl p-5 md:p-6 shadow-xl border-t-4 border-accent relative w-full max-w-[260px] text-center transform hover:-translate-y-2 transition-all duration-300">
                    <!-- Connector line down -->
                    <div class="absolute left-1/2 -bottom-8 w-0.5 h-8 bg-darkblue/20 hidden lg:block -translate-x-1/2"></div>
                    
                    <div class="w-20 h-20 md:w-24 md:h-24 mx-auto rounded-xl bg-softgray border-4 border-white shadow-lg overflow-hidden flex items-center justify-center mb-4 md:mb-6 relative">
                        <i class="fas fa-user-tie text-3xl md:text-4xl text-gray-300"></i>
                    </div>
                    <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1 md:mb-2">Ketua TP PKK Kecamatan</div>
                    <h3 class="text-xl md:text-2xl font-bold text-darkblue mb-1">Ny. Ma'nawiyah</h3>
                    <div class="text-[11px] md:text-sm font-semibold text-accent">Kecamatan Pulomerak</div>
                </div>
            </div>

            <!-- TINGKAT KELURAHAN -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6 relative max-w-6xl mx-auto">
                
                <!-- Connector horizontal line (dynamic based on grid) -->
                <div class="hidden md:block absolute left-[12.5%] right-[12.5%] -top-8 h-0.5 bg-darkblue/20"></div>

                <!-- Vertical Line from top level to horizontal line -->
                <div class="absolute left-1/2 -top-16 w-0.5 h-8 bg-darkblue/20 hidden md:block -translate-x-1/2"></div>

                <?php if (!empty($kelurahans)): ?>
                    <?php 
                    $delay = 100;
                    foreach ($kelurahans as $k): 
                        $nama_ketua = $k['ketua_pkk'] ?? '';
                        $foto_ketua = $k['foto_ketua'] ?? '';
                    ?>
                        <div class="bg-white rounded-xl p-4 md:p-5 shadow-md border-t-4 border-darkblue text-center relative transform hover:-translate-y-2 transition-all duration-300 scroll-reveal" style="transition-delay: <?= $delay ?>ms">
                            
                            <!-- Vertical Line UP to horizontal line -->
                            <div class="absolute left-1/2 -top-8 w-0.5 h-8 bg-darkblue/20 hidden md:block -translate-x-1/2"></div>
                            
                            <div class="w-16 h-16 md:w-20 md:h-20 mx-auto rounded-xl bg-softgray border-4 border-white shadow-md overflow-hidden flex items-center justify-center mb-3 md:mb-5 relative group">
                                <?php if (!empty($foto_ketua)): ?>
                                    <img src="<?= getImg($foto_ketua, 'bidang') ?>" alt="<?= e($k['nama']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <i class="fas fa-user text-xl md:text-2xl text-gray-300"></i>
                                <?php endif; ?>
                            </div>
                            <div class="text-[9px] md:text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Ketua TP PKK Kelurahan</div>
                            
                            <h3 class="text-sm md:text-lg font-bold text-darkblue mb-1 line-clamp-2 <?= empty($nama_ketua) ? 'text-gray-300 italic' : '' ?>">
                                <?= e($nama_ketua ?: '[Belum Diatur]') ?>
                            </h3>
                            <div class="text-[10px] md:text-xs font-semibold text-accent">Kelurahan <?= e($k['nama']) ?></div>
                        </div>
                    <?php 
                    $delay += 100;
                    endforeach; 
                    ?>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
</section>

<!-- DAFTAR WILAYAH (ADMINISTRASI) -->
<section class="py-16 md:py-24 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4 md:px-6">
        
        <div class="flex flex-col md:flex-row items-end justify-between gap-6 mb-12 scroll-reveal">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-accent/10 text-accent font-semibold text-sm mb-3">
                    <i class="fas fa-map"></i> Administrasi
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-darkblue">Wilayah <span class="text-accent">Kerja</span></h2>
                <p class="text-gray-600 mt-2">Daftar Kelurahan beserta rekapitulasi data statistik wilayah.</p>
            </div>
        </div>

        <?php if (empty($kelurahans)): ?>
            <div class="bg-softgray rounded-3xl p-12 text-center border border-gray-100 shadow-sm scroll-reveal">
                <i class="fas fa-folder-open text-5xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">Data Kelurahan belum tersedia.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-6">
                <?php 
                $delay = 100;
                foreach ($kelurahans as $k): 
                ?>
                    <a href="kelurahan-detail.php?id=<?= $k['id'] ?>" class="group block bg-softgray rounded-2xl md:rounded-3xl p-4 md:p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden scroll-reveal" style="transition-delay: <?= $delay ?>ms">
                        
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-darkblue/5 rounded-full group-hover:bg-accent/10 transition-colors duration-500"></div>

                        <div class="flex items-start justify-between mb-3 md:mb-6 relative z-10 gap-2">
                            <div class="w-8 h-8 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-white text-darkblue flex items-center justify-center text-sm md:text-xl shadow-sm border border-gray-100 group-hover:bg-accent group-hover:text-white group-hover:-rotate-6 transition-all shrink-0">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-[8px] md:text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-0.5 md:mb-1">Penduduk</div>
                                <div class="text-xs md:text-sm font-bold text-darkblue flex items-center gap-1 md:gap-1.5 justify-end">
                                    <i class="fas fa-users text-accent text-[10px] md:text-xs"></i> <?= number_format((int)($k['penduduk']??0)) ?>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 mb-3 md:mb-6">
                            <h3 class="text-sm md:text-xl font-bold text-darkblue group-hover:text-accent transition-colors mb-1 md:mb-2">Kelurahan <?= e($k['nama']) ?></h3>
                            <div class="w-10 h-1 bg-gray-200 rounded-full group-hover:w-full group-hover:bg-accent transition-all duration-500"></div>
                        </div>

                        <div class="flex items-center gap-2 md:gap-4 relative z-10">
                            <div class="flex-1 bg-white rounded-lg md:rounded-xl p-2 md:p-3 border border-gray-100 text-center">
                                <div class="text-[9px] md:text-xs font-bold text-gray-500 mb-0.5 md:mb-1">RW</div>
                                <div class="text-sm md:text-lg font-bold text-darkblue"><?= (int)($k['jumlah_rw']??0) ?></div>
                            </div>
                            <div class="flex-1 bg-white rounded-lg md:rounded-xl p-2 md:p-3 border border-gray-100 text-center">
                                <div class="text-[9px] md:text-xs font-bold text-gray-500 mb-0.5 md:mb-1">RT</div>
                                <div class="text-sm md:text-lg font-bold text-darkblue"><?= (int)($k['jumlah_rt']??0) ?></div>
                            </div>
                        </div>
                    </a>
                <?php 
                $delay += 100;
                if ($delay > 300) $delay = 100;
                endforeach; 
                ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'include/footer.php'; ?>
