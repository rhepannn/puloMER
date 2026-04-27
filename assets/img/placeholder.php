<?php
// Placeholder redirect - jika gambar tidak ditemukan
// File ini bisa diabaikan, placeholder menggunakan placehold.co via onerror di HTML

header('Content-Type: image/png');
// Generate simple placeholder PNG 400x250
$w=400; $h=250;
$img = imagecreatetruecolor($w,$h);
$bg  = imagecolorallocate($img,26,79,160);   // Biru primer
$fg  = imagecolorallocate($img,255,255,255); // Putih
imagefill($img,0,0,$bg);
imagestring($img,3,($w/2)-40,($h/2)-8,'Kelurahan Pulomerak',$fg);
imagepng($img);
imagedestroy($img);
