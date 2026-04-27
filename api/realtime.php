<?php
/**
 * Public Realtime API
 * Returns latest content IDs and counts — no authentication required.
 * Called by client-side JS every 30 seconds.
 */
require_once '../include/config.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');

$data = [
    'berita'   => null,
    'kegiatan' => null,
    'laporan'  => null,
    'counts'   => [],
    'ts'       => time(),
    'version'  => file_exists(__DIR__ . '/rt_version.txt') ? (int)file_get_contents(__DIR__ . '/rt_version.txt') : 0,
];

// Latest IDs
$tables = [
    'berita'   => ['id', 'judul', 'tgl_post'],
    'kegiatan' => ['id', 'judul', 'tgl_kegiatan'],
    'laporan'  => ['id', 'judul', 'created_at'],
];

foreach ($tables as $tbl => $fields) {
    $orderCol = $fields[2];
    $res = $conn->query("SELECT {$fields[0]}, {$fields[1]} FROM `$tbl` ORDER BY $orderCol DESC LIMIT 1");
    if ($res && $row = $res->fetch_assoc()) {
        $data[$tbl] = ['id' => (int)$row[$fields[0]], 'judul' => $row[$fields[1]]];
    }
}

// Counts for each table
$countTables = ['berita', 'kegiatan', 'laporan', 'galeri'];
foreach ($countTables as $tbl) {
    $res = $conn->query("SELECT COUNT(*) FROM `$tbl`");
    $data['counts'][$tbl] = $res ? (int)$res->fetch_row()[0] : 0;
}

echo json_encode($data);
