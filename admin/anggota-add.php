<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$bidang_id = (int)($_GET['bidang_id'] ?? 0);
if (!$bidang_id) redirect(SITE_URL . '/admin/bidang.php');

// Cek apakah bidang ada dan cek ownership
$stmt = $conn->prepare("SELECT * FROM bidang WHERE id = ?");
$stmt->bind_param('i', $bidang_id);
$stmt->execute();
$bidang = $stmt->get_result()->fetch_assoc();
if (!$bidang) redirect(SITE_URL . '/admin/bidang.php');

// Hanya admin kelurahan ybs atau superadmin yang boleh nambah anggota
checkOwnership($bidang['kelurahan_id']);

$pageTitle = 'Tambah Anggota - ' . $bidang['nama'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $jabatan   = trim($_POST['jabatan'] ?? '');
    $no_urut   = (int)($_POST['no_urut'] ?? 0);
    $foto      = '';

    if (empty($nama) || empty($jabatan)) {
        $error = 'Nama dan Jabatan wajib diisi!';
    } else {
        if (!empty($_FILES['foto']['tmp_name'])) {
            $up = uploadFile($_FILES['foto'], '../uploads/anggota');
            if (is_array($up) && isset($up['error'])) {
                $error = $up['error'];
            } else {
                $foto = $up;
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO anggota_bidang (bidang_id, nama, jabatan, foto, no_urut) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('isssi', $bidang_id, $nama, $jabatan, $foto, $no_urut);
            if ($stmt->execute()) {
                setFlash('success', 'Anggota berhasil ditambahkan!');
                redirect(SITE_URL . '/admin/bidang-edit.php?id=' . $bidang_id);
            } else {
                $error = 'Gagal menyimpan: ' . $conn->error;
            }
        }
    }
}

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Struktur</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Tambah Anggota</h2>
        <p class="text-gray-400 text-sm mt-1">Menambahkan personil ke dalam <span class="font-bold text-darkblue_alt"><?= e($bidang['nama']) ?></span></p>
    </div>
    <a href="bidang-edit.php?id=<?= $bidang_id ?>" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
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
            <i class="fas fa-user-plus text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Profil Anggota Baru</h3>
    </div>
    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required
                       value="<?= e($_POST['nama'] ?? '') ?>"
                       placeholder="Masukkan nama lengkap anggota..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Jabatan <span class="text-red-400">*</span></label>
                <input type="text" name="jabatan" required
                       value="<?= e($_POST['jabatan'] ?? '') ?>"
                       placeholder="Contoh: Ketua, Sekretaris, Anggota..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Nomor Urut Tampilan</label>
            <input type="number" name="no_urut"
                   value="<?= e($_POST['no_urut'] ?? '0') ?>"
                   class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Urutan anggota saat ditampilkan di halaman publik (0 = paling depan).</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Foto Profil</label>
            <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-xl p-6 cursor-pointer hover:border-accent transition-colors bg-softgray group">
                <i class="fas fa-camera text-2xl text-gray-300 group-hover:text-accent transition-colors mb-2"></i>
                <span class="text-xs text-gray-400 group-hover:text-accent">Klik untuk unggah foto</span>
                <span class="text-[10px] text-gray-300 mt-1 uppercase tracking-wider">JPG, PNG, WEBP · Maks 5MB</span>
                <input type="file" name="foto" accept="image/*" class="hidden" onchange="previewFoto(this)">
            </label>
            <div id="previewWrap" class="hidden mt-4">
                <img id="imgPreview" src="" alt="Preview" class="w-32 h-32 rounded-2xl border-4 border-accent object-cover shadow-md">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
            <button type="submit"
                    class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-check text-xs"></i> Simpan Anggota
            </button>
            <a href="bidang-edit.php?id=<?= $bidang_id ?>"
               class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times text-xs"></i> Batal
            </a>
        </div>
    </form>
</div>

<div class="h-16 lg:h-0"></div>

<script>
function previewFoto(input) {
    const wrap = document.getElementById('previewWrap');
    const img = document.getElementById('imgPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            img.src = e.target.result;
            wrap.classList.remove('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>
