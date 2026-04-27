<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Berita & Informasi';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;
$search  = trim($_GET['q'] ?? '');
$kat     = trim($_GET['kat'] ?? '');
$kel     = trim($_GET['kel'] ?? '');

// Count
$whereClause = '1=1';
$params = [];
$types  = '';
if ($search) { $whereClause .= " AND judul LIKE ?"; $params[] = "%$search%"; $types .= 's'; }
if ($kat)    { $whereClause .= " AND kategori = ?"; $params[] = $kat; $types .= 's'; }
if ($kel !== '') { $whereClause .= " AND kelurahan_id = ?"; $params[] = (int)$kel; $types .= 'i'; }

$stmtC = $conn->prepare("SELECT COUNT(*) FROM berita WHERE $whereClause");
if ($params) $stmtC->bind_param($types, ...$params);
$stmtC->execute();
$total = $stmtC->get_result()->fetch_row()[0];

// List
$stmtL = $conn->prepare("SELECT * FROM berita WHERE $whereClause ORDER BY tgl_post DESC LIMIT ? OFFSET ?");
$params2 = array_merge($params, [$perPage, $offset]);
$types2  = $types . 'ii';
$stmtL->bind_param($types2, ...$params2);
$stmtL->execute();
$beritaList = $stmtL->get_result();

// Kategori untuk filter
$kats = $conn->query("SELECT DISTINCT kategori FROM berita WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");

// ID terbaru untuk realtime
$rtBerita = $conn->query("SELECT MAX(id) FROM berita")->fetch_row()[0] ?? 0;

include 'include/header.php'; ?>
<script>
window.SITE_URL  = '<?= SITE_URL ?>';
window.RT_BERITA = <?= (int)$rtBerita ?>;
window.RT_PAGE   = 'berita';
</script>

<?php 
// Ambil setting hero berita
$heroTitle    = getSetting($conn, 'berita_hero_title', 'Berita & <span class="text-accent">Informasi</span>');
$heroSubtitle = getSetting($conn, 'berita_hero_subtitle', 'Ikuti perkembangan kegiatan dan kabar terbaru dari seluruh Kelurahan di Kecamatan Pulomerak.');
$heroImage    = getSetting($conn, 'berita_hero_image', '7.png');
$heroUrl      = getImg($heroImage, 'settings', '7.png');
?>

<!-- HERO SECTION -->
<section class="relative w-full py-20 lg:py-28 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-50">
        <img src="<?= $heroUrl ?>" alt="Berita" class="w-full h-full object-cover object-center mix-blend-overlay">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-white border border-white/20 text-xs font-semibold tracking-wider uppercase mb-4 backdrop-blur-md">
            Pusat Informasi
        </span>
        <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-4">
            <?= $heroTitle ?>
        </h1>
        <p class="text-gray-300 max-w-2xl mx-auto font-light md:text-lg">
            <?= e($heroSubtitle) ?>
        </p>
    </div>
</section>

<section class="py-12 bg-softgray min-h-[50vh]">
    <div class="container mx-auto px-4 md:px-6">
        
        <!-- SEARCH + FILTER BAR -->
        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 -mt-20 relative z-20">
            <form method="GET" action="berita.php" class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari judul berita..." class="w-full pl-11 pr-4 py-3 bg-softgray border-transparent focus:border-accent focus:bg-white focus:ring-0 rounded-xl transition-colors text-sm font-medium text-darkblue" id="searchBerita">
                </div>
                
                <div class="w-full md:w-64 flex-shrink-0 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <select name="kel" class="w-full pl-11 pr-10 py-3 bg-softgray border-transparent focus:border-accent focus:bg-white focus:ring-0 rounded-xl transition-colors text-sm font-medium text-darkblue appearance-none" onchange="this.form.submit()">
                        <option value="">Semua Lokasi</option>
                        <option value="0" <?= $kel === '0' ? 'selected' : '' ?>>Kecamatan (Pusat)</option>
                        <?php 
                        $kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");
                        while($k = $kels->fetch_assoc()): 
                        ?>
                            <option value="<?= $k['id'] ?>" <?= $kel == $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>

                <button type="submit" class="bg-darkblue hover:bg-darkblue_alt text-white px-8 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                    Cari
                </button>
            </form>

            <!-- Kategori Chips -->
            <?php if($kats->num_rows > 0): ?>
            <div class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-100">
                <a href="berita.php" class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors <?= !$kat ? 'bg-accent text-white shadow-sm' : 'bg-softgray text-gray-500 hover:bg-gray-200' ?>">Semua</a>
                <?php while ($k = $kats->fetch_assoc()): ?>
                    <a href="berita.php?kat=<?= urlencode($k['kategori']) ?><?= $search ? '&q='.urlencode($search) : '' ?>"
                       class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors <?= $kat === $k['kategori'] ? 'bg-accent text-white shadow-sm' : 'bg-softgray text-gray-500 hover:bg-gray-200' ?>">
                        <?= e($k['kategori']) ?>
                    </a>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- BERITA GRID -->
        <?php if ($beritaList->num_rows === 0): ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-20 h-20 mx-auto bg-softgray rounded-full flex items-center justify-center text-gray-400 mb-4 text-3xl">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="text-xl font-bold text-darkblue mb-2">Berita Tidak Ditemukan</h3>
                <p class="text-gray-500">Coba gunakan kata kunci lain atau pilih kategori yang berbeda.</p>
                <a href="berita.php" class="inline-block mt-6 px-6 py-2 bg-darkblue text-white rounded-lg text-sm font-medium hover:bg-darkblue_alt transition-colors">Lihat Semua Berita</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                <?php 
                $delay = 0;
                while ($b = $beritaList->fetch_assoc()): 
                    $delay += 100;
                    if($delay > 300) $delay = 100;
                ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group flex flex-col scroll-reveal delay-<?= $delay ?>">
                        <!-- Image Container with Skew Hover Effect -->
                        <div class="relative h-56 overflow-hidden">
                            <div class="absolute inset-0 bg-darkblue/10 group-hover:bg-transparent transition-colors z-10"></div>
                            <img src="<?= getImg($b['gambar'], 'berita') ?>" alt="<?= e($b['judul']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy">
                            <?php if (!empty($b['kategori'])): ?>
                                <span class="absolute top-4 left-4 z-20 bg-accent text-white text-xs font-bold px-3 py-1 rounded-full shadow-md">
                                    <?= e($b['kategori']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-6 flex flex-col flex-grow">
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-3 font-medium">
                                <span class="flex items-center gap-1.5"><i class="fas fa-calendar text-accent"></i> <?= formatTanggal($b['tgl_post']) ?></span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-user text-accent"></i> Admin</span>
                            </div>
                            
                            <h3 class="text-lg font-bold text-darkblue_alt mb-3 leading-snug group-hover:text-accent transition-colors">
                                <a href="berita-detail.php?id=<?= $b['id'] ?>" class="line-clamp-2"><?= e($b['judul']) ?></a>
                            </h3>
                            
                            <div class="mt-auto pt-4 flex items-center justify-between border-t border-gray-50">
                                <a href="berita-detail.php?id=<?= $b['id'] ?>" class="text-accent font-semibold text-sm flex items-center gap-2 group/link">
                                    Selengkapnya 
                                    <i class="fas fa-arrow-right transform group-hover/link:translate-x-1 transition-transform"></i>
                                </a>
                                <?php if (!empty($b['url_sumber'])): ?>
                                    <a href="<?= e($b['url_sumber']) ?>" target="_blank" rel="noopener" class="text-gray-400 hover:text-darkblue text-xs flex items-center gap-1 transition-colors">
                                        <i class="fas fa-external-link-alt"></i> Sumber
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- PAGINATION -->
            <div class="mt-12 flex justify-center">
                <?php
                // Note: paginate() function returns raw HTML. If the current function returns old CSS classes, 
                // it might need an update or we use a Tailwind wrapper. Assuming it outputs standard <ul><li> items.
                $baseUrl = 'berita.php?';
                if ($search) $baseUrl .= 'q=' . urlencode($search) . '&';
                if ($kat)    $baseUrl .= 'kat=' . urlencode($kat) . '&';
                if ($kel !== '') $baseUrl .= 'kel=' . urlencode($kel) . '&';
                
                $paginationHtml = paginate($total, $page, $perPage, $baseUrl);
                // Simple string replacement if paginate() uses old classes. 
                // Better approach: wrap it in a container that styles it.
                ?>
                <div class="tailwind-pagination">
                    <?= $paginationHtml ?>
                </div>
                <style>
                    .tailwind-pagination .pagination { display: flex; gap: 0.5rem; list-style: none; padding: 0; margin: 0; }
                    .tailwind-pagination .pagination li a, .tailwind-pagination .pagination li span { 
                        display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; 
                        border-radius: 0.5rem; font-size: 0.875rem; font-weight: 600; color: #0A2540; background-color: #fff; 
                        border: 1px solid #e2e8f0; transition: all 0.2s; 
                    }
                    .tailwind-pagination .pagination li a:hover { background-color: #0077B6; color: #fff; border-color: #0077B6; }
                    .tailwind-pagination .pagination li.active span { background-color: #003087; color: #fff; border-color: #003087; }
                    .tailwind-pagination .pagination li.disabled span { color: #94a3b8; background-color: #f8fafc; cursor: not-allowed; }
                </style>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'include/footer.php'; ?>
