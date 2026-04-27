<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect(SITE_URL.'/admin/bidang.php');

$stmt = $conn->prepare("SELECT bidang_id, foto FROM anggota_bidang WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$a = $stmt->get_result()->fetch_assoc();

if ($a) {
    $bidang_id = $a['bidang_id'];
    if ($a['foto'] && file_exists('../uploads/bidang/' . $a['foto'])) {
        unlink('../uploads/bidang/' . $a['foto']);
    }
    
    $stmt = $conn->prepare("DELETE FROM anggota_bidang WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        setFlash('success', 'Anggota berhasil dihapus!');
    } else {
        setFlash('error', 'Gagal menghapus anggota: ' . $conn->error);
    }
    redirect(SITE_URL . '/admin/anggota.php?bidang_id=' . $bidang_id);
} else {
    redirect(SITE_URL . '/admin/bidang.php');
}
