<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$pageTitle = 'Tambah Berita';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul    = trim($_POST['judul'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $tgl_post = $_POST['tgl_post'] ?? date('Y-m-d');
    $url_src  = trim($_POST['url_sumber'] ?? '');
    $kelId    = isSuperAdmin() ? (int)($_POST['kelurahan_id'] ?? 0) : (int)getKelurahanId();

    if (empty($judul)) {
        $error = 'Judul berita wajib diisi!';
    } else {
        $isi = '';
        $gambar = '';
        if (!empty($_FILES['gambar']['tmp_name'])) {
            $up = uploadFile($_FILES['gambar'], '../uploads/berita');
            if (is_array($up) && isset($up['error'])) { $error = $up['error']; }
            else { $gambar = $up; }
        }
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO berita (judul, isi, kategori, gambar, tgl_post, url_sumber, kelurahan_id) VALUES (?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssi', $judul, $isi, $kategori, $gambar, $tgl_post, $url_src, $kelId);
            if ($stmt->execute()) { setFlash('success','Berita berhasil ditambahkan!'); redirect(SITE_URL.'/admin/berita.php'); }
            else { $error = 'Gagal menyimpan: '.$conn->error; }
        }
    }
}
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");
include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Berita</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Tambah Berita</h2>
        <p class="text-gray-400 text-sm mt-1">Isi form di bawah untuk menambahkan berita baru</p>
    </div>
    <a href="berita.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
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
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white">
            <i class="fas fa-newspaper text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Form Tambah Berita</h3>
    </div>
    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">

        <?php if (isSuperAdmin()): ?>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Tujuan Kelurahan</label>
            <select name="kelurahan_id" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <option value="0">Kecamatan (Pusat)</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['kelurahan_id'] ?? '') == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-[11px] text-gray-400 mt-1.5">Pilih "Kecamatan" jika berita untuk umum.</p>
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Judul Berita <span class="text-red-400">*</span></label>
                <input type="text" name="judul" required value="<?= e($_POST['judul'] ?? '') ?>" placeholder="Masukkan judul berita"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Kategori</label>
                <input type="text" name="kategori" list="katList" value="<?= e($_POST['kategori'] ?? '') ?>" placeholder="Contoh: Pemerintahan, Sosial..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <datalist id="katList">
                    <option value="Pemerintahan"><option value="Sosial Kemasyarakatan"><option value="Kesehatan">
                    <option value="Pendidikan"><option value="Infrastruktur"><option value="Ekonomi">
                    <option value="Budaya"><option value="Pengumuman">
                </datalist>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Tanggal Posting</label>
                <input type="date" name="tgl_post" value="<?= e($_POST['tgl_post'] ?? date('Y-m-d')) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">URL Sumber (Opsional)</label>
                <input type="url" name="url_sumber" value="<?= e($_POST['url_sumber'] ?? '') ?>" placeholder="https://..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <p class="text-[11px] text-gray-400 mt-1.5">Link ke sumber berita asli (jika ada)</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Gambar Berita</label>
            <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-xl p-6 cursor-pointer hover:border-accent transition-colors bg-softgray group">
                <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 group-hover:text-accent transition-colors mb-2"></i>
                <span class="text-xs text-gray-400 group-hover:text-accent">Klik untuk pilih gambar</span>
                <span class="text-[10px] text-gray-300 mt-1">JPG, PNG, WEBP · Maks 5MB</span>
                <input type="file" name="gambar" accept="image/*" class="hidden" onchange="previewImg(this)">
            </label>
            <div id="previewWrap" class="hidden mt-3">
                <img id="previewImg" src="" alt="Preview" class="max-h-48 rounded-xl border-2 border-accent object-cover">
            </div>
        </div>

        <div class="flex items-center gap-3 pt-2 border-t border-gray-50">
            <button type="submit" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-save"></i> Simpan Berita
            </button>
            <a href="berita.php" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
<div class="h-16 lg:h-0"></div>
<script>
function previewImg(input) {
    const wrap = document.getElementById('previewWrap');
    const img  = document.getElementById('previewImg');
    if (input.files && input.files[0]) {
        const r = new FileReader();
        r.onload = e => { img.src = e.target.result; wrap.classList.remove('hidden'); };
        r.readAsDataURL(input.files[0]);
    }
}
</script>
<?php include 'footer.php'; ?>
