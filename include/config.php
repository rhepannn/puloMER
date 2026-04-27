<?php
// ============================================================
// KONFIGURASI - Portal Informasi Kecamatan Pulomerak
// ============================================================

// ── DATABASE ─────────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pulomerakk');
define('DB_PORT', '3307');

// ── SITE URL ─────────────────────────────────────────────────
$__p = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$__h = $_SERVER['HTTP_HOST'] ?? 'localhost';
$__r = str_replace(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace('\\', '/', __DIR__));
$__r = rtrim(dirname($__r), '/');
define('SITE_URL', $__p . '://' . $__h . ($__r ? $__r : ''));
define('SITE_NAME', 'Portal Informasi Kecamatan Pulomerak');
define('SITE_DESC', 'Pusat Informasi Masyarakat Kecamatan Pulomerak, Kota Cilegon');
unset($__p);


// ── KONEKSI DATABASE ─────────────────────────────────────────
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;">
        <h2 style="color:#c0392b;">Koneksi Database Gagal</h2>
        <p>' . htmlspecialchars($conn->connect_error) . '</p>
    </div>');
}
$conn->set_charset('utf8mb4');
date_default_timezone_set('Asia/Jakarta');

// ── SESSION ──────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================================
// AUTO-SETUP: Pastikan tabel & kolom selalu ada (silent)
// Tidak perlu jalankan script setup terpisah.
// ============================================================
_ensureSchema($conn);

function _ensureSchema($conn) {
    // 1. Buat tabel site_settings jika belum ada
    $conn->query("
        CREATE TABLE IF NOT EXISTS `site_settings` (
            `setting_key`   VARCHAR(100) NOT NULL,
            `setting_value` TEXT DEFAULT NULL,
            `setting_group` VARCHAR(50)  DEFAULT 'general',
            `label`         VARCHAR(200) DEFAULT NULL,
            `field_type`    VARCHAR(20)  DEFAULT 'text',
            `updated_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`setting_key`),
            KEY `idx_group` (`setting_group`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    // 2. Migrasi kolom jika tabel sudah ada versi lama
    $res  = $conn->query("SHOW COLUMNS FROM `site_settings`");
    $cols = [];
    while ($r = $res->fetch_assoc()) $cols[] = $r['Field'];

    if (!in_array('label',      $cols)) $conn->query("ALTER TABLE `site_settings` ADD `label`      VARCHAR(200) DEFAULT NULL  AFTER `setting_group`");
    if (!in_array('field_type', $cols)) $conn->query("ALTER TABLE `site_settings` ADD `field_type` VARCHAR(20)  DEFAULT 'text' AFTER `label`");
    if (!in_array('updated_at', $cols)) $conn->query("ALTER TABLE `site_settings` ADD `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `field_type`");

    // 3. Kolom tambahan di tabel kelurahan
    $tables = $conn->query("SHOW TABLES LIKE 'kelurahan'");
    if ($tables && $tables->num_rows > 0) {
        $res = $conn->query("SHOW COLUMNS FROM `kelurahan` ");
        $cols = [];
        while ($r = $res->fetch_assoc()) $cols[] = $r['Field'];

        if (!in_array('foto',           $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `foto`           VARCHAR(255) DEFAULT NULL AFTER `id` ");
        if (!in_array('ketua_pkk',      $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `ketua_pkk`      VARCHAR(255) DEFAULT NULL AFTER `nama` ");
        if (!in_array('penduduk_l',     $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `penduduk_l`     INT(11)      DEFAULT 0    AFTER `penduduk` ");
        if (!in_array('penduduk_p',     $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `penduduk_p`     INT(11)      DEFAULT 0    AFTER `penduduk_l` ");
        if (!in_array('inovasi',        $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `inovasi`        TEXT         DEFAULT NULL AFTER `deskripsi` ");
        if (!in_array('jumlah_link',    $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `jumlah_link`    INT(11)      DEFAULT 0    AFTER `jumlah_rt` ");
        if (!in_array('jumlah_krt',     $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `jumlah_krt`     INT(11)      DEFAULT 0    AFTER `jumlah_link` ");
        if (!in_array('jumlah_kk',      $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `jumlah_kk`      INT(11)      DEFAULT 0    AFTER `jumlah_krt` ");
        if (!in_array('dasa_wisma',     $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `dasa_wisma`     INT(11)      DEFAULT 0    AFTER `jumlah_kk` ");
        if (!in_array('ibu_hamil',      $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `ibu_hamil`      INT(11)      DEFAULT 0    AFTER `dasa_wisma` ");
        if (!in_array('ibu_menyusui',   $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `ibu_menyusui`   INT(11)      DEFAULT 0    AFTER `ibu_hamil` ");
        if (!in_array('ibu_melahirkan', $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `ibu_melahirkan` INT(11)      DEFAULT 0    AFTER `ibu_menyusui` ");
        if (!in_array('ibu_nifas',      $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `ibu_nifas`      INT(11)      DEFAULT 0    AFTER `ibu_melahirkan` ");
        if (!in_array('ibu_meninggal',  $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `ibu_meninggal`  INT(11)      DEFAULT 0    AFTER `ibu_nifas` ");
        if (!in_array('pus',            $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `pus`            INT(11)      DEFAULT 0    AFTER `ibu_meninggal` ");
        if (!in_array('wus',            $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `wus`            INT(11)      DEFAULT 0    AFTER `pus` ");
        if (!in_array('lansia',         $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `lansia`         INT(11)      DEFAULT 0    AFTER `wus` ");
        if (!in_array('buta',           $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `buta`           INT(11)      DEFAULT 0    AFTER `lansia` ");
        if (!in_array('bayi_lahir_l',   $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `bayi_lahir_l`   INT(11)      DEFAULT 0    AFTER `buta` ");
        if (!in_array('bayi_lahir_p',   $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `bayi_lahir_p`   INT(11)      DEFAULT 0    AFTER `bayi_lahir_l` ");
        if (!in_array('akte_ada',       $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `akte_ada`       INT(11)      DEFAULT 0    AFTER `bayi_lahir_p` ");
        if (!in_array('akte_tidak',     $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `akte_tidak`     INT(11)      DEFAULT 0    AFTER `akte_ada` ");
        if (!in_array('bayi_meninggal_l',   $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `bayi_meninggal_l`   INT(11)      DEFAULT 0    AFTER `akte_tidak` ");
        if (!in_array('bayi_meninggal_p',   $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `bayi_meninggal_p`   INT(11)      DEFAULT 0    AFTER `bayi_meninggal_l` ");
        if (!in_array('balita_meninggal_l', $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `balita_meninggal_l` INT(11)      DEFAULT 0    AFTER `bayi_meninggal_p` ");
        if (!in_array('balita_meninggal_p', $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `balita_meninggal_p` INT(11)      DEFAULT 0    AFTER `balita_meninggal_l` ");
        if (!in_array('rumah_sehat',        $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `rumah_sehat`        INT(11)      DEFAULT 0    AFTER `balita_meninggal_p` ");
        if (!in_array('rumah_kurang_sehat', $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `rumah_kurang_sehat` INT(11)      DEFAULT 0    AFTER `rumah_sehat` ");
        if (!in_array('sampah',             $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `sampah`             INT(11)      DEFAULT 0    AFTER `rumah_kurang_sehat` ");
        if (!in_array('jamban',             $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `jamban`             INT(11)      DEFAULT 0    AFTER `sampah` ");
        if (!in_array('air_bersih',         $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `air_bersih`         INT(11)      DEFAULT 0    AFTER `jamban` ");
        if (!in_array('makanan_pokok',      $cols)) $conn->query("ALTER TABLE `kelurahan` ADD `makanan_pokok`      VARCHAR(100) DEFAULT NULL AFTER `air_bersih` ");
        
        // Gabung semua penambahan kolom pengurus ke dalam satu query agar stabil di InfinityFree
        $toAdd = [];
        if (!in_array('sekretaris_pkk',  $cols)) $toAdd[] = "ADD `sekretaris_pkk` VARCHAR(255) DEFAULT NULL";
        if (!in_array('bendahara_pkk',   $cols)) $toAdd[] = "ADD `bendahara_pkk`  VARCHAR(255) DEFAULT NULL";
        if (!in_array('pokja1_pkk',      $cols)) $toAdd[] = "ADD `pokja1_pkk`     VARCHAR(255) DEFAULT NULL";
        if (!in_array('pokja2_pkk',      $cols)) $toAdd[] = "ADD `pokja2_pkk`     VARCHAR(255) DEFAULT NULL";
        if (!in_array('pokja3_pkk',      $cols)) $toAdd[] = "ADD `pokja3_pkk`     VARCHAR(255) DEFAULT NULL";
        if (!in_array('pokja4_pkk',      $cols)) $toAdd[] = "ADD `pokja4_pkk`     VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_ketua',      $cols)) $toAdd[] = "ADD `foto_ketua`     VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_sekretaris',  $cols)) $toAdd[] = "ADD `foto_sekretaris` VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_bendahara',  $cols)) $toAdd[] = "ADD `foto_bendahara` VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_pokja1',     $cols)) $toAdd[] = "ADD `foto_pokja1`    VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_pokja2',     $cols)) $toAdd[] = "ADD `foto_pokja2`    VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_pokja3',     $cols)) $toAdd[] = "ADD `foto_pokja3`    VARCHAR(255) DEFAULT NULL";
        if (!in_array('foto_pokja4',     $cols)) $toAdd[] = "ADD `foto_pokja4`    VARCHAR(255) DEFAULT NULL";

        if (!empty($toAdd)) {
            $sql = "ALTER TABLE `kelurahan` " . implode(", ", $toAdd);
            $conn->query($sql);
        }
    }

    // 4. Update tabel users untuk kelurahan_id & Buat Superadmin Otomatis
    $res  = $conn->query("SHOW COLUMNS FROM `users` ");
    $cols = [];
    while ($r = $res->fetch_assoc()) $cols[] = $r['Field'];
    if (!in_array('kelurahan_id', $cols)) $conn->query("ALTER TABLE `users` ADD `kelurahan_id` INT(11) DEFAULT NULL AFTER `role` ");

    _ensureSuperAdmin($conn);

    // 5. Update tabel berita, kegiatan & laporan untuk kelurahan_id & likes
    $tables_check = ['berita', 'kegiatan', 'laporan'];
    foreach($tables_check as $tbl) {
        $res = $conn->query("SHOW COLUMNS FROM `$tbl` ");
        $cols = [];
        while ($r = $res->fetch_assoc()) $cols[] = $r['Field'];
        if (!in_array('kelurahan_id', $cols)) {
            $conn->query("ALTER TABLE `$tbl` ADD `kelurahan_id` INT(11) DEFAULT 0 AFTER `id` ");
            $conn->query("ALTER TABLE `$tbl` ADD INDEX (`kelurahan_id`) ");
        }
        if ($tbl === 'kegiatan' && !in_array('likes', $cols)) {
            $conn->query("ALTER TABLE `kegiatan` ADD `likes` INT(11) DEFAULT 0 AFTER `lokasi` ");
        }
    }

    // Pastikan folder upload utama ada
    $dirs = [
        __DIR__ . '/../uploads',
        __DIR__ . '/../uploads/settings',
        __DIR__ . '/../uploads/bidang',
        __DIR__ . '/../uploads/kegiatan',
        __DIR__ . '/../uploads/berita',
        __DIR__ . '/../uploads/laporan',
        __DIR__ . '/../uploads/galeri'
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) @mkdir($dir, 0755, true);
    }

    // 6. Pastikan Tabel Bidang & Anggota Ada
    _ensureBidangTables($conn);

    // 7. Seed default settings
    _seedSettings($conn);

    // 8. Sembunyikan org_sekcam dari editor (pindah ke group hidden)
    $conn->query("UPDATE site_settings SET setting_group = 'profil_struktur_hidden'
                  WHERE setting_key IN ('org_sekcam_foto','org_sekcam_nama','org_sekcam_jabatan')");
}

/**
 * Membuat akun Superadmin secara otomatis jika belum ada
 */
function _ensureSuperAdmin($conn) {
    $username = 'master_admin';
    $password = 'pulomerak2024';
    $nama     = 'Super Administrator';
    
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param('s', $username);
    $check->execute();
    if ($check->get_result()->num_rows == 0) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $ins = $conn->prepare("INSERT INTO users (username, password, nama, role, kelurahan_id) VALUES (?, ?, ?, 'admin', NULL)");
        $ins->bind_param('sss', $username, $hash, $nama);
        $ins->execute();
    }
}

/**
 * Membuat dan mengisi tabel bidang secara otomatis
 */
function _ensureBidangTables($conn) {
    // Buat Tabel Bidang
    $conn->query("CREATE TABLE IF NOT EXISTS `bidang` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `nama` VARCHAR(100) NOT NULL,
        `slug` VARCHAR(50) NOT NULL UNIQUE,
        `deskripsi` TEXT,
        `prestasi` TEXT,
        `program_unggulan` TEXT,
        `gambar` VARCHAR(255),
        `urutan` INT(11) DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Buat Tabel Anggota
    $conn->query("CREATE TABLE IF NOT EXISTS `anggota_bidang` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `bidang_id` INT(11) NOT NULL,
        `nama` VARCHAR(150) NOT NULL,
        `jabatan` VARCHAR(100),
        `foto` VARCHAR(255),
        `no_hp` VARCHAR(20),
        `urutan` INT(11) DEFAULT 0,
        PRIMARY KEY (`id`),
        CONSTRAINT `fk_bidang_auto` FOREIGN KEY (`bidang_id`) REFERENCES `bidang`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Jika tabel bidang masih kosong, isi data awal
    $defaultBidangs = [
        ['Sekretariat', 'sekretariat', 'Sekretariat bertugas mengoordinasikan administrasi, organisasi, dan hubungan antar lembaga TP PKK Kecamatan Pulomerak.', 'Tertib Administrasi Terbaik 2024', '1. Digitalisasi Arsip (E-PKK)\n2. Koordinasi Berjenjang\n3. Evaluasi Kinerja Kelurahan', 'hero_sekretariat.jpg', 1],
        ['POKJA I', 'pokja-1', 'POKJA I mengelola program Penghayatan dan Pengamalan Pancasila serta Gotong Royong untuk pembinaan karakter keluarga.', 'Juara 2 Lomba Posyandu Tingkat Kota', '1. Pola Asuh Anak & Remaja (PAREDI)\n2. Pembinaan Karakter Keluarga\n3. Gotong Royong Masyarakat', 'hero_pokja1.jpg', 2],
        ['POKJA II', 'pokja-2', 'POKJA II fokus pada bidang Pendidikan dan Keterampilan serta Pengembangan Kehidupan Berkoperasi untuk ekonomi keluarga.', 'Terbaik Pengembangan UP2K PKK 2023', '1. Gerakan Gemar Membaca (GELARI PELANGI)\n2. Pendidikan Keterampilan Keluarga\n3. Pengembangan Ekonomi Keluarga', 'hero_pokja2.jpg', 3],
        ['POKJA III', 'pokja-3', 'POKJA III mengelola program Pangan, Sandang, Perumahan, dan Tata Laksana Rumah Tangga untuk ketahanan pangan.', 'Pemenang Halaman Asri Teratur Indah (HATINYA PKK)', '1. Aku Hatinya PKK\n2. Sosialisasi Pangan Sehat\n3. Ketahanan Pangan Keluarga', 'hero_pokja3.jpg', 4],
        ['POKJA IV', 'pokja-4', 'POKJA IV membidangi Kesehatan, Kelestarian Lingkungan Hidup, dan Perencanaan Sehat untuk kualitas hidup keluarga.', 'Juara Lomba Lingkungan Bersih & Sehat', '1. Percepatan Penurunan Stunting\n2. Pembinaan PHBS Keluarga\n3. Perencanaan Sehat Masyarakat', 'hero_pokja4.jpg', 5]
    ];

    foreach ($defaultBidangs as $b) {
        $check = $conn->prepare("SELECT id FROM bidang WHERE slug = ?");
        $check->bind_param('s', $b[1]);
        $check->execute();
        if ($check->get_result()->num_rows == 0) {
            $ins = $conn->prepare("INSERT INTO `bidang` (`nama`, `slug`, `deskripsi`, `prestasi`, `program_unggulan`, `gambar`, `urutan`) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->bind_param('ssssssi', $b[0], $b[1], $b[2], $b[3], $b[4], $b[5], $b[6]);
            $ins->execute();
        }
    }
}

function _seed($conn, $key, $value, $group, $label, $type = 'text') {
    $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value, setting_group, label, field_type) 
                            VALUES (?, ?, ?, ?, ?) 
                            ON DUPLICATE KEY UPDATE label = VALUES(label), field_type = VALUES(field_type)");
    if (!$stmt) return;
    $stmt->bind_param("sssss", $key, $value, $group, $label, $type);
    $stmt->execute();
    $stmt->close();
}

function _seedSettings($conn) {
    // Beranda — Hero
    _seed($conn, 'beranda_hero_image',    '',   'beranda_hero', 'Gambar Background Hero', 'image');
    _seed($conn, 'beranda_hero_title',    'Bersama Membangun Keluarga<br><span>Sejahtera &amp; Mandiri</span>', 'beranda_hero', 'Judul Hero', 'textarea');
    _seed($conn, 'beranda_hero_subtitle', 'Pusat informasi kegiatan, program inovasi, dan dokumentasi pemberdayaan masyarakat yang mendukung kesejahteraan keluarga di Kecamatan Pulomerak, Kota Cilegon.', 'beranda_hero', 'Deskripsi Hero', 'textarea');

    // Berita — Hero
    _seed($conn, 'berita_hero_image',    '', 'berita_hero', 'Gambar Background Hero Berita', 'image');
    _seed($conn, 'berita_hero_title',    'Berita & <span>Informasi</span>', 'berita_hero', 'Judul Hero Berita',   'textarea');
    _seed($conn, 'berita_hero_subtitle', 'Ikuti perkembangan kegiatan dan kabar terbaru dari seluruh Kelurahan di Kecamatan Pulomerak.', 'berita_hero', 'Deskripsi Hero Berita', 'textarea');

    // Kegiatan — Hero
    _seed($conn, 'kegiatan_hero_image',    '', 'kegiatan_hero', 'Gambar Background Hero Kegiatan', 'image');
    _seed($conn, 'kegiatan_hero_title',    'Galeri <span>Kegiatan</span>', 'kegiatan_hero', 'Judul Hero Kegiatan',   'textarea');
    _seed($conn, 'kegiatan_hero_subtitle', 'Dokumentasi berbagai kegiatan pemberdayaan masyarakat di wilayah Kecamatan Pulomerak.', 'kegiatan_hero', 'Deskripsi Hero Kegiatan', 'textarea');

    // Laporan — Hero
    _seed($conn, 'laporan_hero_image',    '', 'laporan_hero', 'Gambar Background Hero Laporan', 'image');
    _seed($conn, 'laporan_hero_title',    'Laporan & <span>Arsip</span>', 'laporan_hero', 'Judul Hero Laporan',   'textarea');
    _seed($conn, 'laporan_hero_subtitle', 'Transparansi data dan laporan capaian program kerja TP PKK Kecamatan Pulomerak.', 'laporan_hero', 'Deskripsi Hero Laporan', 'textarea');

    // Beranda — Statistik
    _seed($conn, 'stat_penduduk', '12450', 'beranda_stats', 'Jumlah Penduduk', 'number');
    _seed($conn, 'stat_rw',       '8',     'beranda_stats', 'Jumlah RW',       'number');
    _seed($conn, 'stat_rt',       '32',    'beranda_stats', 'Jumlah RT',       'number');
    _seed($conn, 'stat_inovasi',  '5',     'beranda_stats', 'Program Inovasi', 'number');

    // Beranda — PKK
    _seed($conn, 'pkk_pengertian',      'Gerakan Nasional dalam pembangunan masyarakat yang tumbuh dari bawah, yang pengelolaannya dari, oleh, dan untuk masyarakat menuju terwujudnya keluarga yang beriman dan bertaqwa kepada Tuhan Yang Maha Esa, berakhlak mulia dan berbudi luhur, sehat sejahtera, maju dan mandiri, kesetaraan dan keadilan gender serta kesadaran hukum dan lingkungan.',   'beranda_pkk', 'Pengertian Gerakan PKK',  'textarea');
    _seed($conn, 'pkk_tujuan',          'Memberdayakan keluarga untuk meningkatkan kesejahteraannya menuju terwujudnya keluarga yang beriman dan bertaqwa kepada Tuhan Yang Maha Esa, berakhlak mulia dan berbudi luhur, sehat sejahtera, maju dan mandiri, kesetaraan dan keadilan gender serta kesadaran hukum dan lingkungan.', 'beranda_pkk', 'Tujuan Gerakan PKK',       'textarea');
    _seed($conn, 'pkk_sasaran',         'Keluarga di pedesaan maupun perkotaan yang perlu ditingkatkan dan dikembangkan kemampuan serta kepribadiannya dalam bidang:',                                                                                                                                                              'beranda_pkk', 'Sasaran PKK',              'textarea');
    _seed($conn, 'pkk_sasaran_mental',  'Sikap dan perilaku sebagai insan hamba Tuhan dan warga negara yang dinamis.',                                                                                                                                                                                                           'beranda_pkk', 'Sasaran: Mental Spiritual', 'textarea');
    _seed($conn, 'pkk_sasaran_fisik',   'Pangan, sandang, papan, kesehatan, dan lingkungan hidup yang sehat.',                                                                                                                                                                                                                   'beranda_pkk', 'Sasaran: Fisik Material',  'textarea');
    _seed($conn, 'pkk_tugas',           'Tanggung jawab utama meliputi koordinasi TP PKK Desa/Kelurahan, penyuluhan kepada keluarga, pembinaan program kerja, serta pelaporan hasil kegiatan secara berkala kepada tingkat Kota.',                                                                                               'beranda_pkk', 'Tugas TP PKK Kecamatan',   'textarea');

    // Beranda — Counter
    _seed($conn, 'counter_kk',         '3200', 'beranda_counter', 'Kepala Keluarga',       'number');
    _seed($conn, 'counter_sekolah',    '4',    'beranda_counter', 'Sekolah Aktif',         'number');
    _seed($conn, 'counter_kesehatan',  '3',    'beranda_counter', 'Fasilitas Kesehatan',   'number');
    _seed($conn, 'counter_ibadah',     '12',   'beranda_counter', 'Tempat Ibadah',         'number');


    // Profil — Hero
    _seed($conn, 'profil_hero_image',    '', 'profil_hero', 'Gambar Background Hero Profil', 'image');
    _seed($conn, 'profil_hero_title',    'Mewujudkan Masyarakat<br><span>Maju &amp; Sejahtera</span>', 'profil_hero', 'Judul Hero Profil',   'textarea');
    _seed($conn, 'profil_hero_subtitle', 'Mengenal lebih dekat visi, misi, dan struktur organisasi Pemerintah Kecamatan Pulomerak dalam melayani masyarakat Kota Cilegon.', 'profil_hero', 'Deskripsi Hero Profil', 'textarea');

    // Profil — Tentang
    _seed($conn, 'profil_tentang_image', '',                         'profil_tentang', 'Foto Kecamatan',       'image');
    _seed($conn, 'profil_tentang_1',     'Kecamatan Pulomerak adalah salah satu kecamatan yang berada di wilayah Kota Cilegon, Provinsi Banten. Terletak di ujung barat Pulau Jawa, Pulomerak dikenal sebagai gerbang penyeberangan utama Jawa–Sumatera melalui Pelabuhan Merak.', 'profil_tentang', 'Tentang: Paragraf 1', 'textarea');
    _seed($conn, 'profil_tentang_2',     'Dengan luas wilayah yang strategis, kecamatan ini dihuni oleh ribuan jiwa yang terbagi dalam berbagai kelurahan dan lingkungan. Masyarakatnya yang heterogen menjadikan Pulomerak sebagai wilayah yang dinamis dan kaya akan keberagaman budaya.', 'profil_tentang', 'Tentang: Paragraf 2', 'textarea');
    _seed($conn, 'profil_tentang_3',     'Pemerintah Kecamatan Pulomerak berkomitmen untuk memberikan pelayanan terbaik kepada masyarakat melalui program-program inovatif dan transparansi informasi publik.', 'profil_tentang', 'Tentang: Paragraf 3', 'textarea');
    _seed($conn, 'profil_lokasi',        'Kec. Pulomerak, Kota Cilegon', 'profil_tentang', 'Lokasi',         'text');
    _seed($conn, 'profil_luas',          '±3,2 km²',                     'profil_tentang', 'Luas Wilayah',   'text');
    _seed($conn, 'profil_penduduk_info', '±12.450 Jiwa',                 'profil_tentang', 'Jumlah Penduduk','text');

    // Profil — Visi Misi
    _seed($conn, 'profil_visi', '"Terwujudnya Kecamatan Pulomerak yang Maju, Bersih, dan Sejahtera Melalui Pelayanan Prima Berbasis Teknologi dan Partisipasi Masyarakat."', 'profil_visimisi', 'Visi', 'textarea');
    _seed($conn, 'profil_misi', "Meningkatkan kualitas pelayanan administrasi yang cepat, tepat, dan transparan.\nMendorong partisipasi aktif masyarakat dalam pembangunan kecamatan.\nMengembangkan potensi ekonomi lokal dan UMKM masyarakat Pulomerak.\nMenjaga ketertiban, keamanan, dan kerukunan antar warga.\nMeningkatkan kualitas lingkungan hidup yang bersih, sehat, dan nyaman.\nMemanfaatkan teknologi informasi untuk transparansi pemerintahan.", 'profil_visimisi', 'Misi (satu per baris)', 'textarea');

    // Profil — Struktur Organisasi (Ketua TPPKK)
    _seed($conn, 'org_camat_foto',     '', 'profil_struktur', 'Ketua Kecamatan: Foto',     'image');
    _seed($conn, 'org_camat_nama',     'Ny. Hj. Siti Munawaroh', 'profil_struktur', 'Ketua Kecamatan: Nama',     'text');
    _seed($conn, 'org_camat_jabatan',  'Ketua TPPKK Kecamatan Pulomerak', 'profil_struktur', 'Ketua Kecamatan: Jabatan',  'text');
    // org_sekcam — tidak ditampilkan di frontend, disembunyikan dari editor
    _seed($conn, 'org_sekcam_foto',    '', 'profil_struktur_hidden', '(Tidak Ditampilkan) Foto',    'image');
    _seed($conn, 'org_sekcam_nama',    '', 'profil_struktur_hidden', '(Tidak Ditampilkan) Nama',    'text');
    _seed($conn, 'org_sekcam_jabatan', '', 'profil_struktur_hidden', '(Tidak Ditampilkan) Jabatan', 'text');
    // 4 Ketua TPPKK Kelurahan
    $kelurahan = [1 => 'Suralaya', 2 => 'Tamansari', 3 => 'Lebakgede', 4 => 'Mekarsari'];
    for ($i = 1; $i <= 4; $i++) {
        $kel = $kelurahan[$i];
        _seed($conn, "org_kasi_{$i}_foto",    '',                              'profil_struktur', "Kel. {$kel}: Foto",    'image');
        _seed($conn, "org_kasi_{$i}_nama",    "Ketua TPPKK Kel. {$kel}",      'profil_struktur', "Kel. {$kel}: Nama",    'text');
        _seed($conn, "org_kasi_{$i}_jabatan", "Ketua TPPKK Kelurahan {$kel}", 'profil_struktur', "Kel. {$kel}: Jabatan", 'text');
    }

    // Profil — Batas Wilayah
    _seed($conn, 'batas_utara',   'Selat Sunda',   'profil_batas', 'Batas Utara',   'text');
    _seed($conn, 'batas_selatan', 'Kel. Suralaya', 'profil_batas', 'Batas Selatan', 'text');
    _seed($conn, 'batas_barat',   'Selat Sunda',   'profil_batas', 'Batas Barat',   'text');
    _seed($conn, 'batas_timur',   'Kel. Lebak Gede','profil_batas','Batas Timur',   'text');

    // Footer
    _seed($conn, 'footer_alamat',     'Jl. Raya Merak, Kecamatan Pulomerak, Kota Cilegon, Banten 42438', 'footer', 'Alamat Footer',    'text');
    _seed($conn, 'footer_telepon',    '(0254) 571234',                'footer', 'Telepon Footer',   'text');
    _seed($conn, 'footer_email',      'kec.pulomerak@cilegon.go.id',  'footer', 'Email Footer',     'text');
    _seed($conn, 'footer_deskripsi',  'Portal resmi Kecamatan Pulomerak sebagai pusat informasi masyarakat, transparansi pemerintahan, dan pelayanan publik berbasis digital.', 'footer', 'Deskripsi Footer', 'textarea');
    _seed($conn, 'site_logo',         '', 'footer', 'Logo Website', 'image');
    _seed($conn, 'footer_instagram',  'https://www.instagram.com/',  'footer', 'Link Instagram',   'text');
    _seed($conn, 'footer_youtube',    'https://www.youtube.com/',    'footer', 'Link YouTube',     'text');
}
