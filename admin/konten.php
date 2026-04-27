<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$pageTitle = 'Editor Konten';

// Master grouping for tabs
$tabGroups = [
    'Banner & Hero' => [
        'group_banner' => ['label' => 'Semua Banner', 'icon' => 'fas fa-images', 'subgroups' => ['beranda_hero', 'berita_hero', 'kegiatan_hero', 'laporan_hero', 'profil_hero']],
    ],
    'Beranda' => [
        'beranda_stats'   => ['label' => 'Statistik Strip',   'icon' => 'fas fa-chart-bar'],
        'beranda_pkk'     => ['label' => 'Info PKK',          'icon' => 'fas fa-info-circle'],
        'beranda_counter' => ['label' => 'Counter Statistik', 'icon' => 'fas fa-sort-numeric-up'],
    ],
    'Halaman Profil' => [
        'profil_tentang'  => ['label' => 'Tentang Kecamatan',  'icon' => 'fas fa-landmark'],
        'profil_visimisi' => ['label' => 'Visi & Misi',        'icon' => 'fas fa-bullseye'],
        'profil_struktur' => ['label' => 'Struktur Organisasi','icon' => 'fas fa-sitemap'],
        'profil_batas'    => ['label' => 'Batas Wilayah',      'icon' => 'fas fa-map'],
    ],
    'Sistem' => [
        'footer' => ['label' => 'Footer & Global', 'icon' => 'fas fa-globe'],
    ],
];

$activeTab = $_GET['tab'] ?? 'group_banner';

// Find info for active tab
$activeInfo = null;
foreach ($tabGroups as $cat => $tabs) {
    if (isset($tabs[$activeTab])) {
        $activeInfo = $tabs[$activeTab];
        break;
    }
}

// Logic for fetching settings
if (isset($activeInfo['subgroups'])) {
    $placeholders = implode(',', array_fill(0, count($activeInfo['subgroups']), '?'));
    // Gunakan setting_key sebagai pengganti id untuk pengurutan
    $stmt = $conn->prepare("SELECT * FROM site_settings WHERE setting_group IN ($placeholders) ORDER BY FIELD(setting_group, $placeholders), setting_key");
    
    // Bind dynamic params (dua kali lipat karena ada placeholders di IN dan FIELD)
    $allParams = array_merge($activeInfo['subgroups'], $activeInfo['subgroups']);
    $types = str_repeat('s', count($allParams));
    $stmt->bind_param($types, ...$allParams);
    
    $stmt->execute();
    $rawSettings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} else {
    $rawSettings = getSettingsByGroup($conn, $activeTab);
}

// Group settings by their original group for display
$groupedSettings = [];
foreach ($rawSettings as $s) {
    $groupedSettings[$s['setting_group']][] = $s;
}

$uploadDir = __DIR__ . '/../uploads/settings/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $group = $_POST['active_tab'] ?? '';
    $keys  = $_POST['keys'] ?? [];
    $vals  = $_POST['vals'] ?? [];
    $types = $_POST['field_types'] ?? [];

    foreach ($keys as $i => $key) {
        $fieldType = $types[$i] ?? 'text';
        if ($fieldType === 'image') {
            $fileKey = 'file_' . $key;
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $result = uploadFile($_FILES[$fileKey], $uploadDir);
                if (is_string($result) && !isset($result['error'])) {
                    $oldVal = getSetting($conn, $key);
                    if ($oldVal && file_exists($uploadDir . $oldVal)) @unlink($uploadDir . $oldVal);
                    setSetting($conn, $key, $result);
                }
            }
            if (isset($_POST['remove_' . $key]) && $_POST['remove_' . $key] === '1') {
                $oldVal = getSetting($conn, $key);
                if ($oldVal && file_exists($uploadDir . $oldVal)) @unlink($uploadDir . $oldVal);
                setSetting($conn, $key, '');
            }
        } else {
            setSetting($conn, $key, $vals[$i] ?? '');
        }
    }

    setFlash('success', 'Konten berhasil diperbarui!');
    header('Location: konten.php?tab=' . urlencode($group));
    exit;
}

include 'header.php';
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
    <div>
        <div class="text-[10px] font-bold text-accent uppercase tracking-widest mb-1">Editor Visual</div>
        <h2 class="text-2xl md:text-3xl font-bold text-darkblue_alt">Manajemen Konten</h2>
        <p class="text-gray-400 text-sm mt-1">Sesuaikan teks dan gambar pada website secara real-time</p>
    </div>
    <a href="<?= SITE_URL ?>/" target="_blank"
       class="flex items-center gap-2 bg-white border border-gray-200 text-darkblue_alt font-bold text-sm px-4 py-2.5 rounded-xl hover:bg-softgray transition-all shadow-sm self-start sm:self-auto">
        <i class="fas fa-external-link-alt text-xs text-accent"></i> Lihat Website
    </a>
</div>

<!-- Category-based Tab Navigation -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-start">
    
    <!-- Sidebar Navigation -->
    <div class="space-y-6">
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-4 overflow-hidden">
            <div class="space-y-6">
                <?php foreach ($tabGroups as $catName => $tabs): ?>
                <div>
                    <div class="px-3 text-[9px] font-bold text-gray-300 uppercase tracking-[0.2em] mb-3"><?= $catName ?></div>
                    <div class="space-y-1">
                        <?php foreach ($tabs as $key => $info): ?>
                        <?php $isActive = ($activeTab === $key); ?>
                        <a href="konten.php?tab=<?= $key ?>"
                           class="flex items-center gap-3 px-4 py-3 rounded-2xl text-xs font-bold transition-all <?= $isActive ? 'bg-accent text-white shadow-lg shadow-accent/20' : 'text-gray-500 hover:bg-softgray hover:text-darkblue_alt' ?>">
                            <i class="<?= $info['icon'] ?> text-sm <?= $isActive ? 'text-white' : 'text-accent' ?>"></i>
                            <?= $info['label'] ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Helpful Tip -->
        <div class="bg-darkblue_alt rounded-3xl p-6 text-white overflow-hidden relative">
            <i class="fas fa-lightbulb absolute -right-4 -bottom-4 text-7xl text-white/5 rotate-12"></i>
            <h4 class="text-xs font-bold mb-2 flex items-center gap-2"><i class="fas fa-info-circle text-accent"></i> Tips Editor</h4>
            <p class="text-[10px] text-gray-400 leading-relaxed">
                Perubahan pada editor ini akan langsung tampil di website utama. Pastikan ukuran gambar yang diupload tidak terlalu besar untuk performa maksimal.
            </p>
        </div>
    </div>

    <!-- Editor Form Area -->
    <div class="md:col-span-3">
        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <input type="hidden" name="active_tab" value="<?= e($activeTab) ?>">

            <?php if (empty($groupedSettings)): ?>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-20 text-center">
                    <div class="w-20 h-20 rounded-full bg-softgray flex items-center justify-center text-gray-300 mx-auto mb-6">
                        <i class="fas fa-database text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-darkblue_alt mb-2">Data Belum Ada</h3>
                    <p class="text-gray-400 text-sm">Grup ini belum memiliki pengaturan yang terdaftar.</p>
                </div>
            <?php else: ?>
                <?php foreach ($groupedSettings as $groupKey => $settings): ?>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <!-- Section Header -->
                    <div class="bg-softgray/50 px-8 py-5 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-accent shadow-sm">
                                <i class="fas fa-folder-open text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-darkblue_alt uppercase tracking-wider text-xs">
                                    <?php 
                                    // Human readable group name
                                    $groupLabel = str_replace(['_', 'profil', 'beranda'], ['', 'Profil', 'Beranda'], $groupKey);
                                    echo ucwords($groupLabel);
                                    ?>
                                </h3>
                                <p class="text-[10px] text-gray-400 font-medium">Grup Pengaturan: <?= e($groupKey) ?></p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-gray-400 bg-white px-3 py-1 rounded-full border border-gray-50"><?= count($settings) ?> Field</span>
                    </div>

                    <div class="p-8 space-y-8">
                        <?php foreach ($settings as $s): ?>
                        <div class="group">
                            <div class="flex flex-col md:flex-row md:items-start gap-4">
                                <!-- Label & Key -->
                                <div class="md:w-1/3">
                                    <label class="block mb-1">
                                        <span class="text-sm font-bold text-darkblue_alt"><?= e($s['label'] ?? $s['setting_key']) ?></span>
                                    </label>
                                    <code class="text-[9px] font-mono text-gray-400 bg-softgray px-1.5 py-0.5 rounded">#<?= e($s['setting_key']) ?></code>
                                </div>

                                <!-- Input -->
                                <div class="md:w-2/3">
                                    <input type="hidden" name="keys[]" value="<?= e($s['setting_key']) ?>">
                                    <input type="hidden" name="field_types[]" value="<?= e($s['field_type']) ?>">

                                    <?php if ($s['field_type'] === 'image'): ?>
                                        <div class="space-y-4">
                                            <?php if ($s['setting_value']): ?>
                                            <div class="relative w-full max-w-sm aspect-video rounded-2xl overflow-hidden border border-gray-100 shadow-inner group/img">
                                                <img src="<?= SITE_URL ?>/uploads/settings/<?= e($s['setting_value']) ?>" 
                                                     class="w-full h-full object-cover">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity flex items-center justify-center">
                                                    <label class="bg-white text-darkblue_alt text-[10px] font-bold px-4 py-2 rounded-xl cursor-pointer hover:bg-accent hover:text-white transition-all shadow-xl">
                                                        <input type="checkbox" name="remove_<?= e($s['setting_key']) ?>" value="1" class="hidden">
                                                        <i class="fas fa-trash-alt mr-2"></i> Hapus Gambar
                                                    </label>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div class="flex items-center gap-3">
                                                <label class="flex items-center gap-3 bg-softgray border-2 border-dashed border-gray-200 hover:border-accent group-hover:bg-white text-gray-500 hover:text-accent px-5 py-4 rounded-2xl cursor-pointer transition-all w-full text-center justify-center">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                    <span class="text-xs font-bold uppercase tracking-widest"><?= $s['setting_value'] ? 'Ganti Gambar' : 'Pilih File Gambar' ?></span>
                                                    <input type="file" name="file_<?= e($s['setting_key']) ?>" accept="image/*" class="hidden" onchange="previewImg(this, '<?= e($s['setting_key']) ?>')">
                                                </label>
                                            </div>
                                            <div id="preview_<?= e($s['setting_key']) ?>" class="hidden">
                                                <div class="relative w-full max-w-sm aspect-video rounded-2xl overflow-hidden border-2 border-accent shadow-lg">
                                                    <img id="previmg_<?= e($s['setting_key']) ?>" src="" class="w-full h-full object-cover">
                                                    <div class="absolute top-2 left-2 bg-accent text-white text-[9px] font-bold px-2 py-1 rounded-lg">Preview Baru</div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php elseif ($s['field_type'] === 'textarea'): ?>
                                        <textarea name="vals[]" rows="4" 
                                                  class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent focus:ring-4 focus:ring-accent/10 transition-all"><?= e($s['setting_value']) ?></textarea>
                                        <?php if(strpos($s['setting_key'], '_title') !== false): ?>
                                            <p class="text-[9px] text-gray-400 mt-2"><i class="fas fa-info-circle mr-1"></i>Gunakan <code>&lt;br&gt;</code> untuk baris baru, <code>&lt;span&gt;teks&lt;/span&gt;</code> untuk warna aksen.</p>
                                        <?php endif; ?>

                                    <?php elseif ($s['field_type'] === 'number'): ?>
                                        <input type="number" name="vals[]" value="<?= e($s['setting_value']) ?>"
                                               class="w-full max-w-xs bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm font-bold focus:outline-none focus:border-accent transition-all">

                                    <?php else: ?>
                                        <input type="text" name="vals[]" value="<?= e($s['setting_value']) ?>"
                                               class="w-full bg-softgray border border-gray-200 text-darkblue_alt rounded-2xl px-5 py-4 text-sm focus:outline-none focus:border-accent transition-all">
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Form Actions -->
                <div class="flex items-center gap-4 bg-white/50 backdrop-blur-sm sticky bottom-8 p-4 rounded-3xl border border-white shadow-2xl z-20">
                    <button type="submit" class="flex-1 flex items-center justify-center gap-3 bg-accent hover:bg-darkblue text-white font-black px-8 py-5 rounded-2xl transition-all shadow-xl shadow-accent/20 uppercase tracking-widest text-sm">
                        <i class="fas fa-save"></i> Simpan Semua Perubahan
                    </button>
                    <a href="konten.php?tab=<?= e($activeTab) ?>" class="bg-white hover:bg-gray-50 text-gray-500 font-bold px-8 py-5 rounded-2xl border border-gray-200 transition-all text-sm uppercase tracking-widest">
                        Reset
                    </a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="h-20 lg:h-0"></div>

<script>
function previewImg(input, key) {
    const preview = document.getElementById('preview_' + key);
    const img     = document.getElementById('previmg_' + key);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { 
            img.src = e.target.result; 
            preview.classList.remove('hidden'); 
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'footer.php'; ?>
