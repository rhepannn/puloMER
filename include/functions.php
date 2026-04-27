<?php
// ============================================================
// FUNGSI HELPER - Portal Informasi Kecamatan Pulomerak
// ============================================================

/**
 * Sanitasi output untuk mencegah XSS
 */
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Format tanggal Indonesia
 */
function formatTanggal($date, $format = 'full') {
    if (!$date) return '-';
    $bulan = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $ts = strtotime($date);
    $d  = date('j', $ts);
    $m  = $bulan[(int)date('n', $ts)];
    $y  = date('Y', $ts);
    if ($format === 'short') return "$d $m $y";
    return "$d $m $y";
}

/**
 * Truncate teks
 */
function truncate($str, $len = 150) {
    if (!$str) return '';
    $str = strip_tags($str);
    if (mb_strlen($str) <= $len) return $str;
    return mb_substr($str, 0, $len) . '...';
}

/**
 * Slug sederhana
 */
function createSlug($str) {
    $str = strtolower(trim($str));
    $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
    $str = preg_replace('/[\s-]+/', '-', $str);
    return $str;
}

/**
 * Upload file
 */
function uploadFile($file, $dir, $allowedTypes = ['jpg','jpeg','png','gif','webp']) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) return false;
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedTypes)) {
        $types = strtoupper(implode(', ', $allowedTypes));
        return ['error' => "Format file tidak didukung. Gunakan $types."];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => 'Ukuran file terlalu besar. Maksimal 5MB.'];
    }
    $filename = time() . '_' . uniqid() . '.' . $ext;
    $dest = $dir . '/' . $filename;
    
    // Pastikan folder tujuan ada
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        @chmod($dest, 0644);
        return $filename;
    }
    return ['error' => 'Gagal memindahkan file ke server.'];
}

/**
 * Upload file PDF atau dokumen
 */
function uploadDoc($file, $dir) {
    return uploadFile($file, $dir, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
}

/**
 * Redirect
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Flash message
 */
function setFlash($type, $msg) {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
    // Global realtime hook: bump version on any successful DB modification
    if ($type === 'success' && stripos($msg, 'Selamat datang') === false) {
        @file_put_contents(__DIR__ . '/../api/rt_version.txt', time());
    }
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function showFlash() {
    $f = getFlash();
    if (!$f) return '';
    $isSuccess = $f['type'] === 'success';
    $styles = $isSuccess
        ? 'bg-emerald-50 border-emerald-200 text-emerald-800'
        : 'bg-red-50 border-red-200 text-red-800';
    $icon = $isSuccess ? 'fa-check-circle text-emerald-500' : 'fa-exclamation-circle text-red-500';
    return '
    <div class="flash-auto flex items-center justify-between gap-3 ' . $styles . ' border rounded-2xl px-4 py-3 mb-6 text-sm transition-opacity duration-500">
        <div class="flex items-center gap-2">
            <i class="fas ' . $icon . '"></i>
            <span class="font-medium">' . e($f['msg']) . '</span>
        </div>
        <button onclick="this.parentElement.remove()" class="opacity-40 hover:opacity-100 transition-opacity text-lg leading-none flex-shrink-0">&times;</button>
    </div>';
}

/**
 * Cek apakah admin sudah login
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Proteksi halaman admin
 */
function requireAdmin() {
    if (!isAdminLoggedIn()) {
        redirect(SITE_URL . '/admin/login.php');
    }
}

/**
 * Mendapatkan ID Kelurahan admin yang sedang login
 * Return NULL jika Superadmin, INT jika Admin Kelurahan
 */
function getKelurahanId() {
    return $_SESSION['admin_kelurahan'] ?? null;
}

function isSuperAdmin() {
    return getKelurahanId() === null;
}

function isKelurahanAdmin() {
    return getKelurahanId() !== null;
}

/**
 * Proteksi akses data agar hanya bisa diedit pemiliknya (atau superadmin)
 */
function checkOwnership($data_kelurahan_id) {
    if (isSuperAdmin()) return true;
    if ((int)getKelurahanId() === (int)$data_kelurahan_id) return true;
    
    setFlash('error', 'Anda tidak memiliki akses ke data ini.');
    redirect(SITE_URL . '/admin/index.php');
    return false;
}

/**
 * Ambil gambar atau default
 */
function getImg($file, $subdir = 'berita', $default = 'placeholder.jpg') {
    if (!$file) {
        if (strpos($default, 'http') !== false) return $default;
        return SITE_URL . '/assets/img/' . $default;
    }
    
    // Path fisik untuk pengecekan file_exists
    $path = __DIR__ . '/../uploads/' . $subdir . '/' . $file;
    
    if (file_exists($path)) {
        return SITE_URL . '/uploads/' . $subdir . '/' . $file;
    }

    // Jika di uploads tidak ada, coba cek di assets/img (untuk default banner/logo)
    $assetPath = __DIR__ . '/../assets/img/' . $file;
    if (file_exists($assetPath)) {
        return SITE_URL . '/assets/img/' . $file;
    }
    
    // Fallback terakhir ke default
    if (strpos($default, 'http') !== false) return $default;
    $fallbackPath = __DIR__ . '/../assets/img/' . $default;
    if (!file_exists($fallbackPath)) {
        return "https://ui-avatars.com/api/?name=PKK&background=f3f4f6&color=a1a1aa&size=128";
    }
    return SITE_URL . '/assets/img/' . $default;
}

/**
 * Pagination
 */
function paginate($total, $page, $perPage, $url = '?') {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $base = 'inline-flex items-center justify-center w-9 h-9 rounded-xl text-sm font-bold transition-all ';
    $html = '<div class="flex items-center gap-1.5">';
    if ($page > 1)
        $html .= '<a href="'.$url.'page='.($page-1).'" class="'.$base.'bg-white border border-gray-200 text-gray-500 hover:bg-softgray hover:text-accent shadow-sm">&#8592;</a>';
    for ($i = max(1, $page-2); $i <= min($totalPages, $page+2); $i++) {
        $cls = $i == $page ? $base.'bg-accent text-white shadow-lg shadow-accent/25' : $base.'bg-white border border-gray-200 text-gray-500 hover:bg-softgray hover:text-accent shadow-sm';
        $html .= '<a href="'.$url.'page='.$i.'" class="'.$cls.'">'.$i.'</a>';
    }
    if ($page < $totalPages)
        $html .= '<a href="'.$url.'page='.($page+1).'" class="'.$base.'bg-white border border-gray-200 text-gray-500 hover:bg-softgray hover:text-accent shadow-sm">&#8594;</a>';
    $html .= '</div>';
    return $html;
}

/**
 * Ambil satu setting dari tabel site_settings
 */
function getSetting($conn, $key, $default = '') {
    static $cache = [];
    if (isset($cache[$key])) return $cache[$key];
    
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    if (!$stmt) return $default;
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    $val = $row ? ($row['setting_value'] ?? $default) : $default;
    $cache[$key] = $val;
    return $val;
}

/**
 * Set satu setting
 */
function setSetting($conn, $key, $value) {
    $stmt = $conn->prepare("UPDATE site_settings SET setting_value = ? WHERE setting_key = ?");
    $stmt->bind_param("ss", $value, $key);
    $stmt->execute();
    $stmt->close();
}

/**
 * Ambil semua settings sekaligus (untuk performa halaman)
 */
function getAllSettings($conn, $group = null) {
    $settings = [];
    if ($group) {
        $stmt = $conn->prepare("SELECT setting_key, setting_value FROM site_settings WHERE setting_group = ?");
        $stmt->bind_param("s", $group);
    } else {
        $stmt = $conn->prepare("SELECT setting_key, setting_value FROM site_settings");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'] ?? '';
    }
    $stmt->close();
    return $settings;
}

/**
 * Ambil semua settings dengan info lengkap (untuk admin editor)
 */
function getSettingsByGroup($conn, $group) {
    $settings = [];
    $stmt = $conn->prepare("SELECT * FROM site_settings WHERE setting_group = ? ORDER BY setting_key");
    $stmt->bind_param("s", $group);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $settings[] = $row;
    }
    $stmt->close();
    return $settings;
}

/**
 * Bind parameters to a statement safely from an array
 */
function bindParamsSafe($stmt, $types, $params) {
    if (!$params) return;
    $stmt->bind_param($types, ...$params);
}
?>
