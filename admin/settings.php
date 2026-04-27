<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$admin_id = $_SESSION['admin_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param('i', $admin_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$pageTitle = 'Pengaturan Akun';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $conf_pass = $_POST['confirm_password'] ?? '';

    if (empty($nama) || empty($username)) {
        $error = 'Nama dan Username wajib diisi!';
    } else {
        // Check username uniqueness (if changed)
        $check = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $check->bind_param('si', $username, $admin_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'Username sudah digunakan oleh orang lain.';
        } else {
            // Update profile
            $stmt = $conn->prepare("UPDATE users SET nama = ?, username = ? WHERE id = ?");
            $stmt->bind_param('ssi', $nama, $username, $admin_id);
            $stmt->execute();
            $_SESSION['admin_name'] = $nama;

            // Handle password change
            if (!empty($new_pass)) {
                if (!password_verify($old_pass, $user['password'])) {
                    $error = 'Password lama salah!';
                } elseif ($new_pass !== $conf_pass) {
                    $error = 'Konfirmasi password baru tidak cocok!';
                } elseif (strlen($new_pass) < 6) {
                    $error = 'Password baru minimal 6 karakter.';
                } else {
                    $hash = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param('si', $hash, $admin_id);
                    $stmt->execute();
                    $success = 'Profil dan password berhasil diperbarui!';
                }
            } else {
                $success = 'Profil berhasil diperbarui!';
            }
        }
    }
}

include 'header.php';
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Keamanan & Profil</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Pengaturan Akun</h2>
        <p class="text-gray-400 text-sm mt-1">Kelola informasi pribadi dan kredensial login Anda</p>
    </div>
</div>

<?php if (!empty($error)): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-2xl px-5 py-4 mb-6 text-sm font-bold">
    <i class="fas fa-exclamation-circle text-red-500 flex-shrink-0"></i>
    <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<?php if (!empty($success)): ?>
<div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl px-5 py-4 mb-6 text-sm font-bold">
    <i class="fas fa-check-circle text-emerald-500 flex-shrink-0"></i>
    <span><?= e($success) ?></span>
</div>
<?php endif; ?>

<form method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Profil Card -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-darkblue_alt px-8 py-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">Informasi Profil</h3>
                    <p class="text-gray-400 text-xs mt-0.5">Data identitas pengguna di sistem</p>
                </div>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= e($_POST['nama'] ?? $user['nama']) ?>" required
                               class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all font-bold">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-darkblue_alt uppercase tracking-wider mb-2">Username</label>
                        <input type="text" name="username" value="<?= e($_POST['username'] ?? $user['username']) ?>" required
                               class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all font-bold">
                    </div>
                </div>

                <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center text-blue-600 flex-shrink-0">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-blue-900 mb-1">Hak Akses: <?= strtoupper($user['role']) ?></h4>
                        <p class="text-xs text-blue-700/70 leading-relaxed">
                            <?php if($user['role'] === 'admin' && $user['kelurahan_id'] === null): ?>
                                Anda memiliki akses penuh (Superadmin) untuk mengelola seluruh data di portal PKK Kecamatan Pulomerak.
                            <?php else: ?>
                                Anda memiliki akses sebagai Admin Kelurahan. Anda hanya dapat mengelola data yang berkaitan dengan wilayah Anda sendiri.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Card -->
    <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="bg-darkblue_alt px-8 py-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-500 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-key"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">Ganti Password</h3>
                    <p class="text-gray-400 text-xs mt-0.5">Biarkan kosong jika tidak diganti</p>
                </div>
            </div>
            
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Password Lama</label>
                    <input type="password" name="old_password" placeholder="••••••••"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all">
                </div>
                <hr class="border-gray-50 my-2">
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Password Baru</label>
                    <input type="password" name="new_password" placeholder="Minimal 6 karakter"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all">
                </div>
                <div>
                    <label class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" placeholder="Ulangi password baru"
                           class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-accent transition-all">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full bg-accent hover:bg-darkblue text-white font-black px-8 py-4 rounded-2xl transition-all shadow-xl shadow-accent/20 uppercase tracking-widest text-sm flex items-center justify-center gap-3">
            <i class="fas fa-save"></i> Simpan Perubahan
        </button>
    </div>

</form>

<div class="h-16 lg:h-0"></div>

<?php include 'footer.php'; ?>
