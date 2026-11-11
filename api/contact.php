<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'طريقة غير مسموحة']);
    exit;
}

$name    = trim($_POST['name'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $phone === '' || $message === '') {
    echo json_encode(['success' => false, 'error' => 'يرجى تعبئة جميع الحقول']);
    exit;
}
if (mb_strlen($name) > 120 || mb_strlen($phone) > 40 || mb_strlen($message) > 3000) {
    echo json_encode(['success' => false, 'error' => 'أحد الحقول طويل جدًا']);
    exit;
}
// حماية بسيطة من رقم الهاتف غير المنطقي
if (!preg_match('/^[0-9+\-\s()]{6,}$/', $phone)) {
    echo json_encode(['success' => false, 'error' => 'رقم الهاتف غير صالح']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO messages (name, phone, message) VALUES (?, ?, ?)');
    $stmt->execute([$name, $phone, $message]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'حدث خطأ، حاول لاحقًا']);
}
