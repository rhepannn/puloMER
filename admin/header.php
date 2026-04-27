<?php
// Admin header - used by all admin pages
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle).' — ' : '' ?>Admin Panel · PKK Pulomerak</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Tailwind CSS (CDN) - same as public site -->
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
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        /* Sidebar width token */
        :root { --sidebar-w: 260px; }
        body { font-family: 'Inter', sans-serif; }

        /* Sidebar slide-in on mobile */
        @media (max-width: 1024px) {
            #adminSidebar {
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            }
            #adminSidebar.open { transform: translateX(0); }
        }

        /* Custom scrollbar inside sidebar */
        #sidebarNav::-webkit-scrollbar { width: 4px; }
        #sidebarNav::-webkit-scrollbar-track { background: transparent; }
        #sidebarNav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

        /* Smooth fade for flash messages */
        .flash-auto { animation: flashFade 4s ease forwards; }
        @keyframes flashFade { 0%,70%{opacity:1} 100%{opacity:0;pointer-events:none} }
    </style>
</head>
<body class="bg-softgray font-sans antialiased h-full">

<!-- ─────────────────────────────────────────── SIDEBAR -->
<aside id="adminSidebar"
       class="fixed top-0 left-0 h-full z-40 flex flex-col bg-darkblue_alt text-white"
       style="width:var(--sidebar-w)">

    <!-- Brand -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-white/10 flex-shrink-0">
        <div class="w-10 h-10 rounded-xl bg-accent flex items-center justify-center shadow-lg shadow-accent/30 flex-shrink-0">
            <i class="fas fa-building-columns text-white text-sm"></i>
        </div>
        <div>
            <div class="font-bold text-white text-sm leading-tight">Admin Panel</div>
            <div class="text-[10px] text-white/50 font-medium uppercase tracking-widest">PKK Pulomerak</div>
        </div>
        <!-- Close button (mobile) -->
        <button id="sidebarClose" class="ml-auto lg:hidden text-white/40 hover:text-white transition-colors p-1">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Nav -->
    <nav id="sidebarNav" class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

        <!-- Label -->
        <div class="px-3 pt-2 pb-1 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em]">Utama</div>

        <a href="<?= SITE_URL ?>/admin/index.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $adminPage === 'index' ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-tachometer-alt w-4 text-center"></i> Dashboard
        </a>

        <?php if (isSuperAdmin()): ?>
        <a href="<?= SITE_URL ?>/admin/konten.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $adminPage === 'konten' ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-edit w-4 text-center"></i> Editor Konten
        </a>
        <?php else: ?>
        <a href="<?= SITE_URL ?>/admin/kelurahan-edit.php?id=<?= getKelurahanId() ?>"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $adminPage === 'kelurahan-edit' ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-chart-line w-4 text-center"></i> Data Warga Saya
        </a>
        <?php endif; ?>

        <!-- Label -->
        <div class="px-3 pt-4 pb-1 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em]">Konten</div>

        <a href="<?= SITE_URL ?>/admin/berita.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= strpos($adminPage,'berita') === 0 ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-newspaper w-4 text-center"></i> Berita
        </a>
        <a href="<?= SITE_URL ?>/admin/kegiatan.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= strpos($adminPage,'kegiatan') === 0 ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-calendar-check w-4 text-center"></i> Kegiatan
        </a>
        <a href="<?= SITE_URL ?>/admin/laporan.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= strpos($adminPage,'laporan') === 0 ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-file-alt w-4 text-center"></i> Laporan
        </a>

        <!-- Label -->
        <div class="px-3 pt-4 pb-1 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em]">Wilayah & Data</div>

        <?php if (isSuperAdmin()): ?>
        <a href="<?= SITE_URL ?>/admin/kelurahan.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= strpos($adminPage,'kelurahan') === 0 ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-map-marked-alt w-4 text-center"></i> Manajemen Wilayah
        </a>
        <?php endif; ?>
        <a href="<?= SITE_URL ?>/admin/bidang.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= (strpos($adminPage,'bidang') === 0 || strpos($adminPage,'anggota') === 0) ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-sitemap w-4 text-center"></i> Bidang & Pengurus
        </a>

        <?php if (isSuperAdmin()): ?>
        <!-- Label -->
        <div class="px-3 pt-4 pb-1 text-[9px] font-bold text-white/30 uppercase tracking-[0.2em]">Pengaturan</div>

        <a href="<?= SITE_URL ?>/admin/users.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= strpos($adminPage,'users') === 0 ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-user-shield w-4 text-center"></i> Manajemen User
        </a>
        <a href="<?= SITE_URL ?>/admin/settings.php"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all <?= $adminPage === 'settings' ? 'bg-accent text-white shadow-lg shadow-accent/30' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="fas fa-cog w-4 text-center"></i> Pengaturan Web
        </a>
        <?php endif; ?>

        <!-- Separator -->
        <div class="pt-4 border-t border-white/10 mt-4 space-y-0.5">
            <a href="<?= SITE_URL ?>/" target="_blank"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-white/40 hover:bg-white/10 hover:text-white transition-all">
                <i class="fas fa-external-link-alt w-4 text-center"></i> Lihat Website
            </a>
            <a href="<?= SITE_URL ?>/admin/logout.php"
               onclick="return confirm('Yakin ingin keluar?')"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-all">
                <i class="fas fa-sign-out-alt w-4 text-center"></i> Keluar
            </a>
        </div>
    </nav>

    <!-- User Badge -->
    <div class="flex items-center gap-3 px-5 py-4 border-t border-white/10 flex-shrink-0 bg-black/20">
        <div class="w-9 h-9 rounded-xl bg-accent/20 border border-accent/30 flex items-center justify-center text-accent flex-shrink-0">
            <i class="fas fa-user text-xs"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-semibold text-white truncate"><?= e($adminName) ?></div>
            <div class="text-[10px] text-white/40 truncate"><?= e($adminRole) ?></div>
        </div>
    </div>
</aside>

<!-- Sidebar overlay (mobile) -->
<div id="sidebarOverlay"
     class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 hidden lg:hidden"
     onclick="closeSidebar()"></div>

<!-- ─────────────────────────────────────────── MAIN WRAPPER -->
<div class="lg:pl-[260px] min-h-screen flex flex-col transition-all duration-300">

    <!-- TOP BAR -->
    <header class="sticky top-0 z-20 bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm">
        <div class="flex items-center justify-between px-4 md:px-6 h-16">
            <!-- Left: Hamburger + Title -->
            <div class="flex items-center gap-3">
                <button id="sidebarToggle"
                        class="lg:hidden w-9 h-9 rounded-xl border border-gray-200 flex items-center justify-center text-darkblue_alt hover:bg-softgray transition-colors"
                        onclick="openSidebar()">
                    <i class="fas fa-bars text-sm"></i>
                </button>
                <div>
                    <h1 class="font-bold text-darkblue_alt text-base md:text-lg leading-tight">
                        <?= isset($pageTitle) ? e($pageTitle) : 'Dashboard' ?>
                    </h1>
                </div>
            </div>

            <!-- Right: Admin info -->
            <div class="flex items-center gap-3">
                <a href="<?= SITE_URL ?>/" target="_blank"
                   class="hidden md:flex items-center gap-1.5 text-xs text-gray-400 hover:text-accent transition-colors border border-gray-200 px-3 py-1.5 rounded-lg hover:border-accent/30">
                    <i class="fas fa-external-link-alt text-[10px]"></i> Website
                </a>
                <div class="flex items-center gap-2 bg-softgray rounded-xl px-3 py-2 border border-gray-100">
                    <div class="w-7 h-7 rounded-lg bg-accent flex items-center justify-center">
                        <i class="fas fa-user text-white text-[10px]"></i>
                    </div>
                    <div class="hidden sm:block">
                        <div class="text-xs font-bold text-darkblue_alt leading-none"><?= e($adminName) ?></div>
                        <div class="text-[10px] text-gray-400 leading-none mt-0.5"><?= e($adminRole) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- PAGE CONTENT -->
    <main class="flex-1 p-4 md:p-6 lg:p-8">
        <?= showFlash() ?>
