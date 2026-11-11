<?php
require_once __DIR__ . '/../includes/functions.php';

$token = $_GET['token'] ?? $_POST['token'] ?? '';
$id    = $_GET['id'] ?? $_POST['id'] ?? '';
$error = '';
$success = false;
$admin = null;

if ($token && $id) {
    $stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ? AND reset_token = ? AND reset_expires > NOW()');
    $stmt->execute([$id, hash('sha256', $token)]);
    $admin = $stmt->fetch();
}

if (!$admin) {
    $error = 'رابط الاسترجاع غير صالح أو انتهت صلاحيته. يرجى طلب رابط جديد.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    if (mb_strlen($password) < 8) {
        $error = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    } elseif ($password !== $confirm) {
        $error = 'كلمتا المرور غير متطابقتين';
    } else {
        $stmt = $pdo->prepare('UPDATE admins SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>إعادة تعيين كلمة المرور</title>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>كلمة مرور جديدة</h1>

    <?php if ($success): ?>
      <div class="alert alert-ok">تم تغيير كلمة المرور بنجاح.</div>
      <div class="auth-links"><a href="login.php">تسجيل الدخول الآن</a></div>

    <?php elseif ($error): ?>
      <div class="alert alert-err"><?= e($error) ?></div>
      <div class="auth-links"><a href="forgot-password.php">طلب رابط جديد</a></div>

    <?php else: ?>
      <p class="sub">أدخل كلمة مرور جديدة لحساب <?= e($admin['username']) ?></p>
      <form method="post" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">
        <input type="hidden" name="id" value="<?= e($id) ?>">
        <label>كلمة المرور الجديدة <small>(8 أحرف على الأقل)</small>
          <input type="password" name="password" required minlength="8" autofocus>
        </label>
        <label>تأكيد كلمة المرور
          <input type="password" name="password_confirm" required minlength="8">
        </label>
        <button type="submit" class="btn-admin">حفظ كلمة المرور</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
