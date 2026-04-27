<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$id=(int)($_GET['id']??0);if(!$id)redirect(SITE_URL.'/admin/kelurahan.php');
$stmt=$conn->prepare("SELECT gambar FROM kelurahan WHERE id=?");$stmt->bind_param('i',$id);$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();
if($row&&$row['gambar']&&file_exists('../uploads/kegiatan/'.$row['gambar']))unlink('../uploads/kegiatan/'.$row['gambar']);
$del=$conn->prepare("DELETE FROM kelurahan WHERE id=?");$del->bind_param('i',$id);$del->execute();
setFlash('success','Data kelurahan berhasil dihapus!');redirect(SITE_URL.'/admin/kelurahan.php');
