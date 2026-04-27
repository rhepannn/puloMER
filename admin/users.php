<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
if (!isSuperAdmin()) { setFlash('error', 'Halaman ini hanya dapat diakses oleh Superadmin.'); redirect(SITE_URL.'/admin/index.php'); }
$pageTitle = 'Manajemen User';

$sql  = "SELECT u.*, k.nama as nama_kelurahan FROM users u LEFT JOIN kelurahan k ON u.kelurahan_id = k.id ORDER BY u.role, u.username";
$list = $conn->query($sql);

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Pengaturan</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Manajemen User</h2>
        <p class="text-gray-400 text-sm mt-1">Kelola akun admin kecamatan dan kelurahan</p>
    </div>
    <a href="users-add.php" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold text-sm px-5 py-2.5 rounded-xl transition-all shadow-lg shadow-accent/20 self-start sm:self-auto">
        <i class="fas fa-user-plus text-xs"></i> Tambah User
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center text-violet-500">
            <i class="fas fa-user-shield text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Daftar Pengguna</h3>
    </div>
    <?php if ($list->num_rows === 0): ?>
    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-softgray flex items-center justify-center text-gray-300 mb-4"><i class="fas fa-user-slash text-2xl"></i></div>
        <p class="text-gray-400 font-medium">Belum ada user tambahan</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-50 bg-softgray/50">
                    <th class="text-left px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest w-10">#</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Username</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Nama Lengkap</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Role</th>
                    <th class="text-left px-4 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Penugasan</th>
                    <th class="text-right px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $no = 1; while ($u = $list->fetch_assoc()): ?>
                <tr class="hover:bg-softgray/50 transition-colors group">
                    <td class="px-6 py-4 text-gray-400 text-xs"><?= $no++ ?></td>
                    <td class="px-4 py-4 font-bold text-darkblue_alt group-hover:text-accent transition-colors"><?= e($u['username']) ?></td>
                    <td class="px-4 py-4 text-gray-600"><?= e($u['nama']) ?></td>
                    <td class="px-4 py-4">
                        <?php if ($u['kelurahan_id'] === null): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-darkblue/10 text-darkblue text-xs font-bold">
                            <i class="fas fa-crown text-[10px]"></i> Superadmin
                        </span>
                        <?php else: ?>
                        <span class="inline-flex px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold">Admin Kelurahan</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-4 text-sm">
                        <?php if ($u['kelurahan_id'] === null): ?>
                        <span class="text-gray-400 italic text-xs">Seluruh Wilayah</span>
                        <?php else: ?>
                        <span class="text-gray-600"><i class="fas fa-city text-accent text-xs mr-1"></i><?= e($u['nama_kelurahan']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1.5">
                            <a href="users-edit.php?id=<?= $u['id'] ?>" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all"><i class="fas fa-edit text-xs"></i></a>
                            <?php if ($u['id'] != $_SESSION['admin_id']): ?>
                            <a href="users-delete.php?id=<?= $u['id'] ?>" data-confirm="Yakin ingin menghapus user '<?= e($u['username']) ?>'?"
                               class="w-8 h-8 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all"><i class="fas fa-trash text-xs"></i></a>
                            <?php endif; ?>
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
