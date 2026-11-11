<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'الخدمات';
$activeNav = 'services';

$editId  = $_GET['edit'] ?? null;
$editing = null;
$icons   = ['design' => 'تصميم داخلي', 'tools' => 'أدوات/تنفيذ', 'furniture' => 'أثاث', 'chat' => 'استشارة'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon        = array_key_exists($_POST['icon'] ?? '', $icons) ? $_POST['icon'] : 'design';
    $id          = $_POST['id'] ?? null;

    if ($title === '') {
        flash_set('error', 'عنوان الخدمة مطلوب');
    } else {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE services SET title=?, description=?, icon=? WHERE id=?');
            $stmt->execute([$title, $description, $icon, $id]);
            flash_set('success', 'تم تحديث الخدمة');
        } else {
            $stmt = $pdo->prepare('INSERT INTO services (title, description, icon) VALUES (?,?,?)');
            $stmt->execute([$title, $description, $icon]);
            flash_set('success', 'تمت إضافة الخدمة');
        }
    }
    redirect('services.php');
}

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM services WHERE id = ?');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

$services = $pdo->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="panel">
  <h2><?= $editing ? 'تعديل خدمة' : 'إضافة خدمة جديدة' ?></h2>
  <form method="post" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
    <label>العنوان
      <input type="text" name="title" required value="<?= e($editing['title'] ?? '') ?>">
    </label>
    <label>الوصف
      <textarea name="description" rows="3"><?= e($editing['description'] ?? '') ?></textarea>
    </label>
    <label>الأيقونة
      <select name="icon">
        <?php foreach ($icons as $key => $label): ?>
          <option value="<?= $key ?>" <?= (($editing['icon'] ?? 'design') === $key) ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <div class="form-actions">
      <button type="submit" class="btn-admin"><?= $editing ? 'تحديث' : 'إضافة' ?></button>
      <?php if ($editing): ?><a href="services.php" class="btn-admin secondary">إلغاء</a><?php endif; ?>
    </div>
  </form>
</div>

<div class="panel">
  <table class="admin-table">
    <thead><tr><th>العنوان</th><th>الوصف</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($services as $sv): ?>
        <tr>
          <td><?= e($sv['title']) ?></td>
          <td><?= e(mb_substr($sv['description'] ?? '', 0, 60)) ?>…</td>
          <td class="actions">
            <a href="services.php?edit=<?= $sv['id'] ?>">تعديل</a>
            <a href="service-delete.php?id=<?= $sv['id'] ?>&token=<?= csrf_token() ?>" class="danger" data-confirm="حذف هذه الخدمة؟">حذف</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($services)): ?><tr><td colspan="3">لا توجد خدمات بعد</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
