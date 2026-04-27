<?php
require_once '../include/config.php';
require_once '../include/functions.php';

// Proteksi API: Hanya admin yang bisa akses
if (!isAdminLoggedIn()) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

// Get Stats
$stats = [
    'berita'       => $conn->query("SELECT COUNT(*) FROM berita")->fetch_row()[0],
    'kegiatan'     => $conn->query("SELECT COUNT(*) FROM kegiatan")->fetch_row()[0],
    'laporan'      => $conn->query("SELECT COUNT(*) FROM laporan")->fetch_row()[0],
    'anggota'      => $conn->query("SELECT COUNT(*) FROM anggota_bidang")->fetch_row()[0],
];

// Get Latest Content Info for Notifications
// Kita ambil ID paling besar dari berita dan kegiatan
$latest_berita   = $conn->query("SELECT id, judul FROM berita ORDER BY id DESC LIMIT 1")->fetch_assoc();
$latest_kegiatan = $conn->query("SELECT id, judul FROM kegiatan ORDER BY id DESC LIMIT 1")->fetch_assoc();

$response = [
    'status'   => 'success',
    'stats'    => $stats,
    'latest'   => [
        'berita'   => $latest_berita,
        'kegiatan' => $latest_kegiatan
    ],
    'server_time' => time()
];

echo json_encode($response);
