<?php
require_once 'include/config.php';
require_once 'include/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) redirect(SITE_URL . '/index.php'); // Redirect ke beranda jika tidak ada slug

$stmt = $conn->prepare("SELECT * FROM bidang WHERE slug = ?");
$stmt->bind_param('s', $slug);
$stmt->execute();
$bidang = $stmt->get_result()->fetch_assoc();

if (!$bidang) redirect(SITE_URL . '/index.php');

$pageTitle = $bidang['nama'];

// Ambil pengurus/anggota
$stmt = $conn->prepare("SELECT * FROM anggota_bidang WHERE bidang_id = ? ORDER BY urutan ASC");
$stmt->bind_param('i', $bidang['id']);
$stmt->execute();
$anggota = $stmt->get_result();

$currentPage = 'bidang-detail';
include 'include/header.php';

$hero_bg = getImg($bidang['gambar'], 'bidang');
?>

<!-- HERO SECTION -->
<section class="relative w-full py-24 md:py-32 flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-40">
        <img src="<?= $hero_bg ?>" alt="<?= e($bidang['nama']) ?>" class="w-full h-full object-cover object-center mix-blend-overlay blur-sm scale-105">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-softgray via-darkblue/90 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 text-center fade-in-up">
        <span class="inline-block py-1 px-4 rounded-full bg-accent/20 text-white border border-accent/40 text-xs font-semibold tracking-wider uppercase mb-6 backdrop-blur-md">
            Program Kerja Pokok
        </span>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">
            <?= e($bidang['nama']) ?>
        </h1>
        <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mt-6"></div>
    </div>
</section>

<section class="py-16 md:py-24 bg-softgray relative -mt-6">
    <div class="container mx-auto px-4 md:px-6">
        
        <div class="flex flex-col lg:flex-row gap-12 xl:gap-16">
            
            <!-- PROFIL BIDANG & PRESTASI -->
            <div class="lg:w-7/12">
                <div class="bg-white rounded-3xl p-8 md:p-10 shadow-sm border border-gray-100 scroll-reveal">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 text-accent flex items-center justify-center">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-darkblue">Profil <span class="text-accent">Bidang</span></h2>
                    </div>
                    
                    <div class="prose prose-lg max-w-none text-gray-600 leading-relaxed text-justify mb-8">
                        <p><?= nl2br(e($bidang['deskripsi'])) ?></p>
                    </div>
                    
                    <?php if ($bidang['prestasi']): ?>
                    <div class="mt-8 relative scroll-reveal delay-100">
                        <div class="absolute -inset-2 bg-gradient-to-r from-accent/20 to-transparent rounded-2xl transform -rotate-1 opacity-50"></div>
                        <div class="relative bg-white p-6 rounded-xl border border-gray-100 flex items-start sm:items-center gap-5 shadow-sm hover:shadow-md transition-shadow">
                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-full flex items-center justify-center text-white text-2xl flex-shrink-0 shadow-lg shadow-yellow-500/30">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pencapaian Utama</h4>
                                <p class="font-bold text-darkblue text-lg leading-snug"><?= e($bidang['prestasi']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- PROGRAM UNGGULAN -->
                <?php if ($bidang['program_unggulan']): ?>
                <div class="mt-14 scroll-reveal">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-1.5 h-10 bg-accent rounded-full"></div>
                        <h2 class="text-3xl font-extrabold text-darkblue tracking-tight">Program <span class="text-accent">Unggulan</span></h2>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php 
                        $programs = explode("\n", str_replace("\r", "", $bidang['program_unggulan']));
                        $pCount = 1;
                        foreach ($programs as $prog): 
                            if (empty(trim($prog))) continue;
                            $cleanProg = preg_replace('/^\d+[\.\)]\s+/', '', trim($prog));
                        ?>
                        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-accent/30 transition-all duration-500 group flex items-start gap-5 relative overflow-hidden">
                            <!-- Soft Accent Background -->
                            <div class="absolute top-0 right-0 w-32 h-32 bg-accent/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                            
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-softgray text-accent font-black flex items-center justify-center text-xl group-hover:bg-accent group-hover:text-white transition-all duration-500 shadow-inner relative z-10">
                                <?= sprintf('%02d', $pCount++) ?>
                            </div>
                            
                            <div class="flex-grow pt-1 relative z-10">
                                <p class="text-darkblue_alt font-bold leading-relaxed text-sm md:text-base transition-colors group-hover:text-accent"><?= e($cleanProg) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- STRUKTUR PENGURUS -->
            <div class="lg:w-5/12">
                <div class="bg-darkblue/95 p-8 md:p-10 rounded-3xl shadow-2xl border border-white/10 relative overflow-hidden scroll-reveal delay-200">
                    <!-- High-Intensity Accent Blurs -->
                    <div class="absolute -right-20 -top-20 w-72 h-72 bg-accent/30 rounded-full blur-[100px] animate-pulse"></div>
                    <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-blue-600/20 rounded-full blur-[100px]"></div>
 
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-8 pb-6 border-b border-white/10">
                            <div>
                                <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Mari Mengenal Kami</div>
                                <h2 class="text-2xl md:text-3xl font-bold text-white">Struktur Pengurus</h2>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-white backdrop-blur-md border border-white/20">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                        </div>
 
                        <?php if ($anggota->num_rows === 0): ?>
                            <div class="text-center py-10 px-4 bg-white/5 rounded-2xl border border-white/10 backdrop-blur-sm shadow-inner">
                                <i class="fas fa-user-slash text-3xl text-white/20 mb-3"></i>
                                <p class="text-white/40 text-sm">Data struktur pengurus belum tersedia.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php while ($a = $anggota->fetch_assoc()): ?>
                                <div class="bg-white/5 hover:bg-white/10 p-4 rounded-2xl border border-white/10 transition-all duration-300 backdrop-blur-md flex items-center gap-4 group shadow-lg hover:-translate-y-0.5">
                                    <div class="w-14 h-14 rounded-xl overflow-hidden bg-white/10 flex-shrink-0 border-2 border-white/20 group-hover:border-accent transition-colors shadow-md">
                                        <?php if ($a['foto']): ?>
                                            <img src="<?= getImg($a['foto'], 'bidang') ?>" alt="<?= e($a['nama']) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <div class="w-full h-full flex items-center justify-center text-white/30"><i class="fas fa-user"></i></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow min-w-0">
                                        <h4 class="font-bold text-white text-base md:text-lg mb-0.5 leading-snug truncate group-hover:text-accent transition-colors"><?= e($a['nama']) ?></h4>
                                        <div class="text-accent font-bold text-[10px] tracking-wider uppercase">
                                            <?= e($a['jabatan']) ?>
                                        </div>
                                    </div>
                                    <?php if ($a['no_hp']): ?>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $a['no_hp']) ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-xl bg-white/10 text-white flex items-center justify-center hover:bg-accent hover:text-white transition-all shadow-md flex-shrink-0" title="Hubungi">
                                            <i class="fab fa-whatsapp text-lg"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Custom Typography Styles just in case -->
<style>
.prose p { margin-bottom: 1.25em; }
.prose ul { list-style-type: disc; padding-left: 1.625em; margin-bottom: 1.25em; }
.prose li { margin-bottom: 0.5em; }
</style>

<?php include 'include/footer.php'; ?>
