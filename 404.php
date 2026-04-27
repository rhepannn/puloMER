<?php
require_once 'include/config.php';
$pageTitle = 'Halaman Tidak Ditemukan';
include 'include/header.php';
?>
<div class="page-hero" style="min-height:60vh;display:flex;align-items:center;">
    <div class="container page-hero-content" style="text-align:center;">
        <div style="font-size:8rem;font-weight:900;color:rgba(255,255,255,0.25);line-height:1;margin-bottom:16px;">404</div>
        <h1 style="font-size:2rem;margin-bottom:12px;"><i class="fas fa-map-marker-alt"></i> Halaman Tidak Ditemukan</h1>
        <p style="font-size:1.05rem;opacity:0.85;margin-bottom:28px;">
            Maaf, halaman yang Anda cari tidak tersedia atau telah dipindahkan.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="<?= SITE_URL ?>/" class="btn btn-white">
                <i class="fas fa-home"></i> Kembali ke Beranda
            </a>
            <a href="<?= SITE_URL ?>/berita.php" class="btn btn-outline-white">
                <i class="fas fa-newspaper"></i> Lihat Berita
            </a>
        </div>
    </div>
</div>
<?php include 'include/footer.php'; ?>
