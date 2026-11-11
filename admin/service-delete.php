<?php
require_once __DIR__ . '/includes/auth.php';

$id    = $_GET['id'] ?? null;
$token = $_GET['token'] ?? '';

if ($id && hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
    $stmt = $pdo->prepare('DELETE FROM services WHERE id = ?');
    $stmt->execute([$id]);
    flash_set('success', 'تم حذف الخدمة');
} else {
    flash_set('error', 'طلب غير صالح');
}

redirect('services.php');
