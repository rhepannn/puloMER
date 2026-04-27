<?php
require_once '../include/config.php';
require_once '../include/functions.php';
requireAdmin();
$id=(int)($_GET['id']??0);if(!$id)redirect(SITE_URL.'/admin/laporan.php');
$stmt=$conn->prepare("SELECT file FROM laporan WHERE id=?");$stmt->bind_param('i',$id);$stmt->execute();
$row=$stmt->get_result()->fetch_assoc();
if($row&&$row['file']&&file_exists('../uploads/laporan/'.$row['file']))unlink('../uploads/laporan/'.$row['file']);
$del=$conn->prepare("DELETE FROM laporan WHERE id=?");$del->bind_param('i',$id);$del->execute();
setFlash('success','Laporan berhasil dihapus!');redirect(SITE_URL.'/admin/laporan.php');
