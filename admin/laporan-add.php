<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$pageTitle = 'Upload Laporan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul      = trim($_POST['judul'] ?? '');
    $deskripsi  = trim($_POST['deskripsi'] ?? '');
    $tgl_upload = $_POST['tgl_upload'] ?? date('Y-m-d');
    $kelId      = isSuperAdmin() ? (int)($_POST['kelurahan_id'] ?? 0) : (int)getKelurahanId();
    if (empty($judul)) { $error = 'Judul laporan wajib diisi!'; }
    else {
        $file = '';
        if (!empty($_FILES['file']['tmp_name'])) {
            $up = uploadDoc($_FILES['file'], '../uploads/laporan');
            if (!$up) { $error = 'Gagal upload file! Format PDF/DOC/XLS, maks 5MB.'; }
            else { $file = $up; }
        }
        if (empty($error)) {
            $stmt = $conn->prepare("INSERT INTO laporan (judul, deskripsi, file, tgl_upload, kelurahan_id) VALUES (?,?,?,?,?)");
            $stmt->bind_param('ssssi', $judul, $deskripsi, $file, $tgl_upload, $kelId);
            if ($stmt->execute()) { setFlash('success','Laporan berhasil diupload!'); redirect(SITE_URL.'/admin/laporan.php'); }
            else { $error = 'Gagal menyimpan: '.$conn->error; }
        }
    }
}
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");
include 'header.php';
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Laporan</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Upload Laporan</h2>
        <p class="text-gray-400 text-sm mt-1">Upload dokumen laporan baru</p>
    </div>
    <a href="laporan.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start">
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
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white"><i class="fas fa-file-upload text-xs"></i></div>
        <h3 class="font-bold text-darkblue_alt text-sm">Form Upload Laporan</h3>
    </div>
    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
        <?php if (isSuperAdmin()): ?>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Tujuan Kelurahan <span class="text-red-400">*</span></label>
            <select name="kelurahan_id" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <option value="0">Kecamatan (Pusat)</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['kelurahan_id'] ?? '') == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-[11px] text-gray-400 mt-1.5">Laporan ini akan dikelompokkan berdasarkan Kelurahan yang dipilih.</p>
        </div>
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Judul Laporan <span class="text-red-400">*</span></label>
                <input type="text" name="judul" required value="<?= e($_POST['judul'] ?? '') ?>" placeholder="Contoh: Laporan Bulanan April 2025"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Tanggal</label>
                <input type="date" name="tgl_upload" value="<?= e($_POST['tgl_upload'] ?? date('Y-m-d')) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Deskripsi Laporan</label>
            <textarea name="deskripsi" rows="4" placeholder="Keterangan singkat tentang laporan ini..."
                      class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all resize-y"><?= e($_POST['deskripsi'] ?? '') ?></textarea>
        </div>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">File Laporan (PDF/DOC/XLS)</label>
            <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-xl p-6 cursor-pointer hover:border-accent transition-colors bg-softgray group">
                <i class="fas fa-file-pdf text-2xl text-gray-300 group-hover:text-accent transition-colors mb-2"></i>
                <span class="text-xs text-gray-400 group-hover:text-accent">Klik untuk pilih file</span>
                <span class="text-[10px] text-gray-300 mt-1">PDF, DOC, DOCX, XLS, XLSX · Maks 5MB</span>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                       onchange="document.getElementById('fname').textContent = this.files[0]?.name || ''">
            </label>
            <p id="fname" class="text-xs text-accent font-bold mt-2"></p>
        </div>
        <div class="flex items-center gap-3 pt-2 border-t border-gray-50">
            <button type="submit" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-upload"></i> Upload Laporan
            </button>
            <a href="laporan.php" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
<div class="h-16 lg:h-0"></div>
<?php include 'footer.php'; ?>
