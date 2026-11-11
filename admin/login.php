<?php
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$count = $pdo->query('SELECT COUNT(*) c FROM admins')->fetch()['c'];
if ($count == 0) {
    redirect('install.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM admins WHERE username = ? OR email = ?');
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();

    if ($admin && $admin['locked_until'] && strtotime($admin['locked_until']) > time()) {
        $minutes = ceil((strtotime($admin['locked_until']) - time()) / 60);
        $error = "تم قفل الحساب مؤقتًا بسبب محاولات دخول فاشلة متكررة. حاول بعد {$minutes} دقيقة، أو استخدم استرجاع كلمة المرور.";
    } elseif ($admin && password_verify($password, $admin['password_hash'])) {
        $pdo->prepare('UPDATE admins SET failed_attempts = 0, locked_until = NULL WHERE id = ?')->execute([$admin['id']]);
        session_regenerate_id(true);
        $_SESSION['admin_id']       = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        redirect('dashboard.php');
    } else {
        if ($admin) {
            $attempts = $admin['failed_attempts'] + 1;
            $lockUntil = null;
            if ($attempts >= 5) {
                $lockUntil = date('Y-m-d H:i:s', time() + 5 * 60); // قفل 5 دقائق بعد 5 محاولات
            }
            $pdo->prepare('UPDATE admins SET failed_attempts = ?, locked_until = ? WHERE id = ?')
                ->execute([$attempts, $lockUntil, $admin['id']]);
        }
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول - لوحة التحكم</title>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>لوحة التحكم</h1>
    <p class="sub">سجّل الدخول لإدارة موقعك</p>

    <?php if ($msg = flash_get('success')): ?><div class="alert alert-ok"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>

    <form method="post" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <label>اسم المستخدم أو البريد الإلكتروني
        <input type="text" name="username" required autofocus>
      </label>
      <label>كلمة المرور
        <input type="password" name="password" required>
      </label>
      <button type="submit" class="btn-admin">دخول</button>
    </form>

    <div class="auth-links">
      <a href="forgot-password.php">نسيت كلمة المرور؟</a>
    </div>
  </div>
</body>
</html>
