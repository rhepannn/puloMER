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
    $nama             = trim($_POST['nama'] ?? '');
    $deskripsi        = trim($_POST['deskripsi'] ?? '');
    $urutan           = (int)($_POST['urutan'] ?? 0);
    $ikon             = trim($_POST['ikon'] ?? 'fas fa-users');
    $prestasi         = trim($_POST['prestasi'] ?? '');
    $program_unggulan = trim($_POST['program_unggulan'] ?? '');
    // Buat slug otomatis dari nama
    $slug = createSlug($nama);

    if (empty($nama)) {
        $error = 'Nama Bidang/POKJA wajib diisi!';
    } else {
        // Pastikan slug unik
        $cek = $conn->prepare("SELECT id FROM bidang WHERE slug = ?");
        $cek->bind_param('s', $slug);
        $cek->execute();
        if ($cek->get_result()->num_rows > 0) {
            $slug = $slug . '-' . time();
        }

        $stmt = $conn->prepare("INSERT INTO bidang (nama, slug, deskripsi, prestasi, program_unggulan, urutan) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssi', $nama, $slug, $deskripsi, $prestasi, $program_unggulan, $urutan);
        
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Nama Bidang / Pokja <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required
                       value="<?= e($_POST['nama'] ?? '') ?>"
                       placeholder="Contoh: POKJA I, Sekretariat..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Nomor Urut Tampilan</label>
                <input type="number" name="urutan"
                       value="<?= e($_POST['urutan'] ?? '0') ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Menentukan urutan tampilan di halaman depan.</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="3"
                      placeholder="Keterangan mengenai tugas atau fungsi bidang ini..."
                      class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all resize-none"><?= e($_POST['deskripsi'] ?? '') ?></textarea>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Prestasi / Pencapaian</label>
            <input type="text" name="prestasi"
                   value="<?= e($_POST['prestasi'] ?? '') ?>"
                   placeholder="Contoh: Juara 1 Lomba PKK Tingkat Kota"
                   class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Program Unggulan <span class="text-gray-400 font-normal">(satu per baris)</span></label>
            <textarea name="program_unggulan" rows="5"
                      placeholder="1. Program A&#10;2. Program B"
                      class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all resize-none"><?= e($_POST['program_unggulan'] ?? '') ?></textarea>
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
