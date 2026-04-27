<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Inovasi Kecamatan';

include 'include/header.php';
?>

<!-- PAGE HERO -->
<section class="hero compact" style="background-image: url('<?= SITE_URL ?>/assets/img/1.png') !important; background-size: cover; background-position: center;">
    <div class="hero-overlay" style="background: linear-gradient(90deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.4) 100%);"></div>
    <div class="container hero-content animate-fade-up">
        <h1 style="color: white; font-size: 2.2rem; font-weight: 800; margin: 0;">Inovasi Kecamatan</h1>
    </div>
</section>

<section class="section section-alt" id="inovasi">
    <div class="container">
        <div class="section-header">
            <div class="section-label"><i class="fas fa-lightbulb"></i> Program Unggulan</div>
            <h2 class="section-title">Inovasi <span>Kecamatan</span></h2>
            <p class="section-desc">Program-program inovatif yang dikembangkan oleh Kecamatan Pulomerak untuk meningkatkan kesejahteraan masyarakat.</p>
        </div>
        <div class="grid-3">
            <!-- 1. Urban Farming -->
            <div class="card reveal" style="border-radius: 12px; border: none; box-shadow: var(--shadow-sm); padding: 32px; cursor: pointer;" onclick="showInovasi('Urban Farming Pulomerak', 'Program pertanian perkotaan yang memanfaatkan lahan kosong untuk budidaya sayuran dan tanaman pangan guna ketahanan pangan warga. Program ini juga mengedukasi warga mengenai cara bercocok tanam di lahan sempit menggunakan teknik hidroponik dan vertikultur.')">
                <div class="shortcut-icon" style="background: #ecfdf5; color: #059669; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 24px; font-size: 1.5rem;">
                    <i class="fas fa-seedling"></i>
                </div>
                <h3 class="card-title" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; color: var(--gray-900);">Urban Farming Pulomerak</h3>
                <p class="card-text" style="font-size: 0.95rem; color: var(--gray-600); line-height: 1.6;">Program pertanian perkotaan yang memanfaatkan lahan kosong untuk budidaya sayuran...</p>
                <div style="margin-top: 15px; color: var(--primary); font-weight: 700; font-size: 0.85rem;">Lihat Detail <i class="fas fa-arrow-right"></i></div>
            </div>

            <!-- 2. Bank Sampah -->
            <div class="card reveal animate-delay-1" style="border-radius: 12px; border: none; box-shadow: var(--shadow-sm); padding: 32px; cursor: pointer;" onclick="showInovasi('Bank Sampah Digital', 'Sistem pengelolaan sampah berbasis digital yang memungkinkan warga menukar sampah dengan poin yang bisa digunakan untuk membayar tagihan atau ditukar dengan sembako. Inovasi ini bertujuan mengurangi volume sampah sekaligus memberikan nilai ekonomis bagi warga.')">
                <div class="shortcut-icon" style="background: #eff6ff; color: #2563eb; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 24px; font-size: 1.5rem;">
                    <i class="fas fa-recycle"></i>
                </div>
                <h3 class="card-title" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; color: var(--gray-900);">Bank Sampah Digital</h3>
                <p class="card-text" style="font-size: 0.95rem; color: var(--gray-600); line-height: 1.6;">Sistem pengelolaan sampah berbasis digital yang memungkinkan warga menukar sampah...</p>
                <div style="margin-top: 15px; color: var(--primary); font-weight: 700; font-size: 0.85rem;">Lihat Detail <i class="fas fa-arrow-right"></i></div>
            </div>

            <!-- 3. Wifi Publik -->
            <div class="card reveal animate-delay-2" style="border-radius: 12px; border: none; box-shadow: var(--shadow-sm); padding: 32px; cursor: pointer;" onclick="showInovasi('Wifi Publik Gratis', 'Fasilitas internet gratis di titik-titik strategis kelurahan untuk mendukung pendidikan dan produktivitas warga di era digital. Titik hotspot tersedia di kantor kelurahan, taman bermain, dan area publik lainnya untuk memudahkan akses informasi bagi seluruh lapisan masyarakat.')">
                <div class="shortcut-icon" style="background: #fff7ed; color: #ea580c; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 24px; font-size: 1.5rem;">
                    <i class="fas fa-wifi"></i>
                </div>
                <h3 class="card-title" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; color: var(--gray-900);">Wifi Publik Gratis</h3>
                <p class="card-text" style="font-size: 0.95rem; color: var(--gray-600); line-height: 1.6;">Fasilitas internet gratis di titik-titik strategis kelurahan untuk mendukung pendidikan...</p>
                <div style="margin-top: 15px; color: var(--primary); font-weight: 700; font-size: 0.85rem;">Lihat Detail <i class="fas fa-arrow-right"></i></div>
            </div>

            <!-- 4. Posyandu -->
            <div class="card reveal" style="border-radius: 12px; border: none; box-shadow: var(--shadow-sm); padding: 32px; cursor: pointer;" onclick="showInovasi('Posyandu Terintegrasi', 'Sistem posyandu terintegrasi digital untuk pemantauan kesehatan ibu dan anak secara real-time dengan notifikasi otomatis. Data tumbuh kembang anak dicatat secara digital sehingga memudahkan tenaga kesehatan dalam melakukan intervensi jika ditemukan indikasi masalah kesehatan.')">
                <div class="shortcut-icon" style="background: #faf5ff; color: #9333ea; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 24px; font-size: 1.5rem;">
                    <i class="fas fa-heartbeat"></i>
                </div>
                <h3 class="card-title" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; color: var(--gray-900);">Posyandu Terintegrasi</h3>
                <p class="card-text" style="font-size: 0.95rem; color: var(--gray-600); line-height: 1.6;">Sistem posyandu terintegrasi digital untuk pemantauan kesehatan ibu dan anak...</p>
                <div style="margin-top: 15px; color: var(--primary); font-weight: 700; font-size: 0.85rem;">Lihat Detail <i class="fas fa-arrow-right"></i></div>
            </div>

            <!-- 5. UMKM -->
            <div class="card reveal animate-delay-1" style="border-radius: 12px; border: none; box-shadow: var(--shadow-sm); padding: 32px; cursor: pointer;" onclick="showInovasi('Pasar UMKM Digital', 'Platform digital untuk memasarkan produk UMKM lokal Pulomerak ke pasar yang lebih luas melalui media sosial dan e-commerce. Kami memberikan pelatihan packaging, strategi marketing digital, dan akses permodalan bagi para pelaku usaha mikro di wilayah Pulomerak.')">
                <div class="shortcut-icon" style="background: #f0fdfa; color: #0d9488; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 24px; font-size: 1.5rem;">
                    <i class="fas fa-store"></i>
                </div>
                <h3 class="card-title" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; color: var(--gray-900);">Pasar UMKM Digital</h3>
                <p class="card-text" style="font-size: 0.95rem; color: var(--gray-600); line-height: 1.6;">Platform digital untuk memasarkan produk UMKM lokal Pulomerak ke pasar yang lebih luas...</p>
                <div style="margin-top: 15px; color: var(--primary); font-weight: 700; font-size: 0.85rem;">Lihat Detail <i class="fas fa-arrow-right"></i></div>
            </div>

            <!-- 6. Kampung Aman -->
            <div class="card reveal animate-delay-2" style="border-radius: 12px; border: none; box-shadow: var(--shadow-sm); padding: 32px; cursor: pointer;" onclick="showInovasi('Kampung Aman CCTV', 'Program pemasangan CCTV di titik rawan untuk meningkatkan keamanan lingkungan dan mengurangi angka kriminalitas. Sistem ini terhubung langsung dengan pusat komando di kelurahan dan dapat dipantau oleh pengurus RT/RW setempat guna menjamin keamanan warga.')">
                <div class="shortcut-icon" style="background: #fef2f2; color: #dc2626; width: 64px; height: 64px; border-radius: 16px; margin-bottom: 24px; font-size: 1.5rem;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="card-title" style="font-size: 1.25rem; font-weight: 800; margin-bottom: 12px; color: var(--gray-900);">Kampung Aman CCTV</h3>
                <p class="card-text" style="font-size: 0.95rem; color: var(--gray-600); line-height: 1.6;">Program pemasangan CCTV di titik rawan untuk meningkatkan keamanan lingkungan...</p>
                <div style="margin-top: 15px; color: var(--primary); font-weight: 700; font-size: 0.85rem;">Lihat Detail <i class="fas fa-arrow-right"></i></div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL INOVASI -->
<div id="modalInovasi" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.7); z-index:9999; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(5px);">
    <div style="background:white; max-width:500px; width:100%; border-radius:24px; overflow:hidden; box-shadow:0 25px 50px rgba(0,0,0,0.25); position:relative; animation: modalPop 0.3s ease-out;">
        <div style="padding:40px 30px;">
            <div id="modalIcon" style="width:60px; height:60px; background:var(--primary-glow); color:var(--primary); border-radius:16px; display:flex; align-items:center; justify-content:center; font-size:1.8rem; margin-bottom:20px;">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h3 id="modalTitle" style="font-size:1.5rem; font-weight:800; color:var(--primary); margin-bottom:15px;">Judul Inovasi</h3>
            <p id="modalDesc" style="color:var(--gray-600); line-height:1.8; font-size:1rem;">Penjelasan detail akan muncul di sini.</p>
            <button onclick="closeInovasi()" style="margin-top:30px; width:100%; background:var(--primary); color:white; padding:15px; border-radius:12px; font-weight:700; font-size:1rem; transition:all 0.2s;">Tutup Detail</button>
        </div>
        <button onclick="closeInovasi()" style="position:absolute; top:20px; right:20px; color:var(--gray-400); font-size:1.2rem;"><i class="fas fa-times"></i></button>
    </div>
</div>

<style>
@keyframes modalPop {
    from { opacity:0; transform:scale(0.9); }
    to { opacity:1; transform:scale(1); }
}
</style>

<script>
function showInovasi(title, desc) {
    document.getElementById('modalTitle').innerText = title;
    document.getElementById('modalDesc').innerText = desc;
    document.getElementById('modalInovasi').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeInovasi() {
    document.getElementById('modalInovasi').style.display = 'none';
    document.body.style.overflow = 'auto';
}
// Close on outside click
window.onclick = function(event) {
    let modal = document.getElementById('modalInovasi');
    if (event.target == modal) closeInovasi();
}
</script>

<?php include 'include/footer.php'; ?>
