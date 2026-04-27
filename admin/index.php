<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$pageTitle = 'Dashboard';

$stats = [
    'berita'    => $conn->query("SELECT COUNT(*) FROM berita")->fetch_row()[0],
    'kegiatan'  => $conn->query("SELECT COUNT(*) FROM kegiatan")->fetch_row()[0],
    'laporan'   => $conn->query("SELECT COUNT(*) FROM laporan")->fetch_row()[0],
    'kelurahan' => $conn->query("SELECT COUNT(*) FROM kelurahan")->fetch_row()[0],
];

$latestBerita   = $conn->query("SELECT * FROM berita ORDER BY tgl_post DESC LIMIT 5");
$latestKegiatan = $conn->query("SELECT * FROM kegiatan ORDER BY tgl_kegiatan DESC LIMIT 5");

include 'header.php';
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Selamat Datang</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Dashboard</h2>
        <p class="text-gray-400 text-sm mt-1">Panel kontrol Portal PKK Kecamatan Pulomerak</p>
    </div>
    <a href="<?= SITE_URL ?>/" target="_blank"
       class="flex items-center gap-2 bg-darkblue text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-darkblue_alt transition-all shadow-lg shadow-darkblue/20 self-start sm:self-auto">
        <i class="fas fa-external-link-alt text-xs"></i> Lihat Website
    </a>
</div>

<!-- Stat Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <?php
    $statItems = [
        ['berita',    'fa-newspaper',     'bg-blue-500',   'shadow-blue-200',   'Total Berita'],
        ['kegiatan',  'fa-calendar-check','bg-emerald-500','shadow-emerald-200','Total Kegiatan'],
        ['laporan',   'fa-file-alt',      'bg-orange-500', 'shadow-orange-200', 'Total Laporan'],
        ['kelurahan', 'fa-map-marked-alt','bg-violet-500', 'shadow-violet-200', 'Data Warga'],
    ];
    foreach ($statItems as [$key, $icon, $bg, $shadow, $label]):
    ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all flex items-center gap-4">
        <div class="w-12 h-12 <?= $bg ?> rounded-xl flex items-center justify-center text-white shadow-lg <?= $shadow ?> flex-shrink-0">
            <i class="fas <?= $icon ?>"></i>
        </div>
        <div>
            <div class="text-2xl font-black text-darkblue_alt"><?= number_format($stats[$key]) ?></div>
            <div class="text-xs text-gray-400 font-medium"><?= $label ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Quick Shortcuts -->
<div class="mb-8">
    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Pintasan Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <?php
        $shortcuts = [
            ['berita-add.php',   'fa-plus',          'bg-blue-50 text-blue-600 hover:bg-blue-500',   'Tambah Berita'],
            ['kegiatan-add.php', 'fa-calendar-plus', 'bg-emerald-50 text-emerald-600 hover:bg-emerald-500', 'Tambah Kegiatan'],
            ['laporan-add.php',  'fa-file-upload',   'bg-orange-50 text-orange-600 hover:bg-orange-500', 'Upload Laporan'],
            ['kelurahan-add.php','fa-city',          'bg-violet-50 text-violet-600 hover:bg-violet-500', 'Tambah Kelurahan'],
        ];
        foreach ($shortcuts as [$url, $icon, $colClass, $label]):
        ?>
        <a href="<?= $url ?>"
           class="group flex items-center gap-3 bg-white border border-gray-100 rounded-2xl p-4 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all">
            <div class="w-10 h-10 rounded-xl <?= $colClass ?> group-hover:text-white flex items-center justify-center flex-shrink-0 transition-all">
                <i class="fas <?= $icon ?> text-sm"></i>
            </div>
            <span class="text-sm font-bold text-darkblue_alt"><?= $label ?></span>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Recent Tables -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    <!-- Berita Terbaru -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                    <i class="fas fa-newspaper text-xs"></i>
                </div>
                <h3 class="font-bold text-darkblue_alt text-sm">Berita Terbaru</h3>
            </div>
            <a href="berita.php" class="text-xs text-accent font-bold hover:underline">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if ($latestBerita->num_rows === 0): ?>
                <div class="px-6 py-8 text-center text-gray-300 text-sm">Belum ada data</div>
            <?php else: ?>
                <?php while ($b = $latestBerita->fetch_assoc()): ?>
                <div class="flex items-center justify-between px-6 py-3.5 hover:bg-softgray transition-colors group">
                    <div class="flex-1 min-w-0 mr-3">
                        <div class="font-semibold text-darkblue_alt text-sm truncate group-hover:text-accent transition-colors"><?= e($b['judul']) ?></div>
                        <div class="text-xs text-gray-400"><?= e($b['kategori'] ?? '-') ?> · <?= formatTanggal($b['tgl_post']) ?></div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <a href="berita-edit.php?id=<?= $b['id'] ?>"
                           class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all text-xs">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="berita-delete.php?id=<?= $b['id'] ?>"
                           data-confirm="Hapus berita ini?"
                           class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all text-xs">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kegiatan Terbaru -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <i class="fas fa-calendar-check text-xs"></i>
                </div>
                <h3 class="font-bold text-darkblue_alt text-sm">Kegiatan Terbaru</h3>
            </div>
            <a href="kegiatan.php" class="text-xs text-accent font-bold hover:underline">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if ($latestKegiatan->num_rows === 0): ?>
                <div class="px-6 py-8 text-center text-gray-300 text-sm">Belum ada data</div>
            <?php else: ?>
                <?php while ($k = $latestKegiatan->fetch_assoc()): ?>
                <div class="flex items-center justify-between px-6 py-3.5 hover:bg-softgray transition-colors group">
                    <div class="flex-1 min-w-0 mr-3">
                        <div class="font-semibold text-darkblue_alt text-sm truncate group-hover:text-accent transition-colors"><?= e($k['judul']) ?></div>
                        <div class="text-xs text-gray-400"><?= e($k['lokasi'] ?? '-') ?> · <?= formatTanggal($k['tgl_kegiatan']) ?></div>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <a href="kegiatan-edit.php?id=<?= $k['id'] ?>"
                           class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all text-xs">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="kegiatan-delete.php?id=<?= $k['id'] ?>"
                           data-confirm="Hapus kegiatan ini?"
                           class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all text-xs">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bottom padding for mobile nav -->
<div class="h-16 lg:h-0"></div>

<?php include 'footer.php'; ?>
