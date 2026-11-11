<?php
require_once __DIR__ . '/includes/auth.php';

$id        = $_GET['id'] ?? null;
$projectId = $_GET['project_id'] ?? null;
$token     = $_GET['token'] ?? '';

if ($id && hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $stmt = $pdo->prepare('SELECT * FROM project_images WHERE id = ?');
    $stmt->execute([$id]);
    $img = $stmt->fetch();

    if ($img) {
        delete_uploaded_file(__DIR__ . '/../uploads/portfolio', $img['image_path']);
        $stmt = $pdo->prepare('DELETE FROM project_images WHERE id = ?');
        $stmt->execute([$id]);
        flash_set('success', 'تم حذف الصورة من المعرض');
    }
} else {
    flash_set('error', 'طلب غير صالح');
}

redirect('project-edit.php?id=' . (int) $projectId);
