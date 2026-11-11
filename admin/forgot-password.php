<?php
require_once __DIR__ . '/../includes/functions.php';

if (!empty($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$submitted = false;
$devResetLink = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $submitted = true;

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare('SELECT * FROM admins WHERE email = ?');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // نعرض نفس الرسالة سواء وُجد البريد أم لا، حتى لا نكشف عن وجود حسابات
        if ($admin) {
            $token   = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', time() + 3600); // صالح لمدة ساعة

            $stmt = $pdo->prepare('UPDATE admins SET reset_token = ?, reset_expires = ? WHERE id = ?');
            $stmt->execute([hash('sha256', $token), $expires, $admin['id']]);

            $resetLink = rtrim(BASE_URL, '/') . '/admin/reset-password.php?token=' . $token . '&id=' . $admin['id'];

            $siteName = get_setting($pdo, 'site_name', 'الموقع');
            $subject  = '=?UTF-8?B?' . base64_encode('استرجاع كلمة المرور - ' . $siteName) . '?=';
            $body     = "مرحبًا {$admin['username']}،\r\n\r\nطلبت إعادة تعيين كلمة المرور. اضغط الرابط التالي خلال ساعة واحدة:\r\n{$resetLink}\r\n\r\nإن لم تطلب ذلك، تجاهل هذه الرسالة.";
            $headers  = 'From: ' . MAIL_FROM_NAME . ' <' . MAIL_FROM . '>' . "\r\n" .
                        'Content-Type: text/plain; charset=UTF-8';

            @mail($admin['email'], $subject, $body, $headers);

            // بيئة محلية (XAMPP/Laragon) عادة بدون خادم بريد مُهيأ فعليًا،
            // لذلك نعرض الرابط مباشرة كطريقة موثوقة بديلة أثناء التطوير فقط.
            if (APP_ENV === 'local') {
                $devResetLink = $resetLink;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>استرجاع كلمة المرور</title>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body class="auth-page">
  <div class="auth-card">
    <h1>استرجاع كلمة المرور</h1>
    <p class="sub">أدخل بريدك الإلكتروني المسجّل وسنرسل لك رابط إعادة التعيين</p>

    <?php if ($submitted): ?>
      <div class="alert alert-ok">إذا كان البريد الإلكتروني مسجّلًا لدينا، فستصلك رسالة تحتوي رابط إعادة تعيين كلمة المرور خلال دقائق.</div>
      <?php if ($devResetLink): ?>
        <div class="dev-note">
          🔧 وضع التطوير المحلي: لم نتأكد من إعداد خادم بريد على جهازك، لذلك إليك رابط الاسترجاع مباشرة:<br>
          <a href="<?= e($devResetLink) ?>"><?= e($devResetLink) ?></a>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <form method="post" class="admin-form">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <label>البريد الإلكتروني
          <input type="email" name="email" required autofocus>
        </label>
        <button type="submit" class="btn-admin">إرسال رابط الاسترجاع</button>
      </form>
    <?php endif; ?>

    <div class="auth-links"><a href="login.php">العودة لتسجيل الدخول</a></div>
  </div>
</body>
</html>
