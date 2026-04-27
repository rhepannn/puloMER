<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Galeri Kegiatan';

// Ambil semua kategori
$kats = $conn->query("SELECT DISTINCT kategori FROM kegiatan WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");

$kat    = trim($_GET['kat'] ?? '');
$search = trim($_GET['q'] ?? '');
$kel    = trim($_GET['kel'] ?? '');
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 12;
$offset  = ($page - 1) * $perPage;

$where  = '1=1';
$params = [];
$types  = '';
if ($kat)    { $where .= " AND kategori = ?"; $params[] = $kat; $types .= 's'; }
if ($search) { $where .= " AND judul LIKE ?"; $params[] = "%$search%"; $types .= 's'; }
if ($kel !== '') { $where .= " AND kelurahan_id = ?"; $params[] = (int)$kel; $types .= 'i'; }

$stmtC = $conn->prepare("SELECT COUNT(*) FROM kegiatan WHERE $where");
if ($params) $stmtC->bind_param($types, ...$params);
$stmtC->execute();
$total = $stmtC->get_result()->fetch_row()[0];

$stmtL = $conn->prepare("SELECT * FROM kegiatan WHERE $where ORDER BY tgl_kegiatan DESC LIMIT ? OFFSET ?");
$p2 = array_merge($params, [$perPage, $offset]);
$t2 = $types . 'ii';
$stmtL->bind_param($t2, ...$p2);
$stmtL->execute();
$list = $stmtL->get_result();

$rtKegiatan = $conn->query("SELECT MAX(id) FROM kegiatan")->fetch_row()[0] ?? 0;

include 'include/header.php'; ?>
<script>
window.SITE_URL    = '<?= SITE_URL ?>';
window.RT_KEGIATAN = <?= (int)$rtKegiatan ?>;
window.RT_PAGE     = 'kegiatan';
</script>

<?php 
// Ambil setting hero kegiatan
$heroTitle    = getSetting($conn, 'kegiatan_hero_title', 'Galeri <span class="text-accent">Kegiatan</span>');
$heroSubtitle = getSetting($conn, 'kegiatan_hero_subtitle', 'Dokumentasi berbagai kegiatan pemberdayaan masyarakat di wilayah Kecamatan Pulomerak.');
$heroImage    = getSetting($conn, 'kegiatan_hero_image', '8.png');
$heroUrl      = getImg($heroImage, 'settings', '8.png');
?>

<!-- HERO SECTION -->
<section class="relative w-full py-20 lg:py-28 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-50">
        <img src="<?= $heroUrl ?>" alt="Kegiatan PKK" class="w-full h-full object-cover object-center mix-blend-overlay">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-white border border-white/20 text-xs font-semibold tracking-wider uppercase mb-4 backdrop-blur-md">
            Dokumentasi
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
            <form method="GET" action="kegiatan.php" class="flex flex-col md:flex-row gap-4">
                <?php if ($kat): ?><input type="hidden" name="kat" value="<?= e($kat) ?>"><?php endif; ?>
                
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari kegiatan..." class="w-full pl-11 pr-4 py-3 bg-softgray border-transparent focus:border-accent focus:bg-white focus:ring-0 rounded-xl transition-colors text-sm font-medium text-darkblue">
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
                <a href="kegiatan.php<?= $search ? '?q='.urlencode($search) : '' ?><?= $kel !== '' ? '&kel='.urlencode($kel) : '' ?>" 
                   class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors <?= !$kat ? 'bg-accent text-white shadow-sm' : 'bg-softgray text-gray-500 hover:bg-gray-200' ?>">Semua</a>
                <?php 
                $kats->data_seek(0);
                while ($k = $kats->fetch_assoc()): 
                ?>
                    <a href="kegiatan.php?kat=<?= urlencode($k['kategori']) ?><?= $search ? '&q='.urlencode($search) : '' ?><?= $kel !== '' ? '&kel='.urlencode($kel) : '' ?>"
                       class="px-4 py-1.5 rounded-full text-xs font-semibold transition-colors <?= $kat === $k['kategori'] ? 'bg-accent text-white shadow-sm' : 'bg-softgray text-gray-500 hover:bg-gray-200' ?>">
                        <?= e($k['kategori']) ?>
                    </a>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- KEGIATAN GRID -->
        <?php if ($list->num_rows === 0): ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-20 h-20 mx-auto bg-softgray rounded-full flex items-center justify-center text-gray-400 mb-4 text-3xl">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="text-xl font-bold text-darkblue mb-2">Belum Ada Kegiatan</h3>
                <p class="text-gray-500">Data kegiatan yang dicari tidak ditemukan.</p>
                <a href="kegiatan.php" class="inline-block mt-6 px-6 py-2 bg-darkblue text-white rounded-lg text-sm font-medium hover:bg-darkblue_alt transition-colors">Lihat Semua</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 lg:gap-8">
                <?php 
                $delay = 0;
                while ($k = $list->fetch_assoc()): 
                    $delay += 100;
                    if($delay > 400) $delay = 100;
                    $shareText = urlencode("Lihat kegiatan: " . $k['judul'] . " di TP PKK Kecamatan Pulomerak. " . SITE_URL . "/kegiatan-detail.php?id=" . $k['id']);
                ?>
                    <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group flex flex-col scroll-reveal delay-<?= $delay ?>">
                        <!-- Image Container with Skew Effect -->
                        <div class="relative h-52 overflow-hidden bg-softgray">
                            <div class="absolute inset-0 bg-darkblue/10 group-hover:bg-transparent transition-colors z-10"></div>
                            <img src="<?= getImg($k['gambar'], 'kegiatan') ?>" alt="<?= e($k['judul']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110 group-hover:-rotate-1" loading="lazy">
                            <?php if (!empty($k['kategori'])): ?>
                                <span class="absolute top-4 right-4 z-20 bg-accent/90 backdrop-blur-sm text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wider">
                                    <?= e($k['kategori']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="p-5 flex flex-col flex-grow">
                            <h3 class="text-base font-bold text-darkblue_alt mb-2 leading-snug group-hover:text-accent transition-colors line-clamp-2">
                                <a href="kegiatan-detail.php?id=<?= $k['id'] ?>"><?= e($k['judul']) ?></a>
                            </h3>
                            
                            <div class="flex items-center gap-2 text-[11px] text-gray-500 mb-3 font-semibold uppercase tracking-wider">
                                <i class="far fa-calendar-alt text-accent"></i> <?= formatTanggal($k['tgl_kegiatan']) ?>
                            </div>
                            
                            <div class="text-gray-600 text-sm line-clamp-3 mb-4 leading-relaxed">
                                <?= e(truncate($k['deskripsi'], 120)) ?>
                            </div>
                            
                            <div class="mt-auto pt-4 border-t border-gray-50 flex items-center justify-between">
                                <button class="flex items-center gap-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded-lg transition-colors font-bold text-sm" onclick="likeKegiatan(<?= $k['id'] ?>, this)">
                                    <i class="fas fa-heart transform transition-transform"></i> 
                                    <span class="like-count"><?= number_format($k['likes'] ?? 0) ?></span>
                                </button>
                                
                                <div class="flex items-center gap-3">
                                    <a href="kegiatan-detail.php?id=<?= $k['id'] ?>" class="text-accent hover:text-darkblue font-semibold text-xs transition-colors flex items-center gap-1 group/link">
                                        Detail <i class="fas fa-chevron-right text-[10px] transform group-hover/link:translate-x-0.5 transition-transform"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text=<?= $shareText ?>" target="_blank" rel="noopener" class="w-7 h-7 rounded-full bg-[#25D366]/10 text-[#25D366] flex items-center justify-center hover:bg-[#25D366] hover:text-white transition-colors" title="Bagikan ke WhatsApp">
                                        <i class="fab fa-whatsapp"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- PAGINATION -->
            <div class="mt-12 flex justify-center">
                <?php
                $bu = 'kegiatan.php?';
                if ($kat)    $bu .= 'kat='.urlencode($kat).'&';
                if ($search) $bu .= 'q='.urlencode($search).'&';
                if ($kel !== '') $bu .= 'kel='.urlencode($kel).'&';
                
                $paginationHtml = paginate($total, $page, $perPage, $bu);
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

        <script>
        function likeKegiatan(id, btn) {
            if(btn.classList.contains('text-red-500')) return;
            
            const icon = btn.querySelector('i');
            const countSpan = btn.querySelector('.like-count');
            
            icon.style.transform = 'scale(1.3)';
            btn.classList.remove('text-gray-400', 'hover:bg-red-50');
            btn.classList.add('text-red-500', 'bg-red-50');
            
            fetch('api/like-kegiatan.php?id=' + id)
                .then(res => res.json())
                .then(data => {
                    if(data.success) {
                        countSpan.textContent = data.new_likes;
                    }
                    setTimeout(() => icon.style.transform = 'scale(1)', 200);
                });
        }
        </script>
    </div>
</section>

<?php include 'include/footer.php'; ?>
