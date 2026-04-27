<?php
require_once 'include/config.php';
require_once 'include/functions.php';
$pageTitle = 'Profil Organisasi';

// Ambil semua settings
$S = getAllSettings($conn);

$profilHeroBg  = getImg($S['profil_hero_image'] ?? '', 'settings', 'foto-profil.jpg');
$profilFoto    = getImg($S['profil_tentang_image'] ?? '', 'settings', 'foto-profil.jpg');

// Org photos
for ($i=1; $i<=4; $i++) $orgKasiFoto[$i] = getImg($S['org_kasi_'.$i.'_foto'] ?? '', 'settings', '');
$orgCamatFoto  = getImg($S['org_camat_foto'] ?? '', 'settings', '');
$orgSekcamFoto = getImg($S['org_sekcam_foto'] ?? '', 'settings', '');

include 'include/header.php';
?>

<!-- HERO SECTION -->
<section class="relative w-full min-h-[60vh] flex items-center justify-center overflow-hidden bg-darkblue">
    <div class="absolute inset-0 w-full h-full opacity-60">
        <img src="<?= $profilHeroBg ?>" alt="Profil PKK" class="w-full h-full object-cover object-center mix-blend-overlay">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-darkblue_alt via-darkblue/80 to-transparent"></div>
    
    <div class="container mx-auto px-4 md:px-6 relative z-10 pt-20 pb-16 text-center">
        <div class="max-w-3xl mx-auto fade-in-up">
            <span class="inline-block py-1.5 px-4 rounded-full bg-accent/20 text-white border border-accent/40 text-xs font-semibold tracking-wider uppercase mb-6 backdrop-blur-md">
                Profil Kami
            </span>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                <?= $S['profil_hero_title'] ?? 'Mewujudkan Masyarakat <br><span class="text-accent">Maju & Sejahtera</span>' ?>
            </h1>
            <p class="text-lg text-gray-300 leading-relaxed max-w-2xl mx-auto font-light mb-8">
                <?= e($S['profil_hero_subtitle'] ?? 'Mengenal lebih dekat visi, misi, dan struktur organisasi TP PKK Kecamatan Pulomerak dalam melayani masyarakat Kota Cilegon.') ?>
            </p>
            <div class="flex justify-center gap-4">
                <a href="#visi-misi" class="bg-accent hover:bg-white hover:text-darkblue text-white px-6 py-3 rounded-lg font-medium transition-colors duration-300 shadow-lg shadow-accent/20 flex items-center gap-2 text-sm">
                    <i class="fas fa-bullseye"></i> Visi & Misi
                </a>
                <a href="#struktur" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-6 py-3 rounded-lg font-medium transition-colors duration-300 backdrop-blur-sm flex items-center gap-2 text-sm">
                    <i class="fas fa-sitemap"></i> Struktur
                </a>
            </div>
        </div>
    </div>
</section>

<!-- TENTANG SECTION -->
<section class="py-16 md:py-24 bg-softgray overflow-hidden">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            
            <div class="relative scroll-reveal">
                <!-- Human asymmetry & skewing -->
                <div class="absolute -inset-4 bg-accent/10 rounded-3xl transform rotate-3 human-skew-alt"></div>
                <div class="relative rounded-2xl overflow-hidden shadow-xl aspect-[4/3] human-skew">
                    <img src="<?= $profilFoto ?>" alt="Tentang Kami" class="w-full h-full object-cover unskew scale-105" onerror="this.src='https://placehold.co/800x600/003087/ffffff?text=TP+PKK'">
                    <div class="absolute inset-0 bg-darkblue/10 unskew"></div>
                </div>
            </div>

            <div class="scroll-reveal delay-100">
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-8 h-1 bg-accent rounded-full"></span>
                    <span class="text-accent font-semibold text-sm uppercase tracking-wider">Tentang Kami</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-darkblue_alt mb-6 leading-tight">
                    TP PKK <span class="text-accent">Pulomerak</span>
                </h2>
                
                <div class="space-y-4 text-gray-600 text-sm md:text-base leading-relaxed text-justify mb-8">
                    <p><?= e($S['profil_tentang_1'] ?? 'Pemberdayaan dan Kesejahteraan Keluarga (PKK) merupakan gerakan nasional dalam pembangunan masyarakat yang tumbuh dari bawah yang pengelolaannya dari, oleh dan untuk masyarakat.') ?></p>
                    <p><?= e($S['profil_tentang_2'] ?? 'Tim Penggerak PKK Kecamatan Pulomerak senantiasa berkomitmen untuk meningkatkan kesejahteraan keluarga melalui berbagai program pokok yang berkesinambungan.') ?></p>
                    <?php if(!empty($S['profil_tentang_3'])): ?>
                    <p><?= e($S['profil_tentang_3']) ?></p>
                    <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 flex items-center gap-4 shadow-sm group hover:border-accent transition-colors">
                        <div class="w-10 h-10 rounded-full bg-softgray flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-white transition-colors">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Lokasi</div>
                            <div class="text-darkblue_alt font-semibold text-sm"><?= e($S['profil_lokasi'] ?? 'Kec. Pulomerak') ?></div>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100 flex items-center gap-4 shadow-sm group hover:border-accent transition-colors">
                        <div class="w-10 h-10 rounded-full bg-softgray flex items-center justify-center text-accent group-hover:bg-accent group-hover:text-white transition-colors">
                            <i class="fas fa-expand-arrows-alt"></i>
                        </div>
                        <div>
                            <div class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Luas Wilayah</div>
                            <div class="text-darkblue_alt font-semibold text-sm"><?= e($S['profil_luas'] ?? '±3,2 km²') ?></div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- VISI & MISI -->
<section id="visi-misi" class="py-16 md:py-24 bg-white relative">
    <div class="absolute right-0 top-0 w-1/3 h-full bg-softgray skew-x-12 transform origin-top-right opacity-50 hidden lg:block"></div>
    <div class="container mx-auto px-4 md:px-6 relative z-10">
        
        <div class="text-center max-w-2xl mx-auto mb-16 scroll-reveal">
            <span class="text-accent font-semibold tracking-wider text-xs md:text-sm uppercase bg-accent/10 px-3 py-1 rounded-full">Arah & Tujuan</span>
            <h2 class="text-3xl md:text-4xl font-bold text-darkblue_alt mt-4 mb-4">Visi & Misi</h2>
            <div class="w-16 h-1 bg-accent mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 md:gap-12">
            <!-- Visi -->
            <div class="lg:col-span-5 scroll-reveal">
                <div class="bg-darkblue_alt text-white p-8 md:p-12 rounded-3xl shadow-xl h-full flex flex-col justify-center relative overflow-hidden">
                    <i class="fas fa-eye absolute -top-10 -right-10 text-9xl text-white/5 transform -rotate-12"></i>
                    <div class="w-16 h-16 bg-accent/20 rounded-2xl flex items-center justify-center text-accent text-2xl mb-6 backdrop-blur-sm">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Visi</h3>
                    <p class="text-xl md:text-2xl font-light leading-relaxed">
                        "<?= e($S['profil_visi'] ?? 'Terwujudnya keluarga yang beriman dan bertaqwa kepada Tuhan Yang Maha Esa, berakhlak mulia dan berbudi luhur, sehat sejahtera, maju dan mandiri.') ?>"
                    </p>
                </div>
            </div>

            <!-- Misi -->
            <div class="lg:col-span-7 scroll-reveal delay-100">
                <div class="bg-softgray border border-gray-100 p-8 md:p-12 rounded-3xl h-full">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-accent shadow-sm">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-darkblue">Misi</h3>
                    </div>
                    
                    <ul class="space-y-4">
                        <?php
                        $misiItems = array_filter(explode("\n", $S['profil_misi'] ?? "Meningkatkan pembentukan karakter keluarga melalui penghayatan.\nMeningkatkan pendidikan dan ekonomi keluarga.\nMeningkatkan ketahanan keluarga melalui pemenuhan pangan, sandang.\nMeningkatkan derajat kesehatan keluarga, kelestarian lingkungan hidup."));
                        $mCount = 1;
                        foreach ($misiItems as $misi):
                            $misi = trim($misi);
                            if ($misi):
                        ?>
                            <li class="flex items-start gap-4 bg-white p-4 rounded-xl border border-gray-50 shadow-sm">
                                <div class="w-8 h-8 flex-shrink-0 bg-accent text-white rounded-full flex items-center justify-center font-bold text-sm">
                                    <?= $mCount++ ?>
                                </div>
                                <p class="text-gray-600 mt-1 text-sm md:text-base leading-relaxed"><?= e($misi) ?></p>
                            </li>
                        <?php endif; endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- STRUKTUR ORGANISASI -->
<section id="struktur" class="py-16 md:py-24 bg-softgray">
    <div class="container mx-auto px-4 md:px-6">
        <div class="text-center max-w-2xl mx-auto mb-16 scroll-reveal">
            <span class="text-accent font-semibold tracking-wider text-xs md:text-sm uppercase bg-accent/10 px-3 py-1 rounded-full">Struktur Kepengurusan</span>
            <h2 class="text-3xl md:text-4xl font-bold text-darkblue_alt mt-4 mb-4">Tim Penggerak PKK</h2>
            <p class="text-gray-600">Susunan pengurus inti TP PKK Kecamatan Pulomerak.</p>
            <div class="w-16 h-1 bg-accent mx-auto rounded-full mt-4"></div>
        </div>

        <div class="flex flex-col items-center max-w-5xl mx-auto">
            
            <!-- Ketua/Camat -->
            <div class="scroll-reveal mb-8">
                <div class="bg-white w-64 p-6 rounded-2xl shadow-lg border-b-4 border-accent text-center relative z-10 mx-auto">
                    <div class="w-24 h-24 mx-auto bg-softgray rounded-full overflow-hidden mb-4 border-4 border-white shadow-md">
                        <?php if ($orgCamatFoto): ?>
                            <img src="<?= $orgCamatFoto ?>" alt="<?= e($S['org_camat_nama'] ?? '') ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-user-tie text-3xl"></i></div>
                        <?php endif; ?>
                    </div>
                    <h4 class="font-bold text-darkblue_alt text-lg leading-tight mb-1"><?= e($S['org_camat_nama'] ?? 'Hj. Ketua PKK') ?></h4>
                    <p class="text-xs font-semibold text-accent uppercase tracking-wider"><?= e($S['org_camat_jabatan'] ?? 'Ketua TP PKK') ?></p>
                </div>
            </div>

            <!-- Connector Line -->
            <div class="w-0.5 h-12 bg-gray-300 scroll-reveal"></div>
            
            <!-- Horizontal Line -->
            <div class="w-full max-w-3xl h-0.5 bg-gray-300 relative scroll-reveal">
                <div class="absolute left-0 top-0 w-0.5 h-6 bg-gray-300"></div>
                <div class="absolute right-0 top-0 w-0.5 h-6 bg-gray-300"></div>
                <div class="absolute left-1/3 top-0 w-0.5 h-6 bg-gray-300 hidden md:block"></div>
                <div class="absolute right-1/3 top-0 w-0.5 h-6 bg-gray-300 hidden md:block"></div>
            </div>

            <!-- Anggota / Pokja / Kasi -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 w-full mt-8">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                <div class="scroll-reveal delay-<?= $i*100 ?>">
                    <div class="bg-white p-4 md:p-5 rounded-2xl shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-transform duration-300 hover:shadow-md h-full flex flex-col items-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 bg-softgray rounded-full overflow-hidden mb-3 md:mb-4 border-2 border-gray-50">
                            <?php if (!empty($orgKasiFoto[$i])): ?>
                                <img src="<?= $orgKasiFoto[$i] ?>" alt="<?= e($S['org_kasi_'.$i.'_nama'] ?? '') ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400"><i class="fas fa-user text-xl md:text-2xl"></i></div>
                            <?php endif; ?>
                        </div>
                        <h4 class="font-bold text-darkblue_alt text-xs md:text-sm leading-tight mb-1"><?= e($S["org_kasi_{$i}_nama"] ?? "Anggota {$i}") ?></h4>
                        <p class="text-[9px] md:text-[10px] font-semibold text-accent uppercase tracking-wider"><?= e($S["org_kasi_{$i}_jabatan"] ?? "Pokja {$i}") ?></p>
                    </div>
                </div>
                <?php endfor; ?>
            </div>

        </div>
    </div>
</section>

<!-- INFO BATAS WILAYAH -->
<section class="py-12 md:py-16 bg-white border-t border-gray-100">
    <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
            <?php
            $batasArr = [
                ['label' => 'Utara',   'key' => 'batas_utara', 'icon' => 'fa-arrow-up'],
                ['label' => 'Selatan', 'key' => 'batas_selatan', 'icon' => 'fa-arrow-down'],
                ['label' => 'Barat',   'key' => 'batas_barat', 'icon' => 'fa-arrow-left'],
                ['label' => 'Timur',   'key' => 'batas_timur', 'icon' => 'fa-arrow-right'],
            ];
            foreach ($batasArr as $idx => $batas):
            ?>
            <div class="bg-softgray p-6 rounded-2xl text-center scroll-reveal delay-<?= ($idx+1)*100 ?>">
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto text-accent mb-4 shadow-sm">
                    <i class="fas <?= $batas['icon'] ?> text-lg"></i>
                </div>
                <h3 class="text-xs font-bold text-darkblue uppercase tracking-widest mb-2">Batas <?= $batas['label'] ?></h3>
                <p class="text-gray-500 text-sm font-medium"><?= e($S[$batas['key']] ?? 'Wilayah ' . $batas['label']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'include/footer.php'; ?>
