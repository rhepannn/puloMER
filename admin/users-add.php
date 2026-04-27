<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
if (!isSuperAdmin()) { setFlash('error','Halaman ini hanya untuk Superadmin.'); redirect(SITE_URL.'/admin/index.php'); }
$pageTitle = 'Tambah User';
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $nama         = trim($_POST['nama'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role_type    = $_POST['role_type'] ?? 'kecamatan';
    $kelurahan_id = ($role_type === 'kelurahan') ? (int)$_POST['kelurahan_id'] : null;
    if (empty($username) || empty($nama) || empty($password)) { $error = 'Semua field wajib diisi!'; }
    else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?"); $check->bind_param('s',$username); $check->execute();
        if ($check->get_result()->num_rows > 0) { $error = 'Username sudah digunakan!'; }
        else {
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $role_text = ($role_type === 'kecamatan') ? 'admin' : 'kelurahan_admin';
            $stmt = $conn->prepare("INSERT INTO users (username, password, nama, role, kelurahan_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('ssssi', $username, $pass_hash, $nama, $role_text, $kelurahan_id);
            if ($stmt->execute()) { setFlash('success','User baru berhasil ditambahkan!'); redirect(SITE_URL.'/admin/users.php'); }
            else { $error = 'Gagal menyimpan: '.$conn->error; }
        }
    }
}
include 'header.php';
?>
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen User</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Tambah User Admin</h2>
        <p class="text-gray-400 text-sm mt-1">Buat akun admin baru untuk kelurahan atau kecamatan</p>
    </div>
    <a href="users.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start">
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
        <div class="w-8 h-8 rounded-lg bg-accent flex items-center justify-center text-white"><i class="fas fa-user-plus text-xs"></i></div>
        <h3 class="font-bold text-darkblue_alt text-sm">Form Tambah User</h3>
    </div>
    <form method="POST" class="p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Username <span class="text-red-400">*</span></label>
                <input type="text" name="username" required value="<?= e($_POST['username'] ?? '') ?>" placeholder="Contoh: admintamansari"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required value="<?= e($_POST['nama'] ?? '') ?>" placeholder="Masukkan nama lengkap"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Password <span class="text-red-400">*</span></label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Tipe Role <span class="text-red-400">*</span></label>
                <select name="role_type" id="roleType" onchange="toggleKel()" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                    <option value="kecamatan" <?= (($_POST['role_type'] ?? '') === 'kecamatan') ? 'selected' : '' ?>>Admin Kecamatan (Superadmin)</option>
                    <option value="kelurahan" <?= (($_POST['role_type'] ?? '') === 'kelurahan') ? 'selected' : '' ?>>Admin Kelurahan</option>
                </select>
            </div>
        </div>
        <div id="kelGroup" class="<?= (($_POST['role_type'] ?? '') === 'kelurahan') ? '' : 'hidden' ?>">
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Tugaskan ke Kelurahan <span class="text-red-400">*</span></label>
            <select name="kelurahan_id" class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <option value="">-- Pilih Kelurahan --</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= (($_POST['kelurahan_id'] ?? 0) == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-[11px] text-gray-400 mt-1.5">User ini hanya bisa mengelola data kelurahan yang dipilih.</p>
        </div>
        <div class="flex items-center gap-3 pt-2 border-t border-gray-50">
            <button type="submit" class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-save"></i> Buat User Baru
            </button>
            <a href="users.php" class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times"></i> Batal
            </a>
        </div>
    </form>
</div>
<div class="h-16 lg:h-0"></div>
<script>
function toggleKel() {
    const v = document.getElementById('roleType').value;
    document.getElementById('kelGroup').classList.toggle('hidden', v !== 'kelurahan');
}
</script>
<?php include 'footer.php'; ?>
