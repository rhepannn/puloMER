<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL . '/admin/berita.php');

$stmt = $conn->prepare("SELECT gambar FROM berita WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) redirect(SITE_URL . '/admin/berita.php');

// Hapus file gambar jika ada
if (!empty($row['gambar'])) {
    $path = '../uploads/berita/' . $row['gambar'];
    if (file_exists($path)) unlink($path);
}

$del = $conn->prepare("DELETE FROM berita WHERE id = ?");
$del->bind_param('i', $id);
$del->execute();

setFlash('success', 'Berita berhasil dihapus!');
redirect(SITE_URL . '/admin/berita.php');
