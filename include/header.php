<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Get latest news/activities for ticker
$ticker_items = [];
if(isset($conn)) {
    $resB = $conn->query("SELECT id, judul, 'berita' as type FROM berita ORDER BY tgl_post DESC LIMIT 3");
    if($resB) while($row = $resB->fetch_assoc()) $ticker_items[] = $row;
    
    $resK = $conn->query("SELECT id, judul, 'kegiatan' as type FROM kegiatan ORDER BY tgl_kegiatan DESC LIMIT 3");
    if($resK) while($row = $resK->fetch_assoc()) $ticker_items[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= isset($pageDesc) ? e($pageDesc) : (defined('SITE_DESC') ? SITE_DESC : 'Portal TP PKK Kecamatan Pulomerak') ?>">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?><?= defined('SITE_NAME') ? SITE_NAME : 'TP PKK Pulomerak' ?></title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Tailwind CSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        darkblue: '#003087',
                        darkblue_alt: '#0A2540',
                        accent: '#0077B6',
                        softgray: '#F8FAFC',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom humanize classes & subtle animations */
        .human-skew { transform: skew(-4deg); }
        .human-skew-alt { transform: skew(-6deg); }
        .unskew { transform: skew(4deg); }
        
        .fade-in-up {
            opacity: 0;
            transform: translateY(15px);
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        }
        
        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Delays for cascading entrance */
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        .delay-400 { animation-delay: 400ms; }
        
        /* Sticky navbar glass effect */
        .nav-scrolled {
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        
        /* Make everything smaller on mobile to look more compact */
        @media (max-width: 768px) {
            html { font-size: 14px; }
        }
        @media (max-width: 480px) {
            html { font-size: 13px; }
        }
    </style>
</head>
<body class="bg-softgray text-darkblue_alt font-sans antialiased flex flex-col min-h-screen">

<!-- Top Bar (Running Text) -->
<div class="bg-darkblue text-white text-[10px] md:text-xs py-1.5 overflow-hidden border-b border-white/10">
    <div class="container mx-auto px-4 md:px-6 flex items-center">
        <div class="bg-accent text-white px-3 py-0.5 rounded-sm font-bold text-[9px] md:text-xs whitespace-nowrap z-10 relative flex-shrink-0 flex items-center gap-1.5 md:gap-2 shadow-md">
            <span class="hidden md:inline">Info Terbaru</span><span class="md:hidden">Info</span>
        </div>
        <div class="flex-1 overflow-hidden ml-3 relative flex items-center h-full">
            <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover="this.stop();" onmouseout="this.start();" class="flex items-center text-gray-200 w-full h-full">
                <div class="flex items-center gap-8">
                <?php if(!empty($ticker_items)): ?>
                    <?php foreach($ticker_items as $t): 
                        $link = defined('SITE_URL') ? SITE_URL : '';
                        $link .= ($t['type'] === 'berita') ? '/berita-detail.php?id='.$t['id'] : '/kegiatan-detail.php?id='.$t['id'];
                        $icon = ($t['type'] === 'berita') ? 'fa-newspaper' : 'fa-calendar-check';
                    ?>
                        <a href="<?= $link ?>" class="hover:text-accent transition-colors inline-flex items-center gap-1.5">
                            <i class="fas <?= $icon ?> text-accent opacity-80"></i> <?= e($t['judul']) ?>
                        </a>
                        <span class="text-white/20 px-2">•</span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="italic text-gray-400">Selamat Datang di Portal TP PKK Kecamatan Pulomerak</span>
                <?php endif; ?>
                </div>
            </marquee>
        </div>
    </div>
</div>

<!-- Navbar -->
<header id="mainNav" class="bg-white sticky top-0 z-50 transition-all duration-300 shadow-sm border-b border-gray-100">
    <div class="container mx-auto px-4 md:px-6 py-4 md:py-5 transition-all duration-300" id="navInner">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <a href="<?= defined('SITE_URL') ? SITE_URL : '/' ?>" class="flex items-center gap-3 group">
                <img src="<?= defined('SITE_URL') ? SITE_URL : '' ?>/assets/img/pkk_logo.png" alt="Logo PKK" class="w-10 h-10 md:w-12 md:h-12 object-contain group-hover:scale-105 transition-transform duration-500" onerror="this.src='https://via.placeholder.com/50?text=PKK'">
                <div class="flex flex-col">
                    <span class="font-bold text-darkblue leading-tight md:text-lg tracking-wide">Tim Penggerak PKK</span>
                    <span class="text-xs md:text-sm text-accent font-medium">Kecamatan Pulomerak</span>
                </div>
            </a>

            <!-- Mobile Toggle -->
            <button id="mobileMenuBtn" class="md:hidden text-darkblue hover:text-accent focus:outline-none p-2">
                <i class="fas fa-bars text-2xl"></i>
            </button>

            <!-- Desktop Menu -->
            <nav class="hidden md:flex items-center gap-6 lg:gap-8 font-medium">
                <a href="<?= defined('SITE_URL') ? SITE_URL : '/' ?>/" class="text-darkblue_alt hover:text-accent transition-colors duration-300 <?= $currentPage === 'index' ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">Beranda</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/profil.php" class="text-darkblue_alt hover:text-accent transition-colors duration-300 <?= $currentPage === 'profil' ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">Profil</a>
                
                <!-- Dropdown Program -->
                <div class="relative group">
                    <button class="flex items-center gap-1 text-darkblue_alt hover:text-accent transition-colors duration-300 <?= in_array($currentPage, ['program', 'bidang-detail']) ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">
                        Program Pokok <i class="fas fa-chevron-down text-xs mt-1 transition-transform group-hover:rotate-180"></i>
                    </button>
                    <!-- Asymmetry touch: dropdown offset slightly -->
                    <div class="absolute top-full left-0 mt-2 w-56 bg-white shadow-xl rounded-lg overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 border border-gray-100 transform translate-y-2 group-hover:translate-y-0">
                        <ul class="py-2">
                            <?php 
                            if(isset($conn)) {
                                $navBidang = $conn->query("SELECT nama, slug FROM bidang ORDER BY urutan ASC");
                                if($navBidang) {
                                    while($nb = $navBidang->fetch_assoc()): 
                            ?>
                            <li>
                                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/bidang-detail.php?slug=<?= $nb['slug'] ?>" class="block px-4 py-2 text-sm text-darkblue_alt hover:bg-softgray hover:text-accent hover:pl-5 transition-all duration-300 border-l-2 border-transparent hover:border-accent"><?= e($nb['nama']) ?></a>
                            </li>
                            <?php 
                                    endwhile;
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>

                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/kelurahan.php" class="text-darkblue_alt hover:text-accent transition-colors duration-300 <?= in_array($currentPage, ['kelurahan', 'kelurahan-detail', 'rtrw']) ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">Wilayah</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/kegiatan.php" class="text-darkblue_alt hover:text-accent transition-colors duration-300 <?= $currentPage === 'kegiatan' ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">Kegiatan</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/berita.php" class="text-darkblue_alt hover:text-accent transition-colors duration-300 <?= in_array($currentPage, ['berita', 'berita-detail']) ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">Berita</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/inovasi.php" class="text-darkblue_alt hover:text-accent transition-colors duration-300 <?= $currentPage === 'inovasi' ? 'text-accent border-b-2 border-accent pb-1' : '' ?>">Inovasi</a>

                <!-- Login Button -->
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/admin/login.php" class="ml-2 bg-darkblue text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-darkblue_alt transition-all shadow-md flex items-center gap-2">
                    <i class="fas fa-sign-in-alt text-[10px]"></i> Login
                </a>
            </nav>
        </div>
    </div>
    </div>
</header>

<!-- Mobile Menu Overlay (Moved outside header to fix z-index/stacking issues) -->
<div id="mobileMenu" class="fixed inset-0 bg-darkblue/80 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300" style="z-index: 9999;">
    <div class="flex flex-col h-full bg-white w-[85%] max-w-sm shadow-2xl transform -translate-x-full transition-transform duration-300 relative" id="mobileMenuPanel">
        <div class="p-5 flex justify-between items-center border-b border-gray-100 bg-softgray">
            <div class="flex items-center gap-3">
                <img src="<?= defined('SITE_URL') ? SITE_URL : '' ?>/assets/img/pkk_logo.png" alt="PKK" class="w-8 h-8 object-contain">
                <span class="font-bold text-darkblue text-lg">PKK Pulomerak</span>
            </div>
            <button id="closeMenuBtn" class="text-gray-400 hover:text-darkblue p-2 rounded-full hover:bg-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 py-4">
            <a href="<?= defined('SITE_URL') ? SITE_URL : '/' ?>" class="block px-6 py-3 text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent <?= $currentPage === 'index' ? 'border-accent bg-softgray text-accent font-semibold' : '' ?>">Beranda</a>
            <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/profil.php" class="block px-6 py-3 text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent <?= $currentPage === 'profil' ? 'border-accent bg-softgray text-accent font-semibold' : '' ?>">Profil</a>
            
            <div class="px-6 py-3 text-darkblue font-semibold bg-gray-50 mt-2">Program Pokok</div>
            <?php 
            if(isset($conn)) {
                $navBidang2 = $conn->query("SELECT nama, slug FROM bidang ORDER BY urutan ASC");
                if($navBidang2) {
                    while($nb2 = $navBidang2->fetch_assoc()): 
            ?>
            <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/bidang-detail.php?slug=<?= $nb2['slug'] ?>" class="block px-8 py-2 text-sm text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent">- <?= e($nb2['nama']) ?></a>
            <?php 
                    endwhile;
                }
            }
            ?>
            
            <div class="mt-2 border-t border-gray-100 pt-2">
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/kelurahan.php" class="block px-6 py-3 text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent <?= in_array($currentPage, ['kelurahan', 'kelurahan-detail', 'rtrw']) ? 'border-accent bg-softgray text-accent font-semibold' : '' ?>">Wilayah Kelurahan</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/kegiatan.php" class="block px-6 py-3 text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent <?= $currentPage === 'kegiatan' ? 'border-accent bg-softgray text-accent font-semibold' : '' ?>">Kegiatan</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/berita.php" class="block px-6 py-3 text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent <?= in_array($currentPage, ['berita', 'berita-detail']) ? 'border-accent bg-softgray text-accent font-semibold' : '' ?>">Berita</a>
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/inovasi.php" class="block px-6 py-3 text-darkblue_alt hover:bg-softgray hover:text-accent border-l-4 border-transparent <?= $currentPage === 'inovasi' ? 'border-accent bg-softgray text-accent font-semibold' : '' ?>">Inovasi</a>
            </div>

            <!-- Mobile Login Button -->
            <div class="px-6 py-6">
                <a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/admin/login.php" class="w-full bg-darkblue text-white px-6 py-4 rounded-2xl font-bold flex items-center justify-center gap-3 shadow-lg hover:bg-darkblue_alt transition-all">
                    <i class="fas fa-sign-in-alt"></i> Login Admin
                </a>
            </div>
        </div>
    </div>
</div>
