<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL . '/admin/kegiatan.php');
$stmt = $conn->prepare("SELECT gambar FROM kegiatan WHERE id=?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if ($row && !empty($row['gambar'])) {
    $path = '../uploads/kegiatan/' . $row['gambar'];
    if (file_exists($path)) unlink($path);
}
$del = $conn->prepare("DELETE FROM kegiatan WHERE id=?");
$del->bind_param('i', $id);
$del->execute();
setFlash('success', 'Kegiatan berhasil dihapus!');
redirect(SITE_URL . '/admin/kegiatan.php');
