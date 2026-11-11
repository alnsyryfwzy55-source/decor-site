<?php
require_once __DIR__ . '/../includes/functions.php';

// إن وُجد مسؤول واحد على الأقل، امنع الوصول لهذه الصفحة نهائيًا
$count = $pdo->query('SELECT COUNT(*) c FROM admins')->fetch()['c'];
if ($count > 0) {
    redirect('login.php');
}

$errors = [];
$old = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';
    $old = ['username' => $username, 'email' => $email];

    if (mb_strlen($username) < 3) $errors[] = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
    if (mb_strlen($password) < 8) $errors[] = 'كلمة المرور يجب أن تكون 8 أحرف على الأقل';
    if ($password !== $confirm) $errors[] = 'كلمتا المرور غير متطابقتين';

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO admins (username, email, password_hash) VALUES (?, ?, ?)');
        $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT)]);
        flash_set('success', 'تم إنشاء حسابك بنجاح، سجّل الدخول الآن.');
        redirect('login.php');
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تثبيت لوحة التحكم</title>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>مرحبًا 👋</h1>
    <p class="sub">هذه أول مرة تُشغَّل فيها لوحة التحكم. أنشئ حساب المسؤول الرئيسي.</p>

    <?php foreach ($errors as $err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endforeach; ?>

    <form method="post" class="admin-form">
      <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
      <label>اسم المستخدم
        <input type="text" name="username" required value="<?= e($old['username']) ?>">
      </label>
      <label>البريد الإلكتروني <small>(يُستخدم لاسترجاع كلمة المرور)</small>
        <input type="email" name="email" required value="<?= e($old['email']) ?>">
      </label>
      <label>كلمة المرور <small>(8 أحرف على الأقل)</small>
        <input type="password" name="password" required minlength="8">
      </label>
      <label>تأكيد كلمة المرور
        <input type="password" name="password_confirm" required minlength="8">
      </label>
      <button type="submit" class="btn-admin">إنشاء الحساب والبدء</button>
    </form>
  </div>
</body>
</html>
