<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$pageTitle = 'Kelola Bidang';
$stmt = $conn->query("SELECT id, nama, slug, (SELECT COUNT(*) FROM anggota_bidang WHERE bidang_id = bidang.id) as total_anggota FROM bidang ORDER BY urutan ASC");
include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Organisasi</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Kelola Bidang & POKJA</h2>
    </div>
    <?php if (isSuperAdmin()): ?>
    <a href="bidang-add.php" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-accent/20 self-start sm:self-auto">
        <i class="fas fa-plus text-xs"></i> Tambah Bidang
    </a>
    <?php endif; ?>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center text-violet-500">
            <i class="fas fa-sitemap text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Daftar Bidang & POKJA</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-softgray/50">
                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-10">#</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Bidang</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Slug</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Anggota</th>
                    <th class="text-right px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $no = 1; while ($b = $stmt->fetch_assoc()): ?>
                <tr class="hover:bg-softgray/50 transition-colors group">
                    <td class="px-6 py-4 text-gray-400 text-xs"><?= $no++ ?></td>
                    <td class="px-4 py-4">
                        <div class="font-bold text-darkblue_alt group-hover:text-accent transition-colors"><?= e($b['nama']) ?></div>
                    </td>
                    <td class="px-4 py-4">
                        <code class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg"><?= e($b['slug']) ?></code>
                    </td>
                    <td class="px-4 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-violet-50 text-violet-700 text-xs font-bold">
                            <i class="fas fa-users text-[10px]"></i> <?= $b['total_anggota'] ?> Orang
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="anggota.php?bidang_id=<?= $b['id'] ?>"
                               class="flex items-center gap-1.5 h-8 px-3 rounded-lg bg-accent text-white hover:bg-darkblue flex-shrink-0 transition-all text-xs font-bold">
                                <i class="fas fa-users text-[10px]"></i> Anggota
                            </a>
                            <a href="bidang-edit.php?id=<?= $b['id'] ?>"
                               class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <?php if (isSuperAdmin()): ?>
                            <a href="bidang-delete.php?id=<?= $b['id'] ?>"
                               data-confirm="Hapus bidang ini? Semua anggota di dalamnya juga akan terhapus."
                               class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="h-16 lg:h-0"></div>
<?php include 'footer.php'; ?>
