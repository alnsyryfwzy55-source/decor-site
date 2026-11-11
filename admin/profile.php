<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'حسابي';
$activeNav = 'profile';

$stmt = $pdo->prepare('SELECT * FROM admins WHERE id = ?');
$stmt->execute([$current_admin_id]);
$admin = $stmt->fetch();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username         = trim($_POST['username'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $currentPassword  = $_POST['current_password'] ?? '';
    $newPassword      = $_POST['new_password'] ?? '';

    if (!password_verify($currentPassword, $admin['password_hash'])) {
        $errors[] = 'كلمة المرور الحالية غير صحيحة';
    }
    if (mb_strlen($username) < 3) $errors[] = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'البريد الإلكتروني غير صالح';
    if ($newPassword !== '' && mb_strlen($newPassword) < 8) $errors[] = 'كلمة المرور الجديدة يجب أن تكون 8 أحرف على الأقل';

    if (empty($errors)) {
        if ($newPassword !== '') {
            $stmt = $pdo->prepare('UPDATE admins SET username=?, email=?, password_hash=? WHERE id=?');
            $stmt->execute([$username, $email, password_hash($newPassword, PASSWORD_DEFAULT), $admin['id']]);
        } else {
            $stmt = $pdo->prepare('UPDATE admins SET username=?, email=? WHERE id=?');
            $stmt->execute([$username, $email, $admin['id']]);
        }
        $_SESSION['admin_username'] = $username;
        flash_set('success', 'تم تحديث بياناتك بنجاح');
        redirect('profile.php');
    }
}

require __DIR__ . '/includes/header.php';
?>

<div class="panel">
  <form method="post" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <?php foreach ($errors as $err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endforeach; ?>

    <label>اسم المستخدم
      <input type="text" name="username" required value="<?= e($admin['username']) ?>">
    </label>
    <label>البريد الإلكتروني
      <input type="email" name="email" required value="<?= e($admin['email']) ?>">
    </label>
    <label>كلمة المرور الحالية <small>(مطلوبة لتأكيد أي تعديل)</small>
      <input type="password" name="current_password" required>
    </label>
    <label>كلمة مرور جديدة <small>(اتركها فارغة إن لم ترغب بتغييرها)</small>
      <input type="password" name="new_password">
    </label>

    <div class="form-actions">
      <button type="submit" class="btn-admin">حفظ التغييرات</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
