<?php
require_once 'include/config.php';
require_once 'include/functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { redirect(SITE_URL . '/berita.php'); }

$stmt = $conn->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$berita = $stmt->get_result()->fetch_assoc();
if (!$berita) { redirect(SITE_URL . '/berita.php'); }

$pageTitle = $berita['judul'];

// Berita terkait (kategori sama, bukan id ini)
$stmtR = $conn->prepare("SELECT * FROM berita WHERE kategori = ? AND id != ? ORDER BY tgl_post DESC LIMIT 3");
$stmtR->bind_param('si', $berita['kategori'], $id);
$stmtR->execute();
$related = $stmtR->get_result();

// Berita terbaru (sidebar)
$stmtN = $conn->prepare("SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 5");
$stmtN->execute();
$latest = $stmtN->get_result();

include 'include/header.php';
?>

<!-- HERO SECTION DETAIL -->
<section class="relative w-full py-24 md:py-32 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-40">
        <img src="<?= getImg($berita['gambar'], 'berita') ?>" alt="Berita Banner" class="w-full h-full object-cover object-center mix-blend-overlay blur-sm scale-105">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 fade-in-up">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs md:text-sm font-medium text-gray-300 mb-6 backdrop-blur-sm bg-darkblue/30 inline-flex px-4 py-2 rounded-full border border-white/10">
            <a href="<?= SITE_URL ?>/" class="hover:text-white transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
            <a href="<?= SITE_URL ?>/berita.php" class="hover:text-white transition-colors">Berita</a>
            <i class="fas fa-chevron-right text-[10px] text-gray-500"></i>
            <span class="text-white truncate max-w-[150px] sm:max-w-[300px]"><?= e($berita['judul']) ?></span>
        </nav>
        
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-white leading-tight max-w-4xl">
            <?= e($berita['judul']) ?>
        </h1>
    </div>
</section>

<section class="py-12 bg-softgray">
    <div class="container mx-auto px-4 md:px-6">
        <div class="flex flex-col lg:flex-row gap-8 xl:gap-12">
            
            <!-- KONTEN UTAMA -->
            <div class="lg:w-2/3">
                <article class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-12 scroll-reveal">
                    <?php if (!empty($berita['gambar'])): ?>
                        <div class="relative w-full aspect-[16/9] overflow-hidden">
                            <img src="<?= getImg($berita['gambar'], 'berita') ?>" alt="<?= e($berita['judul']) ?>" class="w-full h-full object-cover">
                            <?php if (!empty($berita['kategori'])): ?>
                                <span class="absolute top-6 left-6 z-10 bg-accent text-white text-xs md:text-sm font-bold px-4 py-1.5 rounded-full shadow-md">
                                    <?= e($berita['kategori']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="p-6 md:p-10">
                        <!-- Meta Info -->
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-8 pb-6 border-b border-gray-100">
                            <div class="flex items-center gap-2 bg-softgray px-3 py-1.5 rounded-full">
                                <i class="fas fa-calendar text-accent"></i>
                                <span class="font-medium"><?= formatTanggal($berita['tgl_post']) ?></span>
                            </div>
                            <div class="flex items-center gap-2 bg-softgray px-3 py-1.5 rounded-full">
                                <i class="fas fa-user text-accent"></i>
                                <span class="font-medium">Admin</span>
                            </div>
                            <?php if (!empty($berita['url_sumber'])): ?>
                            <a href="<?= e($berita['url_sumber']) ?>" target="_blank" rel="noopener" class="flex items-center gap-2 bg-accent/10 text-accent hover:bg-accent hover:text-white px-3 py-1.5 rounded-full transition-colors font-medium ml-auto">
                                <i class="fas fa-external-link-alt"></i> Sumber
                            </a>
                            <?php endif; ?>
                        </div>

                        <!-- Main Content -->
                        <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed text-justify prose-headings:text-darkblue prose-a:text-accent hover:prose-a:text-darkblue prose-img:rounded-xl">
                            <?= $berita['isi'] ?? '<p>Konten berita belum tersedia.</p>' ?>
                        </div>

                        <!-- Share Buttons -->
                        <div class="mt-12 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-4">
                            <span class="text-sm font-bold text-darkblue uppercase tracking-wider">Bagikan:</span>
                            <div class="flex items-center gap-3">
                                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(SITE_URL.'/berita-detail.php?id='.$id) ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-[#1877f2] text-white flex items-center justify-center hover:-translate-y-1 transition-transform shadow-md hover:shadow-lg">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?text=<?= urlencode($berita['judul']) ?>&url=<?= urlencode(SITE_URL.'/berita-detail.php?id='.$id) ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-[#1da1f2] text-white flex items-center justify-center hover:-translate-y-1 transition-transform shadow-md hover:shadow-lg">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text=<?= urlencode($berita['judul'].' - '.SITE_URL.'/berita-detail.php?id='.$id) ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-[#25d366] text-white flex items-center justify-center hover:-translate-y-1 transition-transform shadow-md hover:shadow-lg">
                                    <i class="fab fa-whatsapp text-lg"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>

                <!-- BERITA TERKAIT -->
                <?php if ($related->num_rows > 0): ?>
                    <div class="scroll-reveal delay-100">
                        <div class="flex items-center gap-3 mb-6">
                            <h2 class="text-2xl font-bold text-darkblue_alt">Berita Terkait</h2>
                            <div class="h-1 flex-grow bg-gray-200 rounded-full"></div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                            <?php while ($r = $related->fetch_assoc()): ?>
                                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 group">
                                    <div class="relative h-40 overflow-hidden">
                                        <div class="absolute inset-0 bg-darkblue/10 group-hover:bg-transparent transition-colors z-10"></div>
                                        <img src="<?= getImg($r['gambar'], 'berita') ?>" alt="<?= e($r['judul']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                    </div>
                                    <div class="p-5">
                                        <div class="text-[10px] font-bold text-accent mb-2 uppercase tracking-wider">
                                            <?= formatTanggal($r['tgl_post']) ?>
                                        </div>
                                        <h3 class="font-bold text-darkblue text-sm leading-snug group-hover:text-accent transition-colors">
                                            <a href="berita-detail.php?id=<?= $r['id'] ?>" class="line-clamp-2"><?= e($r['judul']) ?></a>
                                        </h3>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- SIDEBAR -->
            <aside class="lg:w-1/3 space-y-8">
                <!-- Widget: Terbaru -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 scroll-reveal">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center">
                            <i class="fas fa-clock"></i>
                        </div>
                        <h3 class="text-xl font-bold text-darkblue">Terbaru</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <?php while ($n = $latest->fetch_assoc()): ?>
                            <?php if ($n['id'] == $id) continue; ?>
                            <div class="flex gap-4 group">
                                <div class="w-20 h-20 flex-shrink-0 rounded-xl overflow-hidden shadow-sm">
                                    <img src="<?= getImg($n['gambar'], 'berita') ?>" alt="<?= e($n['judul']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                </div>
                                <div class="flex flex-col justify-center">
                                    <h4 class="font-bold text-darkblue_alt text-sm leading-snug mb-1 group-hover:text-accent transition-colors">
                                        <a href="berita-detail.php?id=<?= $n['id'] ?>" class="line-clamp-2"><?= e($n['judul']) ?></a>
                                    </h4>
                                    <div class="text-xs text-gray-500 font-medium">
                                        <?= formatTanggal($n['tgl_post']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Widget: Kategori -->
                <div class="bg-white p-6 md:p-8 rounded-3xl shadow-sm border border-gray-100 scroll-reveal delay-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="text-xl font-bold text-darkblue">Kategori</h3>
                    </div>
                    
                    <div class="flex flex-col gap-2">
                        <?php
                        $kats = $conn->query("SELECT kategori, COUNT(*) as total FROM berita WHERE kategori != '' GROUP BY kategori ORDER BY total DESC");
                        while ($k = $kats->fetch_assoc()):
                        ?>
                            <a href="berita.php?kat=<?= urlencode($k['kategori']) ?>" class="flex items-center justify-between p-3 rounded-xl hover:bg-softgray transition-colors group">
                                <span class="font-semibold text-sm text-gray-600 group-hover:text-darkblue"><?= e($k['kategori']) ?></span>
                                <span class="bg-white border border-gray-100 text-accent text-xs font-bold px-2 py-1 rounded-lg group-hover:bg-accent group-hover:text-white transition-colors">
                                    <?= $k['total'] ?>
                                </span>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </aside>
            
        </div>
    </div>
</section>

<!-- Custom Styles for Prose (since Tailwind typography plugin might not be available via CDN) -->
<style>
.prose p { margin-bottom: 1.25em; }
.prose img { margin-top: 2em; margin-bottom: 2em; border-radius: 0.75rem; }
.prose h2 { font-size: 1.5em; margin-top: 2em; margin-bottom: 1em; font-weight: 700; color: #003087; }
.prose h3 { font-size: 1.25em; margin-top: 1.6em; margin-bottom: 0.6em; font-weight: 700; color: #003087; }
.prose ul { list-style-type: disc; padding-left: 1.625em; margin-bottom: 1.25em; }
.prose ol { list-style-type: decimal; padding-left: 1.625em; margin-bottom: 1.25em; }
.prose li { margin-bottom: 0.5em; }
.prose a { color: #0077B6; text-decoration: underline; font-weight: 500; }
.prose strong { font-weight: 600; color: #0A2540; }
</style>

<?php include 'include/footer.php'; ?>
