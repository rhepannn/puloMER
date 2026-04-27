<?php
require_once '../include/config.php';
require_once '../include/functions.php';

header('Content-Type: application/json');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Increment likes
    $conn->query("UPDATE kegiatan SET likes = likes + 1 WHERE id = $id");
    
    // Get new count
    $res = $conn->query("SELECT likes FROM kegiatan WHERE id = $id");
    $row = $res->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'new_likes' => number_format($row['likes'])
    ]);
} else {
    echo json_encode(['success' => false]);
}
