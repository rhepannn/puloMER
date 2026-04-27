<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Inovasi Kecamatan';

include 'include/header.php';
?>

<!-- HERO SECTION -->
<section class="relative w-full py-20 lg:py-28 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-50">
        <img src="<?= SITE_URL ?>/assets/img/1.png" alt="Inovasi" class="w-full h-full object-cover object-center mix-blend-overlay">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>

    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-3 rounded-full bg-white/10 text-white border border-white/20 text-xs font-semibold tracking-wider uppercase mb-4 backdrop-blur-md">
            Program Unggulan
        </span>
        <h1 class="text-3xl md:text-5xl font-bold text-white leading-tight mb-4">
            Inovasi <span class="text-accent">Kecamatan</span>
        </h1>
        <p class="text-gray-300 max-w-2xl mx-auto font-light md:text-lg">
            Program-program inovatif yang dikembangkan oleh Kecamatan Pulomerak untuk meningkatkan kesejahteraan masyarakat.
        </p>
    </div>
</section>

<!-- INOVASI GRID -->
<section class="py-12 bg-softgray">
    <div class="container mx-auto px-4 md:px-6">

        <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">

            <!-- 1. Urban Farming -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer scroll-reveal"
                 onclick="showInovasi('Urban Farming Pulomerak', 'Program pertanian perkotaan yang memanfaatkan lahan kosong untuk budidaya sayuran dan tanaman pangan guna ketahanan pangan warga. Program ini juga mengedukasi warga mengenai cara bercocok tanam di lahan sempit menggunakan teknik hidroponik dan vertikultur.')">
                <div class="p-6 flex flex-col h-full">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue_alt mb-2 group-hover:text-accent transition-colors">Urban Farming Pulomerak</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-grow">Program pertanian perkotaan yang memanfaatkan lahan kosong untuk budidaya sayuran dan tanaman pangan.</p>
                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-1.5 text-accent font-semibold text-sm group/link">
                        Lihat Detail <i class="fas fa-chevron-right text-xs transform group-hover/link:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <!-- 2. Bank Sampah -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer scroll-reveal delay-100"
                 onclick="showInovasi('Bank Sampah Digital', 'Sistem pengelolaan sampah berbasis digital yang memungkinkan warga menukar sampah dengan poin yang bisa digunakan untuk membayar tagihan atau ditukar dengan sembako. Inovasi ini bertujuan mengurangi volume sampah sekaligus memberikan nilai ekonomis bagi warga.')">
                <div class="p-6 flex flex-col h-full">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue_alt mb-2 group-hover:text-accent transition-colors">Bank Sampah Digital</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-grow">Sistem pengelolaan sampah berbasis digital yang memungkinkan warga menukar sampah dengan poin.</p>
                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-1.5 text-accent font-semibold text-sm group/link">
                        Lihat Detail <i class="fas fa-chevron-right text-xs transform group-hover/link:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <!-- 3. Wifi Publik -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer scroll-reveal delay-200"
                 onclick="showInovasi('Wifi Publik Gratis', 'Fasilitas internet gratis di titik-titik strategis kelurahan untuk mendukung pendidikan dan produktivitas warga di era digital. Titik hotspot tersedia di kantor kelurahan, taman bermain, dan area publik lainnya untuk memudahkan akses informasi bagi seluruh lapisan masyarakat.')">
                <div class="p-6 flex flex-col h-full">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue_alt mb-2 group-hover:text-accent transition-colors">Wifi Publik Gratis</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-grow">Fasilitas internet gratis di titik-titik strategis kelurahan untuk mendukung pendidikan warga.</p>
                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-1.5 text-accent font-semibold text-sm group/link">
                        Lihat Detail <i class="fas fa-chevron-right text-xs transform group-hover/link:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <!-- 4. Posyandu -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer scroll-reveal"
                 onclick="showInovasi('Posyandu Terintegrasi', 'Sistem posyandu terintegrasi digital untuk pemantauan kesehatan ibu dan anak secara real-time dengan notifikasi otomatis. Data tumbuh kembang anak dicatat secara digital sehingga memudahkan tenaga kesehatan dalam melakukan intervensi jika ditemukan indikasi masalah kesehatan.')">
                <div class="p-6 flex flex-col h-full">
                    <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue_alt mb-2 group-hover:text-accent transition-colors">Posyandu Terintegrasi</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-grow">Sistem posyandu terintegrasi digital untuk pemantauan kesehatan ibu dan anak secara real-time.</p>
                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-1.5 text-accent font-semibold text-sm group/link">
                        Lihat Detail <i class="fas fa-chevron-right text-xs transform group-hover/link:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <!-- 5. UMKM -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer scroll-reveal delay-100"
                 onclick="showInovasi('Pasar UMKM Digital', 'Platform digital untuk memasarkan produk UMKM lokal Pulomerak ke pasar yang lebih luas melalui media sosial dan e-commerce. Kami memberikan pelatihan packaging, strategi marketing digital, dan akses permodalan bagi para pelaku usaha mikro di wilayah Pulomerak.')">
                <div class="p-6 flex flex-col h-full">
                    <div class="w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-store"></i>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue_alt mb-2 group-hover:text-accent transition-colors">Pasar UMKM Digital</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-grow">Platform digital untuk memasarkan produk UMKM lokal Pulomerak ke pasar yang lebih luas.</p>
                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-1.5 text-accent font-semibold text-sm group/link">
                        Lihat Detail <i class="fas fa-chevron-right text-xs transform group-hover/link:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

            <!-- 6. Kampung Aman -->
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl border border-gray-100 transition-all duration-300 group cursor-pointer scroll-reveal delay-200"
                 onclick="showInovasi('Kampung Aman CCTV', 'Program pemasangan CCTV di titik rawan untuk meningkatkan keamanan lingkungan dan mengurangi angka kriminalitas. Sistem ini terhubung langsung dengan pusat komando di kelurahan dan dapat dipantau oleh pengurus RT/RW setempat guna menjamin keamanan warga.')">
                <div class="p-6 flex flex-col h-full">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 text-red-500 flex items-center justify-center text-2xl mb-5">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="text-lg font-bold text-darkblue_alt mb-2 group-hover:text-accent transition-colors">Kampung Aman CCTV</h3>
                    <p class="text-gray-500 text-sm leading-relaxed flex-grow">Program pemasangan CCTV di titik rawan untuk meningkatkan keamanan lingkungan warga.</p>
                    <div class="mt-5 pt-4 border-t border-gray-50 flex items-center gap-1.5 text-accent font-semibold text-sm group/link">
                        Lihat Detail <i class="fas fa-chevron-right text-xs transform group-hover/link:translate-x-1 transition-transform"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- MODAL INOVASI -->
<div id="modalInovasi" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-[9999] items-center justify-center p-4">
    <div class="bg-white max-w-lg w-full rounded-2xl shadow-2xl overflow-hidden relative" style="animation: modalPop 0.25s ease-out;">
        <button onclick="closeInovasi()" class="absolute top-4 right-4 text-gray-400 hover:text-darkblue_alt p-1 rounded-full hover:bg-gray-100 transition-colors">
            <i class="fas fa-times text-lg"></i>
        </button>
        <div class="p-8">
            <div id="modalIcon" class="w-14 h-14 rounded-2xl bg-blue-50 text-accent flex items-center justify-center text-2xl mb-5">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h3 id="modalTitle" class="text-xl font-bold text-darkblue_alt mb-3">Judul Inovasi</h3>
            <p id="modalDesc" class="text-gray-500 leading-relaxed text-sm">Penjelasan detail akan muncul di sini.</p>
            <button onclick="closeInovasi()" class="mt-6 w-full bg-darkblue hover:bg-darkblue_alt text-white py-3 rounded-xl font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95) translateY(8px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
</style>

<script>
function showInovasi(title, desc) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalDesc').innerText  = desc;
    const modal = document.getElementById('modalInovasi');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}
function closeInovasi() {
    const modal = document.getElementById('modalInovasi');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto';
}
window.addEventListener('click', function(e) {
    const modal = document.getElementById('modalInovasi');
    if (e.target === modal) closeInovasi();
});
</script>

<?php include 'include/footer.php'; ?>
