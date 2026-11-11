<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'التصنيفات';
$activeNav = 'categories';

$editId  = $_GET['edit'] ?? null;
$editing = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $id   = $_POST['id'] ?? null;

    if ($name === '') {
        flash_set('error', 'اسم التصنيف مطلوب');
    } else {
        $slug = unique_slug($pdo, 'categories', slugify($name), $id ?: null);
        if ($id) {
            $stmt = $pdo->prepare('UPDATE categories SET name = ?, slug = ? WHERE id = ?');
            $stmt->execute([$name, $slug, $id]);
            flash_set('success', 'تم تحديث التصنيف');
        } else {
            $stmt = $pdo->prepare('INSERT INTO categories (name, slug) VALUES (?, ?)');
            $stmt->execute([$name, $slug]);
            flash_set('success', 'تمت إضافة التصنيف');
        }
    }
    redirect('categories.php');
}

if ($editId) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([$editId]);
    $editing = $stmt->fetch();
}

$categories = $pdo->query(
    'SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id = c.id) AS projects_count
     FROM categories c ORDER BY sort_order'
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="panel">
  <h2><?= $editing ? 'تعديل تصنيف' : 'إضافة تصنيف جديد' ?></h2>
  <form method="post" class="admin-form inline-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <?php if ($editing): ?><input type="hidden" name="id" value="<?= $editing['id'] ?>"><?php endif; ?>
    <input type="text" name="name" placeholder="اسم التصنيف (مثال: مطابخ)" required value="<?= e($editing['name'] ?? '') ?>">
    <button type="submit" class="btn-admin"><?= $editing ? 'تحديث' : 'إضافة' ?></button>
    <?php if ($editing): ?><a href="categories.php" class="btn-admin secondary">إلغاء</a><?php endif; ?>
  </form>
</div>

<div class="panel">
  <table class="admin-table">
    <thead><tr><th>الاسم</th><th>الرابط</th><th>عدد الأعمال</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($categories as $c): ?>
        <tr>
          <td><?= e($c['name']) ?></td>
          <td dir="ltr"><?= e($c['slug']) ?></td>
          <td><?= $c['projects_count'] ?></td>
          <td class="actions">
            <a href="categories.php?edit=<?= $c['id'] ?>">تعديل</a>
            <a href="category-delete.php?id=<?= $c['id'] ?>&token=<?= csrf_token() ?>" class="danger" data-confirm="حذف هذا التصنيف؟ (لن تُحذف الأعمال المرتبطة به)">حذف</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($categories)): ?><tr><td colspan="4">لا توجد تصنيفات بعد</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
