<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL.'/admin/laporan.php');
$stmt = $conn->prepare("SELECT * FROM laporan WHERE id=?"); $stmt->bind_param('i',$id); $stmt->execute();
$l = $stmt->get_result()->fetch_assoc();
if (!$l) redirect(SITE_URL.'/admin/laporan.php');
$pageTitle = 'Edit Laporan';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? ''); $deskripsi = trim($_POST['deskripsi'] ?? ''); $tgl_upload = $_POST['tgl_upload'] ?? date('Y-m-d');
    $file = $l['file'];
    if (empty($judul)) { $error = 'Judul wajib diisi!'; }
    else {
        if (!empty($_FILES['file']['tmp_name'])) {
            $up = uploadDoc($_FILES['file'], '../uploads/laporan');
            if (!$up) { $error = 'Gagal upload file!'; }
            else { if ($file && file_exists('../uploads/laporan/'.$file)) unlink('../uploads/laporan/'.$file); $file = $up; }
        }
        if (empty($error)) {
            $stmt = $conn->prepare("UPDATE laporan SET judul=?,deskripsi=?,file=?,tgl_upload=? WHERE id=?");
            $stmt->bind_param('ssssi',$judul,$deskripsi,$file,$tgl_upload,$id);
            if ($stmt->execute()) { setFlash('success','Laporan berhasil diperbarui!'); redirect(SITE_URL.'/admin/laporan.php'); }
            else { $error = 'Gagal: '.$conn->error; }
        }
    }
}
include 'header.php';
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen Laporan</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Edit Laporan</h2>
        <p class="text-gray-400 text-sm mt-1">Perbarui data laporan yang sudah ada</p>
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
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white"><i class="fas fa-edit text-xs"></i></div>
        <h3 class="font-bold text-darkblue_alt text-sm">Edit Data Laporan</h3>
    </div>
    <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Judul Laporan <span class="text-red-400">*</span></label>
                <input type="text" name="judul" required value="<?= e($_POST['judul'] ?? $l['judul']) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Tanggal</label>
                <input type="date" name="tgl_upload" value="<?= e($_POST['tgl_upload'] ?? $l['tgl_upload']) ?>"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
        </div>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="4" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all resize-y"><?= e($_POST['deskripsi'] ?? $l['deskripsi']) ?></textarea>
        </div>
        <div>
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Ganti File (Opsional)</label>
            <?php if (!empty($l['file'])): ?>
            <div class="flex items-center gap-3 bg-softgray border border-gray-200 rounded-xl px-4 py-3 mb-3">
                <i class="fas fa-file-pdf text-red-400 text-xl flex-shrink-0"></i>
                <div class="min-w-0">
                    <div class="font-bold text-xs text-darkblue_alt truncate"><?= e($l['file']) ?></div>
                    <a href="<?= SITE_URL ?>/uploads/laporan/<?= e($l['file']) ?>" target="_blank"
                       class="text-[11px] text-accent hover:underline">Lihat File Saat Ini</a>
                </div>
            </div>
            <?php endif; ?>
            <label class="flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-200 rounded-xl p-5 cursor-pointer hover:border-accent transition-colors bg-softgray group">
                <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 group-hover:text-accent mb-1"></i>
                <span class="text-xs text-gray-400">Klik untuk ganti file</span>
                <span class="text-[10px] text-gray-300 mt-0.5">PDF, DOC, DOCX, XLS, XLSX · Maks 5MB</span>
                <input type="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="hidden"
                       onchange="document.getElementById('fname').textContent = this.files[0]?.name || ''">
            </label>
            <p id="fname" class="text-xs text-accent font-bold mt-2"></p>
            <p class="text-[11px] text-gray-400 mt-1">Biarkan kosong jika tidak mengganti file.</p>
        </div>
        <div class="flex items-center gap-3 pt-2 border-t border-gray-50">
            <button type="submit" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-save"></i> Perbarui Laporan
            </button>
            <a href="laporan.php" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
<div class="h-16 lg:h-0"></div>
<?php include 'footer.php'; ?>
