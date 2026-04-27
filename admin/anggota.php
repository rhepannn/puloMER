<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$bidang_id = (int)($_GET['bidang_id'] ?? 0);
if (!$bidang_id) redirect(SITE_URL.'/admin/bidang.php');

$s = $conn->prepare("SELECT * FROM bidang WHERE id = ?");
$s->bind_param('i', $bidang_id);
$s->execute();
$bidang = $s->get_result()->fetch_assoc();
if (!$bidang) redirect(SITE_URL.'/admin/bidang.php');

$pageTitle = 'Anggota: ' . $bidang['nama'];

$s2 = $conn->prepare("SELECT * FROM anggota_bidang WHERE bidang_id = ? ORDER BY urutan ASC");
$s2->bind_param('i', $bidang_id);
$s2->execute();
$list = $s2->get_result();

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Bidang & Pengurus</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Kelola Anggota</h2>
        <p class="text-gray-400 text-sm mt-1">Bidang: <span class="font-bold text-darkblue_alt"><?= e($bidang['nama']) ?></span></p>
    </div>
    <div class="flex items-center gap-3 self-start sm:self-auto">
        <a href="bidang.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl transition-all hover:bg-softgray shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i> Kembali
        </a>
        <a href="anggota-add.php?bidang_id=<?= $bidang_id ?>" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-accent/20">
            <i class="fas fa-plus text-xs"></i> Tambah Anggota
        </a>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white">
            <i class="fas fa-users text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Daftar Pengurus & Anggota</h3>
    </div>
    <?php if ($list->num_rows === 0): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-softgray flex items-center justify-center text-gray-300 mb-4"><i class="fas fa-user-slash text-2xl"></i></div>
        <p class="text-gray-400 font-medium mb-3">Belum ada anggota</p>
        <a href="anggota-add.php?bidang_id=<?= $bidang_id ?>" class="text-sm text-accent font-bold hover:underline">+ Tambah sekarang</a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-softgray/50">
                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-10">#</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-16">Foto</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Jabatan</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">No. HP/WA</th>
                    <th class="text-right px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $no = 1; while ($a = $list->fetch_assoc()): ?>
                <tr class="hover:bg-softgray/50 transition-colors group">
                    <td class="px-6 py-3 text-gray-400 text-xs"><?= $no++ ?></td>
                    <td class="px-4 py-3">
                        <img src="<?= getImg($a['foto'], 'bidang') ?>" alt="" class="w-12 h-12 rounded-xl object-cover border border-gray-100 shadow-sm">
                    </td>
                    <td class="px-4 py-3 font-bold text-darkblue_alt group-hover:text-accent transition-colors"><?= e($a['nama']) ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-accent/10 text-accent text-xs font-bold"><?= e($a['jabatan']) ?></span>
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= e($a['no_hp'] ?: '—') ?></td>
                    <td class="px-6 py-3">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="anggota-edit.php?id=<?= $a['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all"><i class="fas fa-edit text-xs"></i></a>
                            <a href="anggota-delete.php?id=<?= $a['id'] ?>" data-confirm="Yakin ingin menghapus anggota ini?" class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all"><i class="fas fa-trash text-xs"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<div class="h-16 lg:h-0"></div>
<?php include 'footer.php'; ?>
