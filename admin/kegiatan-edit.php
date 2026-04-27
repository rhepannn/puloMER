<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL . '/admin/kegiatan.php');

$stmt = $conn->prepare("SELECT * FROM kegiatan WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$k = $stmt->get_result()->fetch_assoc();
if (!$k) redirect(SITE_URL . '/admin/kegiatan.php');

$pageTitle = 'Edit Kegiatan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul        = trim($_POST['judul'] ?? '');
    $tgl_kegiatan = $_POST['tgl_kegiatan'] ?? '';
    $lokasi       = trim($_POST['lokasi'] ?? '');
    $deskripsi    = trim($_POST['konten'] ?? '');
    $kategori     = trim($_POST['kategori'] ?? '');
    $kelId        = isSuperAdmin() ? (int)($_POST['kelurahan_id'] ?? 0) : (int)getKelurahanId();

    if (empty($judul) || empty($tgl_kegiatan) || empty($deskripsi)) {
        $error = 'Semua field wajib diisi!';
    } else {
        $gambar = $k['gambar'];
        if (!empty($_FILES['gambar']['tmp_name'])) {
            $res = uploadFile($_FILES['gambar'], '../uploads/kegiatan');
            if (is_array($res) && isset($res['error'])) {
                $error = $res['error'];
            } else {
                if ($gambar && file_exists('../uploads/kegiatan/' . $gambar)) {
                    @unlink('../uploads/kegiatan/' . $gambar);
                }
                $gambar = $res;
            }
        }

        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE kegiatan SET judul = ?, tgl_kegiatan = ?, lokasi = ?, deskripsi = ?, gambar = ?, kategori = ?, kelurahan_id = ? WHERE id = ?");
            $stmt->bind_param('ssssssii', $judul, $tgl_kegiatan, $lokasi, $deskripsi, $gambar, $kategori, $kelId, $id);
            if ($stmt->execute()) {
                setFlash('success', 'Kegiatan berhasil diperbarui!');
                redirect(SITE_URL . '/admin/kegiatan.php');
            } else {
                $error = 'Gagal menyimpan ke database.';
            }
        }
    }
}

$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");
include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Konten</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Edit Kegiatan</h2>
        <p class="text-gray-400 text-sm mt-1">Memperbarui data: <span class="font-bold text-darkblue_alt"><?= e($k['judul']) ?></span></p>
    </div>
    <a href="kegiatan.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
        <i class="fas fa-arrow-left text-xs text-accent"></i> Kembali
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6 text-sm font-bold">
    <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
    <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
            <div class="space-y-6">
                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Judul Kegiatan <span class="text-red-400">*</span></label>
                    <input type="text" name="judul" required value="<?= e($_POST['judul'] ?? $k['judul']) ?>" placeholder="Masukkan judul kegiatan..."
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all font-bold">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Konten / Deskripsi <span class="text-red-400">*</span></label>
                    <textarea name="konten" id="editor" rows="15" 
                              class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all resize-none"><?= e($_POST['konten'] ?? $k['deskripsi']) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-6">
        <!-- Settings -->
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-darkblue_alt px-6 py-4">
                <h3 class="text-white font-bold text-sm">Pengaturan</h3>
            </div>
            <div class="p-6 space-y-6">
                <?php if (isSuperAdmin()): ?>
                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Tujuan Kelurahan</label>
                    <select name="kelurahan_id" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all font-bold">
                        <option value="0">Kecamatan (Pusat)</option>
                        <?php while ($kel = $kels->fetch_assoc()): ?>
                        <option value="<?= $kel['id'] ?>" <?= (($_POST['kelurahan_id'] ?? $k['kelurahan_id']) == $kel['id']) ? 'selected' : '' ?>><?= e($kel['nama']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <?php endif; ?>

                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Kategori</label>
                    <input type="text" name="kategori" list="katList" value="<?= e($_POST['kategori'] ?? $k['kategori']) ?>" placeholder="Contoh: PKK, Posyandu..."
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all">
                    <datalist id="katList">
                        <option value="PKK">
                        <option value="Posyandu">
                        <option value="Kesehatan">
                        <option value="Pendidikan">
                        <option value="Sosial">
                    </datalist>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Tanggal Kegiatan <span class="text-red-400">*</span></label>
                    <input type="date" name="tgl_kegiatan" required value="<?= e($_POST['tgl_kegiatan'] ?? $k['tgl_kegiatan']) ?>"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Lokasi</label>
                    <input type="text" name="lokasi" value="<?= e($_POST['lokasi'] ?? $k['lokasi']) ?>" placeholder="Contoh: Aula Kecamatan"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Gambar Utama</label>
                    <div class="group relative bg-softgray border-2 border-dashed border-gray-200 rounded-2xl p-4 transition-all hover:border-accent">
                        <input type="file" name="gambar" accept="image/*" onchange="previewImage(this)"
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        <div id="preview-placeholder" class="<?= $k['gambar'] ? 'hidden' : '' ?> text-center py-4">
                            <i class="fas fa-cloud-upload-alt text-gray-300 text-3xl mb-2 group-hover:text-accent transition-colors"></i>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest group-hover:text-accent transition-colors">Klik untuk ganti</p>
                        </div>
                        <img id="image-preview" src="<?= getImg($k['gambar'], 'kegiatan') ?>" 
                             class="<?= $k['gambar'] ? '' : 'hidden' ?> w-full h-40 object-cover rounded-xl shadow-sm">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 font-medium"><i class="fas fa-info-circle mr-1"></i>Abaikan jika tidak ingin mengganti gambar.</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-accent hover:bg-darkblue text-white font-black px-8 py-4 rounded-2xl transition-all shadow-xl shadow-accent/20 uppercase tracking-widest text-sm flex items-center justify-center gap-3">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </div>
</form>

<div class="h-16 lg:h-0"></div>

<script>
function previewImage(input) {
    const placeholder = document.getElementById('preview-placeholder');
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>
