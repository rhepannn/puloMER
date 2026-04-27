<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

// Hanya superadmin yang boleh nambah Bidang/Pokja baru secara umum
if (!isSuperAdmin()) {
    setFlash('error', 'Halaman ini hanya untuk Superadmin.');
    redirect(SITE_URL . '/admin/bidang.php');
}

$pageTitle = 'Tambah Bidang / POKJA';
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $no_urut   = (int)($_POST['no_urut'] ?? 0);
    $kelId     = (int)($_POST['kelurahan_id'] ?? 0);
    $ikon      = trim($_POST['ikon'] ?? 'fas fa-users');

    if (empty($nama)) {
        $error = 'Nama Bidang/POKJA wajib diisi!';
    } else {
        $stmt = $conn->prepare("INSERT INTO bidang (nama, deskripsi, ikon, no_urut, kelurahan_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssii', $nama, $deskripsi, $ikon, $no_urut, $kelId);
        
        if ($stmt->execute()) {
            setFlash('success', 'Bidang/POKJA berhasil ditambahkan!');
            redirect(SITE_URL . '/admin/bidang.php');
        } else {
            $error = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Struktur</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Tambah Bidang/POKJA</h2>
        <p class="text-gray-400 text-sm mt-1">Membuat grup struktur organisasi baru (Pokja/Sekretariat)</p>
    </div>
    <a href="bidang.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
        <i class="fas fa-arrow-left text-xs text-accent"></i> Kembali
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6 text-sm">
    <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
    <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white">
            <i class="fas fa-sitemap text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Form Bidang Baru</h3>
    </div>
    <form method="POST" class="p-6 space-y-5">
        
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Ditujukan Untuk <span class="text-red-400">*</span></label>
            <select name="kelurahan_id" required
                    class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <option value="0">Kecamatan (Pusat)</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                    <option value="<?= $k['id'] ?>" <?= (($_POST['kelurahan_id'] ?? '') == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Pilih apakah bidang ini milik Kecamatan atau salah satu Kelurahan.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Nama Bidang / Pokja <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required
                       value="<?= e($_POST['nama'] ?? '') ?>"
                       placeholder="Contoh: POKJA I, Sekretariat..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Ikon FontAwesome</label>
                <div class="relative">
                    <input type="text" name="ikon"
                           value="<?= e($_POST['ikon'] ?? 'fas fa-users') ?>"
                           placeholder="Contoh: fas fa-book"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 text-accent">
                        <i id="ikonPreview" class="<?= e($_POST['ikon'] ?? 'fas fa-users') ?>"></i>
                    </div>
                </div>
                <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Gunakan class CSS FontAwesome v5.15.</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="3"
                      placeholder="Keterangan mengenai tugas atau fungsi bidang ini..."
                      class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all resize-none"><?= e($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Nomor Urut</label>
            <input type="number" name="no_urut"
                   value="<?= e($_POST['no_urut'] ?? '0') ?>"
                   class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Menentukan urutan bidang di halaman depan.</p>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
            <button type="submit"
                    class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-plus text-xs"></i> Buat Bidang
            </button>
            <a href="bidang.php"
               class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times text-xs"></i> Batal
            </a>
        </div>
    </form>
</div>

<div class="h-16 lg:h-0"></div>

<script>
document.querySelector('input[name="ikon"]').addEventListener('input', function(e) {
    document.getElementById('ikonPreview').className = e.target.value || 'fas fa-users';
});
</script>

<?php include 'footer.php'; ?>
