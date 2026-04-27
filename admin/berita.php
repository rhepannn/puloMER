<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$pageTitle = 'Manajemen Berita';

$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;
$offset  = ($page - 1) * $perPage;

$kelIdFilter = $_GET['kelurahan'] ?? '';
$kelId       = getKelurahanId();

if (isSuperAdmin()) {
    $where = $kelIdFilter !== '' ? " WHERE kelurahan_id = " . (int)$kelIdFilter : "";
} else {
    $where = " WHERE kelurahan_id = " . (int)$kelId;
}

$total = $conn->query("SELECT COUNT(*) FROM berita" . $where)->fetch_row()[0];
$sql   = "SELECT * FROM berita" . $where . " ORDER BY tgl_post DESC LIMIT ? OFFSET ?";
$stmt  = $conn->prepare($sql);
$stmt->bind_param('ii', $perPage, $offset);
$stmt->execute();
$list = $stmt->get_result();
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");

include 'header.php';
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Konten</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Manajemen Berita</h2>
        <p class="text-gray-400 text-sm mt-1">Total <span class="font-bold text-accent"><?= number_format($total) ?></span> berita</p>
    </div>
    <div class="flex items-center gap-3 flex-shrink-0">
        <?php if (isSuperAdmin()): ?>
        <form method="GET">
            <select name="kelurahan" onchange="this.form.submit()"
                    class="bg-white border border-gray-200 text-darkblue_alt text-sm rounded-xl px-3 py-2.5 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent/20 shadow-sm">
                <option value="">Semua Kelurahan</option>
                <option value="0" <?= $kelIdFilter === '0' ? 'selected' : '' ?>>Kecamatan (Pusat)</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= $kelIdFilter == $k['id'] ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </form>
        <?php endif; ?>
        <a href="berita-add.php"
           class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-accent/20">
            <i class="fas fa-plus text-xs"></i> Tambah Berita
        </a>
    </div>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
            <i class="fas fa-newspaper text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Daftar Berita</h3>
    </div>

    <?php if ($list->num_rows === 0): ?>
    <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
        <div class="w-16 h-16 rounded-2xl bg-softgray flex items-center justify-center text-gray-300 mb-4">
            <i class="fas fa-newspaper text-2xl"></i>
        </div>
        <p class="text-gray-400 font-medium mb-3">Belum ada berita</p>
        <a href="berita-add.php" class="text-sm text-accent font-bold hover:underline">+ Tambah sekarang</a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-softgray/50">
                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-10">#</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-16">Foto</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Judul</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kategori</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">Tanggal</th>
                    <th class="text-right px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $no = $offset + 1; while ($b = $list->fetch_assoc()): ?>
                <tr class="hover:bg-softgray/50 transition-colors group">
                    <td class="px-6 py-3 text-gray-400 text-xs font-medium"><?= $no++ ?></td>
                    <td class="px-4 py-3">
                        <img src="<?= getImg($b['gambar'], 'berita') ?>" alt=""
                             class="w-12 h-12 rounded-xl object-cover border border-gray-100 shadow-sm">
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-semibold text-darkblue_alt group-hover:text-accent transition-colors max-w-xs truncate"><?= e($b['judul']) ?></div>
                        <?php if (!empty($b['url_sumber'])): ?>
                        <div class="text-xs text-gray-400 mt-0.5"><i class="fas fa-link mr-1"></i>Ada link sumber</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold"><?= e($b['kategori'] ?? '-') ?></span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-400 whitespace-nowrap"><?= formatTanggal($b['tgl_post']) ?></td>
                    <td class="px-6 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="../berita-detail.php?id=<?= $b['id'] ?>" target="_blank"
                               class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-accent hover:text-white flex items-center justify-center transition-all" title="Preview">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="berita-edit.php?id=<?= $b['id'] ?>"
                               class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <a href="berita-delete.php?id=<?= $b['id'] ?>"
                               data-confirm="Yakin hapus berita '<?= e(addslashes($b['judul'])) ?>'?"
                               class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all" title="Hapus">
                                <i class="fas fa-trash text-xs"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Pagination -->
<div class="mt-6">
    <?= paginate($total, $page, $perPage, 'berita.php?') ?>
</div>

<div class="h-16 lg:h-0"></div>
<?php include 'footer.php'; ?>
