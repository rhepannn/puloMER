<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

if (!isSuperAdmin()) {
    setFlash('error', 'Halaman ini hanya dapat diakses oleh Superadmin.');
    redirect(SITE_URL . '/admin/index.php');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL . '/admin/users.php');

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$u = $stmt->get_result()->fetch_assoc();
if (!$u) redirect(SITE_URL . '/admin/users.php');

$pageTitle = 'Edit User';
$kels = $conn->query("SELECT id, nama FROM kelurahan ORDER BY nama");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama         = trim($_POST['nama'] ?? '');
    $password     = $_POST['password'] ?? '';
    $role_type    = $_POST['role_type'] ?? 'kecamatan';
    $kelurahan_id = ($role_type === 'kelurahan') ? (int)$_POST['kelurahan_id'] : null;

    if (empty($nama)) {
        $error = 'Nama lengkap wajib diisi!';
    } else {
        $role_text = ($role_type === 'kecamatan') ? 'admin' : 'kelurahan_admin';
        
        if (!empty($password)) {
            // Update dengan password baru
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, password=?, role=?, kelurahan_id=? WHERE id=?");
            $stmt->bind_param('ssssi', $nama, $pass_hash, $role_text, $kelurahan_id, $id);
        } else {
            // Update tanpa ganti password
            $stmt = $conn->prepare("UPDATE users SET nama=?, role=?, kelurahan_id=? WHERE id=?");
            $stmt->bind_param('sssi', $nama, $role_text, $kelurahan_id, $id);
        }
        
        if ($stmt->execute()) {
            setFlash('success', 'Data user berhasil diperbarui!');
            redirect(SITE_URL . '/admin/users.php');
        } else {
            $error = 'Gagal menyimpan: ' . $conn->error;
        }
    }
}

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Manajemen User</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Edit User Admin</h2>
        <p class="text-gray-400 text-sm mt-1">Perbarui data akun admin: <span class="font-bold text-darkblue_alt"><?= e($u['username']) ?></span></p>
    </div>
    <a href="users.php" class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
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
            <i class="fas fa-user-edit text-xs"></i>
        </div>
        <h3 class="font-bold text-darkblue_alt text-sm">Form Edit User</h3>
    </div>
    <form method="POST" class="p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Username</label>
                <input type="text" value="<?= e($u['username']) ?>" disabled
                       class="w-full bg-gray-100 border border-gray-200 text-gray-400 rounded-xl px-4 py-3 text-sm cursor-not-allowed shadow-inner">
                <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Username tidak dapat diubah.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Nama Lengkap <span class="text-red-400">*</span></label>
                <input type="text" name="nama" required
                       value="<?= e($_POST['nama'] ?? $u['nama']) ?>"
                       placeholder="Masukkan nama lengkap..."
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Ganti Password</label>
                <input type="password" name="password"
                       placeholder="Kosongkan jika tidak ingin ganti"
                       class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <p class="text-[10px] text-gray-400 mt-1.5 ml-1">Minimal 6 karakter jika ingin diganti.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-darkblue_alt mb-2">Tipe Role <span class="text-red-400">*</span></label>
                <?php 
                    $curr_role = ($_POST['role_type'] ?? ($u['kelurahan_id'] === null ? 'kecamatan' : 'kelurahan'));
                ?>
                <select name="role_type" id="roleType" onchange="toggleKelurahan()"
                        class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                    <option value="kecamatan" <?= $curr_role === 'kecamatan' ? 'selected' : '' ?>>Admin Kecamatan (Superadmin)</option>
                    <option value="kelurahan" <?= $curr_role === 'kelurahan' ? 'selected' : '' ?>>Admin Kelurahan</option>
                </select>
            </div>
        </div>

        <div id="kelGroup" class="<?= $curr_role === 'kelurahan' ? '' : 'hidden' ?>">
            <label class="block text-xs font-bold text-darkblue_alt mb-2">Tugaskan ke Kelurahan <span class="text-red-400">*</span></label>
            <select name="kelurahan_id"
                    class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent focus:ring-2 focus:ring-accent/10 transition-all">
                <option value="">-- Pilih Kelurahan --</option>
                <?php while ($k = $kels->fetch_assoc()): ?>
                    <option value="<?= $k['id'] ?>" <?= (($_POST['kelurahan_id'] ?? $u['kelurahan_id']) == $k['id']) ? 'selected' : '' ?>><?= e($k['nama']) ?></option>
                <?php endwhile; ?>
            </select>
            <p class="text-[10px] text-gray-400 mt-1.5 ml-1">User ini hanya bisa mengelola data untuk kelurahan yang dipilih.</p>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
            <button type="submit"
                    class="flex items-center gap-2 bg-accent hover:bg-darkblue text-white font-bold px-6 py-3 rounded-xl transition-all shadow-lg shadow-accent/20 text-sm">
                <i class="fas fa-save text-xs"></i> Simpan Perubahan
            </button>
            <a href="users.php"
               class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold px-6 py-3 rounded-xl transition-all text-sm">
                <i class="fas fa-times text-xs"></i> Batal
            </a>
        </div>
    </form>
</div>

<div class="h-16 lg:h-0"></div>

<script>
function toggleKelurahan() {
    const role = document.getElementById('roleType').value;
    const group = document.getElementById('kelGroup');
    if(role === 'kelurahan') {
        group.classList.remove('hidden');
    } else {
        group.classList.add('hidden');
    }
}
</script>

<?php include 'footer.php'; ?>
