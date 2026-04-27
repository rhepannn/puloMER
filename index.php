<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Beranda';

// Ambil semua settings
$S = getAllSettings($conn);
$heroImg = getImg($S['beranda_hero_image'] ?? '', 'settings', 'foto-beranda.jpg');

// Ambil berita terbaru
$stmtB = $conn->prepare("SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 5");
$stmtB->execute();
$beritaList = $stmtB->get_result();
$beritaArr = [];
while ($b = $beritaList->fetch_assoc()) $beritaArr[] = $b;

// Ambil kegiatan terbaru
$stmtK = $conn->prepare("SELECT * FROM kegiatan ORDER BY tgl_kegiatan DESC LIMIT 6");
$stmtK->execute();
$kegiatanList = $stmtK->get_result();

// Statistik Kelurahan
$statQ = $conn->query("SELECT SUM(penduduk) as sum_penduduk, SUM(penduduk_l) as sum_l, SUM(penduduk_p) as sum_p, SUM(jumlah_rw) as sum_rw, SUM(jumlah_rt) as sum_rt, SUM(IF(inovasi IS NOT NULL AND inovasi != '', 1, 0)) as sum_inovasi FROM kelurahan");
$statD = ($statQ && $statQ->num_rows > 0) ? $statQ->fetch_assoc() : null;

$stat_penduduk = $statD['sum_penduduk'] ?? 0;
$stat_l = $statD['sum_l'] ?? 0;
$stat_p = $statD['sum_p'] ?? 0;
$stat_rw = $statD['sum_rw'] ?? 0;
$stat_rt = $statD['sum_rt'] ?? 0;
$stat_inovasi = $statD['sum_inovasi'] ?? 0;

include 'include/header.php'; 
?>

<!-- HERO SECTION -->
<section class="relative w-full min-h-[85vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 w-full h-full">
        <img src="<?= $heroImg ?>" alt="Hero PKK" class="w-full h-full object-cover object-center">
    </div>
    <!-- Soft Gradient Overlay: Dark Blue to Light Blue multiply -->
    <div class="absolute inset-0 bg-gradient-to-r from-darkblue_alt/95 via-darkblue/80 to-accent/40 mix-blend-multiply"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 pt-20 pb-32">
        <div class="max-w-3xl fade-in-up">
            <span class="inline-block py-1.5 px-4 rounded-full bg-white/10 text-white border border-white/20 text-xs font-semibold tracking-wider uppercase mb-6 backdrop-blur-md">
                Portal Resmi TP PKK
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                <?= $S['beranda_hero_title'] ?: 'Mewujudkan Masyarakat <span class="text-accent">Maju & Sejahtera</span>' ?>
            </h1>
            <p class="text-lg md:text-xl text-gray-200 leading-relaxed mb-10 max-w-2xl font-light">
                <?= e($S['beranda_hero_title'] ? ($S['beranda_hero_subtitle'] ?? '') : 'Portal informasi terpadu TP PKK Kecamatan Pulomerak untuk pemberdayaan keluarga dan kesejahteraan masyarakat Kota Cilegon.') ?>
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="profil.php" class="bg-accent hover:bg-darkblue text-white px-8 py-3.5 rounded-lg font-medium transition-colors duration-300 shadow-lg shadow-accent/20 flex items-center gap-2">
                    <i class="fas fa-university"></i> Profil Organisasi
                </a>
                <a href="kegiatan.php" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-8 py-3.5 rounded-lg font-medium transition-colors duration-300 backdrop-blur-sm flex items-center gap-2">
                    <i class="fas fa-camera"></i> Kegiatan PKK
                </a>
            </div>
        </div>
    </div>
</section>

<!-- STATISTIK STRIP (Overlap with Hero) -->
<section class="relative z-20 -mt-24 px-4 md:px-6 pb-16">
    <div class="container mx-auto">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-6 md:p-8 flex flex-col lg:flex-row gap-8 justify-between items-center scroll-reveal delay-100">
            
            <!-- Exact Stats layout -->
            <div class="flex-1 w-full lg:border-r border-gray-100 lg:pr-8">
                <h3 class="text-darkblue_alt font-bold text-lg mb-5 flex items-center gap-2">
                    <i class="fas fa-users text-accent"></i> Demografi Penduduk
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-softgray p-5 rounded-xl text-center border border-gray-50">
                        <div class="text-[10px] md:text-xs text-gray-500 font-semibold mb-1 tracking-wider uppercase">Laki-Laki</div>
                        <div class="text-2xl md:text-3xl font-bold text-darkblue"><?= number_format($stat_l, 0, ',', '.') ?></div>
                    </div>
                    <div class="bg-softgray p-5 rounded-xl text-center border border-gray-50">
                        <div class="text-[10px] md:text-xs text-gray-500 font-semibold mb-1 tracking-wider uppercase">Perempuan</div>
                        <div class="text-2xl md:text-3xl font-bold text-darkblue"><?= number_format($stat_p, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="mt-5 text-center text-sm font-medium text-gray-500 bg-gray-50 py-2 rounded-lg">
                    TOTAL PENDUDUK: <span class="text-accent font-bold text-lg ml-1"><?= number_format($stat_penduduk, 0, ',', '.') ?></span> Jiwa
                </div>
            </div>

            <!-- RW/RT/Inovasi -->
            <div class="flex-1 w-full grid grid-cols-3 gap-2 md:gap-4">
                <div class="text-center p-4 hover:bg-softgray transition-colors rounded-xl">
                    <div class="text-3xl md:text-4xl font-bold text-darkblue mb-2"><?= number_format($stat_rw, 0, ',', '.') ?></div>
                    <div class="text-xs md:text-sm text-gray-500 font-medium">Rukun Warga</div>
                </div>
                <div class="text-center p-4 hover:bg-softgray transition-colors rounded-xl border-l border-gray-100">
                    <div class="text-3xl md:text-4xl font-bold text-darkblue mb-2"><?= number_format($stat_rt, 0, ',', '.') ?></div>
                    <div class="text-xs md:text-sm text-gray-500 font-medium">Rukun Tetangga</div>
                </div>
                <div class="text-center p-4 hover:bg-softgray transition-colors rounded-xl border-l border-gray-100">
                    <div class="text-3xl md:text-4xl font-bold text-accent mb-2"><?= number_format($stat_inovasi, 0, ',', '.') ?></div>
                    <div class="text-xs md:text-sm text-gray-500 font-medium">Inovasi</div>
                </div>
            </div>
            
        </div>
    </div>
</section>

<!-- TENTANG PKK -->
<section class="py-12 md:py-20 bg-softgray">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center max-w-2xl mx-auto mb-16 scroll-reveal">
            <span class="text-accent font-semibold tracking-wider text-xs md:text-sm uppercase bg-accent/10 px-3 py-1 rounded-full">Tentang Kami</span>
            <h2 class="text-3xl md:text-4xl font-bold text-darkblue_alt mt-4 mb-4">Mengenal Gerakan PKK</h2>
            <div class="w-16 h-1 bg-accent mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 items-start">
            <div class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100 scroll-reveal">
                <h3 class="text-xl font-bold text-darkblue mb-5 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-softgray flex items-center justify-center text-accent">
                        <i class="fas fa-info text-lg"></i>
                    </div>
                    Pengertian
                </h3>
                <p class="text-gray-600 leading-relaxed text-justify text-sm md:text-base">
                    <?= e($S['pkk_pengertian'] ?? 'Pemberdayaan dan Kesejahteraan Keluarga (PKK) adalah gerakan nasional dalam pembangunan masyarakat yang tumbuh dari bawah yang pengelolaannya dari, oleh dan untuk masyarakat.') ?>
                </p>
            </div>
            
            <div class="bg-white p-8 md:p-10 rounded-2xl shadow-sm border border-gray-100 scroll-reveal delay-100 mt-0 lg:mt-12"> <!-- Slight asymmetry -->
                <h3 class="text-xl font-bold text-darkblue mb-5 flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-softgray flex items-center justify-center text-accent">
                        <i class="fas fa-bullseye text-lg"></i>
                    </div>
                    Tujuan
                </h3>
                <p class="text-gray-600 leading-relaxed text-justify text-sm md:text-base">
                    <?= e($S['pkk_tujuan'] ?? 'Memberdayakan keluarga untuk meningkatkan kesejahteraan menuju terwujudnya keluarga yang beriman dan bertaqwa kepada Tuhan Yang Maha Esa, berakhlak mulia dan berbudi luhur, sehat sejahtera, maju dan mandiri, kesetaraan dan keadilan gender serta kesadaran hukum dan lingkungan.') ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- 10 PROGRAM POKOK -->
<section class="py-16 md:py-24 bg-white relative overflow-hidden">
    <!-- Subtle background accents -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-softgray rounded-full mix-blend-multiply filter blur-3xl opacity-70 transform translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-80 h-80 bg-accent/5 rounded-full mix-blend-multiply filter blur-3xl opacity-70 transform -translate-x-1/2 translate-y-1/2"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        <div class="flex flex-col md:flex-row justify-between items-end mb-16 scroll-reveal">
            <div class="max-w-2xl">
                <span class="text-accent font-semibold tracking-wider text-xs md:text-sm uppercase bg-accent/10 px-3 py-1 rounded-full">Pilar Utama</span>
                <h2 class="text-3xl md:text-4xl font-bold text-darkblue_alt mt-4 mb-4">10 Program Pokok PKK</h2>
                <p class="text-gray-600 text-base md:text-lg">Pilar utama pemberdayaan keluarga yang diimplementasikan secara komprehensif oleh kelompok kerja.</p>
            </div>
        </div>

        <?php
        $programIcons = [
            'fa-heart', 'fa-hands-holding-circle', 'fa-utensils', 'fa-shirt', 'fa-house-chimney',
            'fa-graduation-cap', 'fa-heart-pulse', 'fa-shop', 'fa-leaf', 'fa-calendar-check'
        ];
        $programNames = [
            'Penghayatan Pancasila', 'Gotong Royong', 'Pangan', 'Sandang', 'Perumahan & Tata Laksana',
            'Pendidikan & Keterampilan', 'Kesehatan', 'Pengembangan Koperasi', 'Kelestarian Lingkungan', 'Perencanaan Sehat'
        ];
        ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3 md:gap-5">
            <?php for($i=0; $i<10; $i++): ?>
                <!-- human-skew-alt on slightly random elements to break perfection -->
                <div class="bg-softgray hover:bg-darkblue group p-5 md:p-6 rounded-2xl transition-all duration-300 text-center flex flex-col items-center justify-center scroll-reveal" style="animation-delay: <?= ($i%5)*100 ?>ms;">
                    <div class="flex flex-col items-center">
                        <div class="w-14 h-14 bg-white group-hover:bg-white/10 rounded-full flex items-center justify-center mb-4 shadow-sm transition-colors duration-300">
                            <i class="fas <?= $programIcons[$i] ?> text-xl text-accent group-hover:text-white transition-colors duration-300"></i>
                        </div>
                        <h4 class="font-semibold text-darkblue_alt group-hover:text-white text-xs md:text-sm leading-snug transition-colors duration-300">
                            <?= $programNames[$i] ?>
                        </h4>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
        
        <div class="mt-16 grid grid-cols-1 lg:grid-cols-2 gap-8 scroll-reveal delay-200">
            <!-- Sasaran Box -->
            <div class="bg-darkblue_alt text-white p-8 md:p-10 rounded-2xl shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full transform translate-x-1/4 -translate-y-1/4"></div>
                <h3 class="text-xl font-bold mb-4 relative z-10 text-white"><i class="fas fa-users-viewfinder text-accent mr-2"></i> Sasaran Gerakan</h3>
                <p class="text-gray-300 text-sm leading-relaxed mb-6 relative z-10">
                    <?= e($S['pkk_sasaran'] ?? 'Keluarga, baik di perdesaan maupun perkotaan yang perlu ditingkatkan dan dikembangkan kemampuan dan kepribadiannya.') ?>
                </p>
                <ul class="space-y-3 text-sm text-gray-300 relative z-10">
                    <li class="flex items-start gap-3 bg-white/5 p-3 rounded-lg"><i class="fas fa-check-circle text-accent mt-0.5"></i> <span><strong>Mental Spiritual:</strong> <?= e($S['pkk_sasaran_mental'] ?? 'Sikap dan perilaku keluarga.') ?></span></li>
                    <li class="flex items-start gap-3 bg-white/5 p-3 rounded-lg"><i class="fas fa-check-circle text-accent mt-0.5"></i> <span><strong>Fisik Material:</strong> <?= e($S['pkk_sasaran_fisik'] ?? 'Pangan, sandang, perumahan, kesehatan.') ?></span></li>
                </ul>
            </div>
            
            <!-- Tugas Box -->
            <div class="bg-softgray border border-gray-100 p-8 md:p-10 rounded-2xl">
                <h3 class="text-xl font-bold text-darkblue mb-4"><i class="fas fa-list-check text-accent mr-2"></i> Tugas TP PKK</h3>
                <p class="text-gray-600 text-sm leading-relaxed mb-6">
                    <?= e($S['pkk_tugas'] ?? 'Merencanakan, melaksanakan dan membina pelaksanaan program-program kerja TP PKK, sesuai dengan keadaan dan kebutuhan masyarakat.') ?>
                </p>
                <div class="mt-auto">
                    <a href="profil.php" class="inline-flex items-center gap-2 bg-white px-5 py-2.5 rounded-lg text-darkblue font-semibold hover:bg-accent hover:text-white transition-colors text-sm shadow-sm border border-gray-100">
                        Lihat Profil Lengkap <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BERITA & KEGIATAN SPLIT SECTION -->
<section class="py-16 md:py-24 bg-softgray">
    <div class="container mx-auto px-4 md:px-6">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
            
            <!-- BERITA (7 cols) -->
            <div class="lg:col-span-7">
                <div class="flex justify-between items-end mb-8 scroll-reveal">
                    <div>
                        <span class="text-accent font-semibold text-xs md:text-sm uppercase bg-accent/10 px-3 py-1 rounded-full">Informasi Terkini</span>
                        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt mt-3">Berita Terbaru</h2>
                    </div>
                    <a href="berita.php" class="hidden md:inline-flex text-sm font-semibold text-accent hover:text-darkblue transition-colors items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200">
                        Lihat Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <?php if(empty($beritaArr)): ?>
                    <div class="bg-white p-10 rounded-2xl border border-gray-100 text-center scroll-reveal shadow-sm">
                        <i class="fas fa-newspaper text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">Belum ada berita dipublikasikan.</p>
                    </div>
                <?php else: ?>
                    <!-- Highlighted News -->
                    <?php $bUtama = $beritaArr[0]; ?>
                    <a href="berita-detail.php?id=<?= $bUtama['id'] ?>" class="group block mb-6 scroll-reveal">
                        <!-- human-skew applies slight tilt to the image container -->
                        <div class="relative rounded-2xl overflow-hidden human-skew aspect-[16/9] shadow-sm mb-4 bg-darkblue">
                            <img src="<?= getImg($bUtama['gambar'], 'berita') ?>" alt="<?= e($bUtama['judul']) ?>" class="unskew w-full h-full object-cover opacity-90 group-hover:opacity-100 group-hover:scale-105 transition-all duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-darkblue_alt/95 via-darkblue_alt/40 to-transparent unskew"></div>
                            <div class="absolute bottom-0 left-0 w-full p-6 md:p-8 unskew">
                                <span class="bg-accent text-white text-[10px] md:text-xs px-3 py-1 rounded-full mb-3 inline-block font-semibold uppercase tracking-wider"><?= e($bUtama['kategori'] ?? 'Berita') ?></span>
                                <h3 class="text-xl md:text-2xl font-bold text-white mb-3 group-hover:text-accent transition-colors leading-snug"><?= e($bUtama['judul']) ?></h3>
                                <div class="text-gray-300 text-xs font-medium flex items-center gap-4">
                                    <span><i class="fas fa-calendar-alt mr-1 text-accent"></i> <?= formatTanggal($bUtama['tgl_post']) ?></span>
                                    <span><i class="fas fa-user mr-1 text-accent"></i> Admin</span>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- List News -->
                    <div class="space-y-4">
                        <?php for($i = 1; $i <= min(3, count($beritaArr)-1); $i++): $b = $beritaArr[$i]; ?>
                        <a href="berita-detail.php?id=<?= $b['id'] ?>" class="group flex gap-4 bg-white p-3 rounded-2xl border border-gray-100 hover:shadow-md transition-all scroll-reveal delay-<?= $i*100 ?>">
                            <div class="w-24 h-24 md:w-32 md:h-32 flex-shrink-0 rounded-xl overflow-hidden">
                                <img src="<?= getImg($b['gambar'], 'berita') ?>" alt="<?= e($b['judul']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            </div>
                            <div class="flex flex-col justify-center py-1 md:py-2 pr-2">
                                <span class="text-accent text-[10px] uppercase tracking-wider font-bold mb-1"><?= e($b['kategori'] ?? 'Berita') ?></span>
                                <h4 class="text-darkblue_alt font-bold text-sm md:text-base leading-snug mb-2 group-hover:text-accent transition-colors line-clamp-2"><?= e($b['judul']) ?></h4>
                                <div class="text-gray-400 text-xs mt-auto font-medium"><i class="fas fa-clock mr-1"></i> <?= formatTanggal($b['tgl_post']) ?></div>
                            </div>
                        </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- KEGIATAN (5 cols) -->
            <div class="lg:col-span-5 mt-12 lg:mt-0">
                <div class="flex justify-between items-end mb-8 scroll-reveal delay-200">
                    <div>
                        <span class="text-accent font-semibold text-xs md:text-sm uppercase bg-accent/10 px-3 py-1 rounded-full">Aktivitas</span>
                        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt mt-3">Kegiatan Terkini</h2>
                    </div>
                    <a href="kegiatan.php" class="hidden md:inline-flex text-sm font-semibold text-accent hover:text-darkblue transition-colors items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200">
                        Semua <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <?php if($kegiatanList->num_rows === 0): ?>
                    <div class="bg-white p-10 rounded-2xl border border-gray-100 text-center scroll-reveal shadow-sm">
                        <i class="fas fa-camera text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500 font-medium">Belum ada kegiatan dipublikasikan.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-2 gap-3 md:gap-4">
                        <?php 
                        $delay = 100;
                        // Some slight asymmetry in padding/margins dynamically applied
                        $count = 0;
                        while($k = $kegiatanList->fetch_assoc()): 
                            $extraClass = ''; 
                        ?>
                        <a href="kegiatan-detail.php?id=<?= $k['id'] ?>" class="group relative block aspect-[4/5] rounded-2xl overflow-hidden scroll-reveal <?= $extraClass ?>" style="animation-delay: <?= $delay ?>ms;">
                            <img src="<?= getImg($k['gambar'], 'kegiatan') ?>" alt="<?= e($k['judul']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-darkblue_alt/90 via-darkblue/20 to-transparent opacity-80 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="absolute bottom-0 left-0 w-full p-4 md:p-5 transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                <h4 class="text-white font-semibold text-xs md:text-sm leading-tight line-clamp-2 mb-2"><?= e($k['judul']) ?></h4>
                                <p class="text-accent text-[10px] md:text-xs font-medium"><i class="fas fa-calendar-alt mr-1"></i> <?= formatTanggal($k['tgl_kegiatan']) ?></p>
                            </div>
                        </a>
                        <?php 
                        $delay += 100;
                        $count++;
                        endwhile; 
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="mt-8 text-center md:hidden flex gap-3 justify-center">
                    <a href="berita.php" class="bg-white text-darkblue border border-gray-200 px-5 py-2 rounded-lg text-sm font-semibold">Semua Berita</a>
                    <a href="kegiatan.php" class="bg-accent text-white px-5 py-2 rounded-lg text-sm font-semibold shadow-sm">Semua Kegiatan</a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
