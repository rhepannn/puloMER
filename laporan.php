<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Laporan & Arsip';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;
$offset  = ($page - 1) * $perPage;
$search  = trim($_GET['q'] ?? '');

$where = '1=1'; $params = []; $types = '';
if ($search) { $where .= " AND (judul LIKE ? OR deskripsi LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; $types .= 'ss'; }

$stmtC = $conn->prepare("SELECT COUNT(*) FROM laporan WHERE $where");
if ($params) $stmtC->bind_param($types, ...$params);
$stmtC->execute();
$total = $stmtC->get_result()->fetch_row()[0];

$stmtL = $conn->prepare("SELECT * FROM laporan WHERE $where ORDER BY tgl_upload DESC LIMIT ? OFFSET ?");
$p2 = array_merge($params, [$perPage, $offset]);
$t2 = $types . 'ii';
$stmtL->bind_param($t2, ...$p2);
$stmtL->execute();
$list = $stmtL->get_result();

include 'include/header.php';
?>

<?php 
// Ambil setting hero laporan
$heroTitle    = getSetting($conn, 'laporan_hero_title', 'Laporan & <span class="text-accent">Arsip</span>');
$heroSubtitle = getSetting($conn, 'laporan_hero_subtitle', 'Transparansi data dan laporan capaian program kerja TP PKK Kecamatan Pulomerak.');
$heroImage    = getSetting($conn, 'laporan_hero_image', '9.png');
$heroUrl      = getImg($heroImage, 'settings', '9.png');
?>

<!-- HERO SECTION -->
<section class="relative w-full py-20 lg:py-28 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-50">
        <img src="<?= $heroUrl ?>" alt="Laporan dan Arsip" class="w-full h-full object-cover object-center mix-blend-overlay">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-white border border-white/20 text-xs font-semibold tracking-wider uppercase mb-4 backdrop-blur-md">
            Transparansi
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
        
        <!-- SEARCH BAR -->
        <div class="max-w-3xl mx-auto bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 mb-12 -mt-20 relative z-20">
            <form method="GET" action="laporan.php" class="flex flex-col sm:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                        <i class="fas fa-search"></i>
                    </div>
                    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari judul atau deskripsi laporan..." class="w-full pl-11 pr-4 py-3 bg-softgray border-transparent focus:border-accent focus:bg-white focus:ring-0 rounded-xl transition-colors text-sm font-medium text-darkblue">
                </div>
                <button type="submit" class="bg-darkblue hover:bg-darkblue_alt text-white px-8 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center gap-2 shadow-md">
                    Cari
                </button>
            </form>
        </div>

        <!-- LAPORAN LIST -->
        <?php if ($list->num_rows === 0): ?>
            <div class="max-w-3xl mx-auto text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-20 h-20 mx-auto bg-softgray rounded-full flex items-center justify-center text-gray-400 mb-4 text-3xl">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-darkblue mb-2">Belum Ada Laporan</h3>
                <p class="text-gray-500">Laporan atau arsip yang Anda cari belum tersedia.</p>
                <?php if($search): ?>
                    <a href="laporan.php" class="inline-block mt-6 px-6 py-2 bg-darkblue text-white rounded-lg text-sm font-medium hover:bg-darkblue_alt transition-colors">Tampilkan Semua</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="max-w-4xl mx-auto space-y-4">
                <?php 
                $delay = 0;
                while ($l = $list->fetch_assoc()):
                    $delay += 100;
                    if($delay > 500) $delay = 100;
                    
                    $ext = strtolower(pathinfo($l['file'] ?? '', PATHINFO_EXTENSION));
                    
                    // Determine colors based on file extension
                    if (in_array($ext, ['pdf'])) {
                        $iconClass = 'text-red-500 bg-red-50 group-hover:bg-red-500 group-hover:text-white';
                        $icon = 'fa-file-pdf';
                    } elseif (in_array($ext, ['xls', 'xlsx'])) {
                        $iconClass = 'text-green-500 bg-green-50 group-hover:bg-green-500 group-hover:text-white';
                        $icon = 'fa-file-excel';
                    } elseif (in_array($ext, ['doc', 'docx'])) {
                        $iconClass = 'text-blue-500 bg-blue-50 group-hover:bg-blue-500 group-hover:text-white';
                        $icon = 'fa-file-word';
                    } else {
                        $iconClass = 'text-gray-500 bg-gray-100 group-hover:bg-gray-500 group-hover:text-white';
                        $icon = 'fa-file-alt';
                    }
                ?>
                    <div class="bg-white rounded-2xl p-5 md:p-6 shadow-sm hover:shadow-md border border-gray-100 transition-all duration-300 flex flex-col md:flex-row items-start md:items-center gap-5 scroll-reveal delay-<?= $delay ?> group">
                        
                        <div class="w-14 h-14 flex-shrink-0 rounded-xl flex items-center justify-center text-2xl transition-colors duration-300 <?= $iconClass ?>">
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        
                        <div class="flex-grow min-w-0">
                            <h3 class="text-base md:text-lg font-bold text-darkblue_alt mb-1 leading-snug group-hover:text-accent transition-colors truncate">
                                <?= e($l['judul']) ?>
                            </h3>
                            <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 font-medium mb-2">
                                <span class="flex items-center gap-1.5"><i class="far fa-calendar-alt text-accent"></i> <?= formatTanggal($l['tgl_upload']) ?></span>
                                <?php if (!empty($ext)): ?>
                                    <span class="flex items-center gap-1.5 uppercase"><i class="fas fa-file text-accent"></i> <?= $ext ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($l['deskripsi'])): ?>
                                <p class="text-gray-600 text-sm line-clamp-2 md:line-clamp-1"><?= e(truncate($l['deskripsi'], 120)) ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex-shrink-0 mt-4 md:mt-0 w-full md:w-auto text-center">
                            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 text-gray-400 font-medium text-xs border border-gray-100">
                                <i class="fas fa-file-alt"></i> Arsip Digital
                            </span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- PAGINATION -->
            <div class="mt-12 flex justify-center">
                <?php
                $bu = 'laporan.php?';
                if ($search) $bu .= 'q='.urlencode($search).'&';
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
    </div>
</section>

<?php include 'include/footer.php'; ?>
