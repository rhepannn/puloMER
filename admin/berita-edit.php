<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL.'/admin/berita.php');
$stmt = $conn->prepare("SELECT * FROM berita WHERE id = ?");
$stmt->bind_param('i', $id); $stmt->execute();
$berita = $stmt->get_result()->fetch_assoc();
if (!$berita) redirect(SITE_URL.'/admin/berita.php');
checkOwnership($berita['kelurahan_id']);
$pageTitle = 'Edit Berita';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $tgl_post = $_POST['tgl_post'] ?? date('Y-m-d');
    $url_src  = trim($_POST['url_sumber'] ?? '');
    $isi = ''; $gambar = $berita['gambar'];
    if (empty($judul)) { $error = 'Judul wajib diisi!'; }
    else {
        if (!empty($_FILES['gambar']['tmp_name'])) {
            $up = uploadFile($_FILES['gambar'], '../uploads/berita');
            if (is_array($up) && isset($up['error'])) { $error = $up['error']; }
            else { if ($gambar && file_exists('../uploads/berita/'.$gambar)) unlink('../uploads/berita/'.$gambar); $gambar = $up; }
        }
        if (empty($error)) {
            $kelId = isSuperAdmin() ? (int)($_POST['kelurahan_id'] ?? 0) : (int)$berita['kelurahan_id'];
            $stmt = $conn->prepare("UPDATE berita SET judul=?,isi=?,kategori=?,gambar=?,tgl_post=?,url_sumber=?,kelurahan_id=? WHERE id=?");
            $stmt->bind_param('ssssssii', $judul, $isi, $kategori, $gambar, $tgl_post, $url_src, $kelId, $id);
            if ($stmt->execute()) { setFlash('success','Berita berhasil diperbarui!'); redirect(SITE_URL.'/admin/berita.php'); }
            else { $error = 'Gagal: '.$conn->error; }
        }
    }
}
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");
include 'header.php';
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Berita</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Edit Berita</h2>
        <p class="text-gray-400 text-sm mt-1">Perbarui data berita yang sudah ada</p>
    </div>
    <a href="berita.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start">
        <i class="fas fa-arrow-left text-xs text-accent"></i> Kembali
    </a>
</div>
<?php if (!empty($error)): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6 text-sm">
    <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i> <span><?= e($error) ?></span>
</div>
<?php endif; ?>
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex items-center gap-3 px-6 py-4 border-b border-gray-50">
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white"><i class="fas fa-edit text-xs"></i></div>
        <h3 class="font-bold text-darkblue_alt text-sm">Edit Data Berita</h3>
    </div>
    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
        <?php if (isSuperAdmin()): ?>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Tujuan Kelurahan</label>
            <select name="kelurahan_id" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <option value="0">Kecamatan (Pusat)</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['kelurahan_id'] ?? $berita['kelurahan_id']) == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Judul Berita <span class="text-red-400">*</span></label>
                <input type="text" name="judul" required value="<?= e($_POST['judul'] ?? $berita['judul']) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Kategori</label>
                <input type="text" name="kategori" list="katList" value="<?= e($_POST['kategori'] ?? $berita['kategori']) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <datalist id="katList"><option value="Pemerintahan"><option value="Sosial Kemasyarakatan"><option value="Kesehatan"><option value="Pendidikan"><option value="Infrastruktur"><option value="Ekonomi"><option value="Budaya"><option value="Pengumuman"></datalist>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Tanggal Posting</label>
                <input type="date" name="tgl_post" value="<?= e($_POST['tgl_post'] ?? $berita['tgl_post']) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">URL Sumber (Opsional)</label>
                <input type="url" name="url_sumber" value="<?= e($_POST['url_sumber'] ?? $berita['url_sumber']) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Gambar Berita</label>
            <?php if (!empty($berita['gambar'])): ?>
            <img id="previewImg" src="<?= getImg($berita['gambar'],'berita') ?>" alt="" class="max-h-44 rounded-xl border-2 border-gray-200 object-cover mb-3">
            <?php else: ?>
            <img id="previewImg" src="" alt="" class="hidden max-h-44 rounded-xl border-2 border-accent object-cover mb-3">
            <?php endif; ?>
            <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-xl p-5 cursor-pointer hover:border-accent transition-colors bg-softgray group">
                <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 group-hover:text-accent mb-1"></i>
                <span class="text-xs text-gray-400">Klik untuk ganti gambar</span>
                <span class="text-[10px] text-gray-300 mt-0.5">JPG, PNG, WEBP · Maks 5MB</span>
                <input type="file" name="gambar" accept="image/*" class="hidden" onchange="swapImg(this)">
            </label>
            <p class="text-[11px] text-gray-400 mt-1.5">Biarkan kosong jika tidak mengganti gambar.</p>
        </div>
        <div class="flex items-center gap-3 pt-2 border-t border-gray-50">
            <button type="submit" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-save"></i> Perbarui Berita
            </button>
            <a href="berita.php" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
<div class="h-16 lg:h-0"></div>
<script>
function swapImg(input) {
    const img = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { img.src = e.target.result; img.classList.remove('hidden'); };
        r.readAsDataURL(input.files[0]);
    }
}
</script>
<?php include 'footer.php'; ?>
