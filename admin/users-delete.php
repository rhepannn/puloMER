<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();

if (!isSuperAdmin()) {
    setFlash('error', 'Halaman ini hanya dapat diakses oleh Superadmin.');
    redirect(SITE_URL . '/admin/index.php');
}

$id = (int)($_GET['id'] ?? 0);
if (!$id || $id == $_SESSION['admin_id']) {
    setFlash('error', 'Aksi tidak valid.');
    redirect(SITE_URL . '/admin/users.php');
}

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    setFlash('success', 'User berhasil dihapus.');
} else {
    setFlash('error', 'Gagal menghapus user: ' . $conn->error);
}

redirect(SITE_URL . '/admin/users.php');
