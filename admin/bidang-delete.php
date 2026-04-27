<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

if (!isSuperAdmin()) {
    setFlash('error', 'Akses ditolak.');
    redirect('bidang.php');
}

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    // Ambil info bidang untuk hapus gambar
    $stmt = $conn->prepare("SELECT gambar FROM bidang WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $b = $stmt->get_result()->fetch_assoc();
    
    if ($b && $b['gambar'] && file_exists('../uploads/bidang/' . $b['gambar'])) {
        @unlink('../uploads/bidang/' . $b['gambar']);
    }

    // Hapus anggota di bidang ini dulu (FK akan menangani jika ada, tapi aman untuk eksplisit)
    $conn->query("DELETE FROM anggota_bidang WHERE bidang_id = $id");
    
    // Hapus bidang
    $del = $conn->prepare("DELETE FROM bidang WHERE id = ?");
    $del->bind_param('i', $id);
    if ($del->execute()) {
        setFlash('success', 'Bidang berhasil dihapus.');
    } else {
        setFlash('error', 'Gagal menghapus bidang.');
    }
}

redirect('bidang.php');
