<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Daftar RW/RT';

$stmt = $conn->prepare("SELECT * FROM kelurahan ORDER BY nama ASC");
$stmt->execute();
$list = $stmt->get_result();

include 'include/header.php';
?>

<!-- HERO SECTION -->
<section class="relative w-full py-20 md:py-28 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-40">
        <img src="<?= SITE_URL ?>/assets/img/1.png" alt="Daftar RW/RT" class="w-full h-full object-cover object-center mix-blend-overlay blur-sm scale-105">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-4 rounded-full bg-accent/20 text-white border border-accent/40 text-xs font-semibold tracking-wider uppercase mb-6 backdrop-blur-md">
            Wilayah Administrasi
        </span>
        <h1 class="text-3xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">
            Daftar <span class="text-accent">RW/RT</span>
        </h1>
        <p class="text-gray-300 max-w-2xl mx-auto text-sm md:text-base mb-6">
            Peta persebaran Rukun Warga dan Rukun Tetangga di tiap Kelurahan se-Kecamatan Pulomerak.
        </p>
        <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mt-2"></div>
    </div>
</section>

<section class="py-16 md:py-24 bg-softgray relative -mt-6">
    <div class="container mx-auto px-4 md:px-6">
        
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12 scroll-reveal">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-accent/10 text-accent font-semibold text-sm mb-3">
                    <i class="fas fa-map-marked-alt"></i> Persebaran Wilayah
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-darkblue">Data <span class="text-accent">Kelurahan</span></h2>
            </div>
        </div>

        <?php if ($list->num_rows === 0): ?>
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm scroll-reveal">
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-city text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-darkblue mb-2">Data Belum Tersedia</h3>
                <p class="text-gray-500">Data kelurahan dan RW/RT akan segera ditambahkan oleh administrator.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php 
                $delay = 100;
                while ($k = $list->fetch_assoc()): 
                ?>
                    <a href="kelurahan-detail.php?id=<?= $k['id'] ?>" class="group block bg-white rounded-3xl p-6 border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 relative overflow-hidden scroll-reveal" style="transition-delay: <?= $delay ?>ms">
                        
                        <!-- Decoration background -->
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-accent/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="flex items-start justify-between mb-6 relative z-10">
                            <div class="w-12 h-12 rounded-2xl bg-darkblue_alt text-white flex items-center justify-center text-xl shadow-lg group-hover:bg-accent group-hover:rotate-6 transition-all">
                                <i class="fas fa-city"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Penduduk</div>
                                <div class="text-sm font-bold text-darkblue flex items-center gap-1.5 justify-end">
                                    <i class="fas fa-users text-accent text-xs"></i> <?= number_format((int)($k['penduduk']??0)) ?>
                                </div>
                            </div>
                        </div>

                        <div class="relative z-10 mb-6">
                            <h3 class="text-xl font-bold text-darkblue group-hover:text-accent transition-colors mb-2">Kelurahan <?= e($k['nama']) ?></h3>
                            <div class="w-10 h-1 bg-gray-200 rounded-full group-hover:w-16 group-hover:bg-accent transition-all"></div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 relative z-10">
                            <div class="bg-softgray rounded-xl p-3 border border-gray-100 group-hover:border-accent/20 transition-colors">
                                <div class="text-xs font-bold text-gray-500 mb-1">Jumlah RW</div>
                                <div class="text-lg font-bold text-darkblue"><?= (int)($k['jumlah_rw']??0) ?></div>
                            </div>
                            <div class="bg-softgray rounded-xl p-3 border border-gray-100 group-hover:border-accent/20 transition-colors">
                                <div class="text-xs font-bold text-gray-500 mb-1">Jumlah RT</div>
                                <div class="text-lg font-bold text-darkblue"><?= (int)($k['jumlah_rt']??0) ?></div>
                            </div>
                        </div>
                    </a>
                <?php 
                $delay += 100;
                if ($delay > 300) $delay = 100;
                endwhile; 
                ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include 'include/footer.php'; ?>
