<?php
require_once __DIR__ . '/includes/auth.php';

$id    = $_GET['id'] ?? null;
$token = $_GET['token'] ?? '';

if ($id && hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    if ($project) {
        $galleryImages = get_project_images($pdo, $project['id']);
        foreach ($galleryImages as $img) {
            delete_uploaded_file(__DIR__ . '/../uploads/portfolio', $img['image_path']);
        }
        delete_uploaded_file(__DIR__ . '/../uploads/portfolio', $project['cover_image']);
        $stmt = $pdo->prepare('DELETE FROM projects WHERE id = ?');
        $stmt->execute([$id]);
        flash_set('success', 'تم حذف المشروع بنجاح');
    }
} else {
    flash_set('error', 'طلب غير صالح');
}

redirect('projects.php');
