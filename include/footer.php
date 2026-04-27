<?php
// Ambil settings footer (sesuaikan dengan config sebelumnya jika ada)
$footerS = isset($conn) ? getAllSettings($conn, 'footer') : [];
$footerLogo = defined('SITE_URL') ? SITE_URL . '/assets/img/pkk_logo.png' : '/assets/img/pkk_logo.png';
?>
<!-- Main Footer -->
<footer class="bg-darkblue_alt text-white mt-auto relative overflow-hidden">
    <!-- Subtle top border / detail -->
    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-accent via-white/20 to-accent opacity-50"></div>
    
    <div class="container mx-auto px-4 md:px-6 pt-10 pb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-8">
            
            <!-- Brand & About -->
            <div class="lg:col-span-5 fade-in-up">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-white p-1.5 rounded-lg inline-block human-skew-alt">
                        <img src="<?= $footerLogo ?>" alt="Logo PKK" class="w-10 h-10 object-contain unskew" onerror="this.src='https://via.placeholder.com/50'">
                    </div>
                    <div>
                        <h3 class="text-lg md:text-xl font-bold text-white tracking-wide">TP PKK Pulomerak</h3>
                        <p class="text-accent text-xs md:text-sm font-medium">Kota Cilegon, Banten</p>
                    </div>
                </div>
                <p class="text-gray-300 text-xs md:text-sm leading-relaxed mb-4 pr-0 md:pr-8">
                    <?= e($footerS['footer_deskripsi'] ?? 'Gerakan Pemberdayaan dan Kesejahteraan Keluarga (PKK) dengan tujuan memberdayakan keluarga untuk meningkatkan kesejahteraan menuju terwujudnya keluarga yang beriman, bertaqwa, berakhlak mulia dan berbudi luhur.') ?>
                </p>
                <!-- Social -->
                <div class="flex gap-4">
                    <a href="<?= e($footerS['footer_instagram'] ?? '#') ?>" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-accent transition-colors duration-300 group">
                        <i class="fab fa-instagram text-white group-hover:scale-110 transition-transform"></i>
                    </a>
                    <a href="<?= e($footerS['footer_youtube'] ?? '#') ?>" target="_blank" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-accent transition-colors duration-300 group">
                        <i class="fab fa-youtube text-white group-hover:scale-110 transition-transform"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-accent transition-colors duration-300 group">
                        <i class="fab fa-facebook-f text-white group-hover:scale-110 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Tautan Cepat -->
            <div class="lg:col-span-3 fade-in-up delay-100">
                <h4 class="text-base md:text-lg font-semibold mb-4 pb-1 inline-block relative">
                    Tautan Cepat
                    <span class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-accent"></span>
                </h4>
                <ul class="space-y-2">
                    <li><a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/profil.php" class="text-gray-300 hover:text-accent hover:pl-2 transition-all duration-300 text-sm flex items-center gap-2"><i class="fas fa-angle-right text-accent text-xs"></i> Profil PKK</a></li>
                    <li><a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/kegiatan.php" class="text-gray-300 hover:text-accent hover:pl-2 transition-all duration-300 text-sm flex items-center gap-2"><i class="fas fa-angle-right text-accent text-xs"></i> Daftar Kegiatan</a></li>
                    <li><a href="<?= defined('SITE_URL') ? SITE_URL : '' ?>/berita.php" class="text-gray-300 hover:text-accent hover:pl-2 transition-all duration-300 text-sm flex items-center gap-2"><i class="fas fa-angle-right text-accent text-xs"></i> Berita Terbaru</a></li>
                </ul>
            </div>

            <!-- Kontak -->
            <div class="lg:col-span-4 fade-in-up delay-200">
                <h4 class="text-base md:text-lg font-semibold mb-4 pb-1 inline-block relative">
                    Kontak Kami
                    <span class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-accent"></span>
                </h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <div class="mt-1 bg-white/5 p-2 rounded-full"><i class="fas fa-map-marker-alt text-accent"></i></div>
                        <span class="text-gray-300 text-sm leading-relaxed"><?= e($footerS['footer_alamat'] ?? 'Kantor Kecamatan Pulomerak<br>Jl. Raya Merak, Kota Cilegon, Banten') ?></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="bg-white/5 p-2 rounded-full"><i class="fas fa-phone-alt text-accent"></i></div>
                        <span class="text-gray-300 text-sm"><?= e($footerS['footer_telepon'] ?? '(0254) 571234') ?></span>
                    </li>
                    <li class="flex items-center gap-3">
                        <div class="bg-white/5 p-2 rounded-full"><i class="fas fa-envelope text-accent"></i></div>
                        <span class="text-gray-300 text-sm"><?= e($footerS['footer_email'] ?? 'kec.pulomerak@cilegon.go.id') ?></span>
                    </li>
                </ul>
            </div>
            
        </div>
    </div>

    <!-- Copyright -->
    <div class="border-t border-white/5 bg-black/20">
        <div class="container mx-auto px-4 md:px-6 py-3 flex flex-col md:flex-row justify-between items-center gap-2">
            <p class="text-gray-400 text-[10px] md:text-xs text-center md:text-left">
                &copy; <?= date('Y') ?> <strong>TP PKK Kecamatan Pulomerak</strong>. All rights reserved.
            </p>
        </div>
    </div>
</footer>

<!-- Vanilla JS UI Logic -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Sticky Navbar Effect
        const header = document.getElementById('mainNav');
        const navInner = document.getElementById('navInner');
        
        if(header && navInner) {
            window.addEventListener('scroll', () => {
                if (window.scrollY > 20) {
                    header.classList.add('nav-scrolled');
                    navInner.classList.remove('py-4', 'md:py-5');
                    navInner.classList.add('py-2', 'md:py-3');
                } else {
                    header.classList.remove('nav-scrolled');
                    navInner.classList.remove('py-2', 'md:py-3');
                    navInner.classList.add('py-4', 'md:py-5');
                }
            });
        }
        
        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const closeMenuBtn = document.getElementById('closeMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        const mobileMenuPanel = document.getElementById('mobileMenuPanel');
        
        if(mobileMenuBtn && closeMenuBtn && mobileMenu && mobileMenuPanel) {
            function openMenu() {
                mobileMenu.classList.remove('hidden');
                // Brief delay to allow display:block to apply before animating opacity
                setTimeout(() => {
                    mobileMenu.classList.remove('opacity-0');
                    mobileMenuPanel.classList.remove('-translate-x-full');
                }, 20);
                document.body.style.overflow = 'hidden';
            }
            
            function closeMenu() {
                mobileMenu.classList.add('opacity-0');
                mobileMenuPanel.classList.add('-translate-x-full');
                setTimeout(() => {
                    mobileMenu.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 300);
            }
            
            mobileMenuBtn.addEventListener('click', openMenu);
            closeMenuBtn.addEventListener('click', closeMenu);
            
            // Close menu if clicking outside panel
            mobileMenu.addEventListener('click', (e) => {
                if(e.target === mobileMenu) closeMenu();
            });
        }
        
        // Element reveal on scroll (fade-in-up for non-footer elements)
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in-up');
                    entry.target.style.opacity = '1';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);
        
        document.querySelectorAll('.scroll-reveal').forEach(el => {
            el.style.opacity = '0'; // Initial state
            observer.observe(el);
        });
    });
</script>
</body>
</html>
