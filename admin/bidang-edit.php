<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL . '/admin/bidang.php');

$stmt = $conn->prepare("SELECT * FROM bidang WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$bidang = $stmt->get_result()->fetch_assoc();
if (!$bidang) redirect(SITE_URL . '/admin/bidang.php');

checkOwnership($bidang['kelurahan_id']);

$pageTitle = 'Edit Bidang - ' . $bidang['nama'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $no_urut   = (int)($_POST['no_urut'] ?? 0);
    $ikon      = trim($_POST['ikon'] ?? 'fas fa-users');

    if (empty($nama)) {
        $error = 'Nama Bidang/POKJA wajib diisi!';
    } else {
        $stmt = $conn->prepare("UPDATE bidang SET nama=?, deskripsi=?, ikon=?, no_urut=? WHERE id=?");
        $stmt->bind_param('sssii', $nama, $deskripsi, $ikon, $no_urut, $id);
        
        if ($stmt->execute()) {
            setFlash('success', 'Data bidang berhasil diperbarui!');
            redirect(SITE_URL . '/admin/bidang.php');
        } else {
            $error = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}

// Ambil daftar anggota
$members = $conn->query("SELECT * FROM anggota_bidang WHERE bidang_id = $id ORDER BY no_urut ASC, id ASC");

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Struktur</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Edit Bidang/POKJA</h2>
        <p class="text-gray-400 text-sm mt-1">Mengelola detail bidang dan daftar anggota di dalamnya</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="bidang.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm">
            <i class="fas fa-arrow-left text-xs text-accent"></i> Kembali
        </a>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6 text-sm">
    <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
    <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Form Edit Bidang -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden sticky top-24">
            <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
                <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white">
                    <i class="fas fa-edit text-xs"></i>
                </div>
                <h3 class="font-bold text-darkblue_alt text-sm">Data Utama Bidang</h3>
            </div>
            <form method="POST" class="p-6 space-y-5">
                <div>
                    <label class="block text-xs font-bold text-darkblue_alt mb-2">Nama Bidang <span class="text-red-400">*</span></label>
                    <input type="text" name="nama" required
                           value="<?= e($_POST['nama'] ?? $bidang['nama']) ?>"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-darkblue_alt mb-2">Ikon FontAwesome</label>
                    <div class="relative">
                        <input type="text" name="ikon" id="ikonInput"
                               value="<?= e($_POST['ikon'] ?? $bidang['ikon']) ?>"
                               class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-accent">
                            <i id="ikonPreview" class="<?= e($_POST['ikon'] ?? $bidang['ikon']) ?>"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-darkblue_alt mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all resize-none"><?= e($_POST['deskripsi'] ?? $bidang['deskripsi']) ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold text-darkblue_alt mb-2">Nomor Urut</label>
                    <input type="number" name="no_urut"
                           value="<?= e($_POST['no_urut'] ?? $bidang['no_urut']) ?>"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                </div>
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                        <i class="fas fa-save text-xs"></i> Simpan Bidang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Anggota -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white shadow-sm">
                        <i class="fas fa-users text-xs"></i>
                    </div>
                    <h3 class="font-bold text-darkblue_alt text-sm">Daftar Anggota</h3>
                </div>
                <a href="anggota-add.php?bidang_id=<?= $id ?>"
                   class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-3 py-1.5 rounded-lg transition-all text-[11px] shadow-sm">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 border-b border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="px-6 py-4">Anggota</th>
                            <th class="px-6 py-4">Jabatan</th>
                            <th class="px-6 py-4 text-center">Urutan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if ($members->num_rows > 0): ?>
                            <?php while($m = $members->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="<?= getImg($m['foto'], 'anggota') ?>" 
                                                 alt="" 
                                                 class="w-10 h-10 rounded-lg object-cover border-2 border-gray-100 group-hover:border-accent transition-all">
                                            <div class="font-bold text-darkblue_alt text-sm"><?= e($m['nama']) ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-accent/5 text-accent text-[10px] font-bold rounded-md border border-accent/10">
                                            <?= e($m['jabatan']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs font-bold text-gray-500">
                                        <?= $m['no_urut'] ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="anggota-edit.php?id=<?= $m['id'] ?>" 
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 hover:bg-accent hover:text-white transition-all shadow-sm"
                                               title="Edit">
                                                <i class="fas fa-edit text-[10px]"></i>
                                            </a>
                                            <a href="anggota-delete.php?id=<?= $m['id'] ?>" 
                                               onclick="return confirm('Hapus anggota ini?')"
                                               class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-100 text-gray-400 hover:bg-red-500 hover:text-white transition-all shadow-sm"
                                               title="Hapus">
                                                <i class="fas fa-trash text-[10px]"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 opacity-30">
                                        <i class="fas fa-users-slash text-4xl mb-2 text-darkblue"></i>
                                        <p class="text-sm font-bold text-darkblue">Belum ada anggota</p>
                                        <p class="text-xs">Klik tombol tambah untuk mengisi daftar anggota.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="h-16 lg:h-0"></div>

<script>
document.getElementById('ikonInput').addEventListener('input', function(e) {
    document.getElementById('ikonPreview').className = e.target.value || 'fas fa-users';
});
</script>

<?php include 'footer.php'; ?>
