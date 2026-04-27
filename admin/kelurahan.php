
<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
if (!isSuperAdmin()) { setFlash('error', 'Halaman ini hanya dapat diakses oleh Superadmin.'); redirect(SITE_URL.'/admin/index.php'); }
$pageTitle = 'Manajemen Kelurahan';
$list = $conn->query("SELECT * FROM kelurahan ORDER BY nama");
include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Wilayah & Statistik</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Manajemen Kelurahan</h2>
        <p class="text-gray-400 text-sm mt-1">Data statistik kependudukan wilayah Pulomerak</p>
    </div>
    <a href="kelurahan-add.php" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-accent/20 self-start sm:self-auto">
        <i class="fas fa-plus text-xs"></i> Tambah Data
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
            <i class="fas fa-map-marked-alt text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Daftar Statistik Wilayah</h3>
    </div>
    <?php if ($list->num_rows === 0): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-softgray flex items-center justify-center text-gray-300 mb-4"><i class="fas fa-city text-2xl"></i></div>
        <p class="text-gray-400 font-medium">Belum ada data kelurahan</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-softgray/50">
                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-10">#</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-16">Foto</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Kelurahan</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Ketua TP PKK</th>
                    <th class="text-center px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">RW</th>
                    <th class="text-center px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">RT</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Penduduk</th>
                    <th class="text-right px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $no = 1; while ($k = $list->fetch_assoc()): ?>
                <tr class="hover:bg-softgray/50 transition-colors group">
                    <td class="px-6 py-3 text-gray-400 text-xs"><?= $no++ ?></td>
                    <td class="px-4 py-3">
                        <img src="<?= getImg($k['gambar'], 'kegiatan') ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100">
                    </td>
                    <td class="px-4 py-3">
                        <div class="font-bold text-darkblue_alt group-hover:text-accent transition-colors"><?= e($k['nama']) ?></div>
                        <?php if (!empty($k['inovasi'])): ?>
                        <div class="text-xs text-amber-500 mt-0.5"><i class="fas fa-lightbulb mr-1"></i>Ada program inovasi</div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= e($k['ketua_pkk'] ?: 'Belum diatur') ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold"><?= e($k['jumlah_rw'] ?? '-') ?></span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-xs font-bold"><?= e($k['jumlah_rt'] ?? '-') ?></span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= !empty($k['penduduk']) ? number_format($k['penduduk']).' jiwa' : '-' ?></td>
                    <td class="px-6 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="kelurahan-export.php?id=<?= $k['id'] ?>" class="w-8 h-8 rounded-lg bg-orange-50 text-orange-500 hover:bg-orange-500 hover:text-white flex items-center justify-center transition-all" title="Laporan PDF"><i class="fas fa-file-pdf text-xs"></i></a>
                            <a href="../kelurahan-detail.php?id=<?= $k['id'] ?>" target="_blank" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-accent hover:text-white flex items-center justify-center transition-all" title="Preview"><i class="fas fa-eye text-xs"></i></a>
                            <a href="kelurahan-edit.php?id=<?= $k['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all" title="Edit"><i class="fas fa-edit text-xs"></i></a>
                            <a href="kelurahan-delete.php?id=<?= $k['id'] ?>" data-confirm="Yakin hapus data kelurahan ini?" class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all" title="Hapus"><i class="fas fa-trash text-xs"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="h-16 lg:h-0"></div>
<?php include 'footer.php'; ?>
