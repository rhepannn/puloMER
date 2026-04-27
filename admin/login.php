<?php
require_once '../include/config.php';
require_once '../include/functions.php';

if (isAdminLoggedIn()) redirect(SITE_URL . '/admin/index.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_id']           = $user['id'];
            $_SESSION['admin_name']         = $user['nama'];
            $_SESSION['admin_role']         = $user['role'];
            $_SESSION['admin_kelurahan']    = $user['kelurahan_id'];
            setFlash('success', 'Selamat datang, ' . $user['nama'] . '!');
            redirect(SITE_URL . '/admin/index.php');
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkblue:     '#003087',
                        darkblue_alt: '#0A2540',
                        accent:       '#0077B6',
                        softgray:     '#F8FAFC',
                    },
                    fontFamily: { sans: ['Inter','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .glow-blur { filter: blur(80px); }
    </style>
</head>
<body class="h-full bg-darkblue_alt flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Background decorative blurs -->
    <div class="absolute top-0 left-0 w-96 h-96 bg-accent/20 rounded-full glow-blur -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>
    <div class="absolute bottom-0 right-0 w-96 h-96 bg-darkblue/30 rounded-full glow-blur translate-x-1/2 translate-y-1/2 pointer-events-none"></div>
    <div class="absolute top-1/2 right-0 w-64 h-64 bg-accent/10 rounded-full glow-blur translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">

        <!-- Back to site -->
        <a href="<?= SITE_URL ?>/"
           class="flex items-center gap-2 text-white/40 hover:text-white text-sm font-medium mb-8 transition-colors w-fit">
            <i class="fas fa-arrow-left text-xs"></i> Kembali ke Website
        </a>

        <!-- Card -->
        <div class="bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 shadow-2xl p-8 md:p-10">

            <!-- Header -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-2xl bg-accent mx-auto flex items-center justify-center shadow-xl shadow-accent/30 mb-4">
                    <i class="fas fa-building-columns text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white mb-1">Admin Panel</h1>
                <p class="text-white/40 text-sm">Portal TP PKK Kecamatan Pulomerak</p>
            </div>

            <!-- Error -->
            <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-500/10 border border-red-500/20 text-red-300 rounded-2xl px-4 py-3 mb-6 text-sm">
                <i class="fas fa-exclamation-circle flex-shrink-0"></i>
                <span><?= e($error) ?></span>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" autocomplete="off" class="space-y-5">
                <div>
                    <label class="block text-xs font-bold text-white/50 uppercase tracking-widest mb-2">Username</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                            <i class="fas fa-user text-sm"></i>
                        </span>
                        <input type="text" name="username"
                               value="<?= e($_POST['username'] ?? '') ?>"
                               placeholder="Masukkan username"
                               required autofocus
                               class="w-full bg-white/5 border border-white/10 text-white placeholder-white/20 rounded-2xl pl-11 pr-4 py-3.5 text-sm focus:outline-none focus:border-accent/60 focus:bg-white/10 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-white/50 uppercase tracking-widest mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-white/30">
                            <i class="fas fa-lock text-sm"></i>
                        </span>
                        <input type="password" name="password" id="passInput"
                               placeholder="Masukkan password"
                               required
                               class="w-full bg-white/5 border border-white/10 text-white placeholder-white/20 rounded-2xl pl-11 pr-12 py-3.5 text-sm focus:outline-none focus:border-accent/60 focus:bg-white/10 transition-all">
                        <button type="button" onclick="togglePass()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition-colors">
                            <i class="fas fa-eye text-sm" id="passIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-accent hover:bg-darkblue text-white font-bold py-3.5 rounded-2xl flex items-center justify-center gap-2 transition-all shadow-lg shadow-accent/30 hover:shadow-darkblue/30 mt-2">
                    <i class="fas fa-sign-in-alt"></i> Masuk ke Dashboard
                </button>
            </form>
        </div>

        <p class="text-center text-white/20 text-xs mt-6">© <?= date('Y') ?> TP PKK Kecamatan Pulomerak</p>
    </div>

    <script>
    function togglePass() {
        const inp  = document.getElementById('passInput');
        const icon = document.getElementById('passIcon');
        if (inp.type === 'password') { inp.type = 'text';     icon.className = 'fas fa-eye-slash text-sm'; }
        else                         { inp.type = 'password'; icon.className = 'fas fa-eye text-sm'; }
    }
    </script>
</body>
</html>
