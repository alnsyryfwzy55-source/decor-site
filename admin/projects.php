<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'الأعمال';
$activeNav = 'projects';

$projects = $pdo->query(
    "SELECT p.*, c.name AS cat_name FROM projects p
     LEFT JOIN categories c ON c.id = p.category_id
     ORDER BY p.created_at DESC"
)->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="panel-header">
  <a href="project-edit.php" class="btn-admin">+ إضافة عمل جديد</a>
</div>

<div class="panel">
  <table class="admin-table">
    <thead>
      <tr><th>الصورة</th><th>العنوان</th><th>التصنيف</th><th>الحالة</th><th>مميز</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach ($projects as $p): ?>
      <tr>
        <td>
          <?php if ($p['cover_image']): ?>
            <img src="../uploads/portfolio/<?= e($p['cover_image']) ?>" class="thumb" alt="">
          <?php else: ?>
            <span class="thumb-empty">—</span>
          <?php endif; ?>
        </td>
        <td><?= e($p['title']) ?></td>
        <td><?= e($p['cat_name'] ?? '—') ?></td>
        <td><span class="badge <?= $p['status'] === 'published' ? 'badge-ok' : 'badge-muted' ?>">
          <?= $p['status'] === 'published' ? 'منشور' : 'مسودة' ?>
        </span></td>
        <td><?= $p['is_featured'] ? '⭐' : '' ?></td>
        <td class="actions">
          <a href="project-edit.php?id=<?= $p['id'] ?>">تعديل</a>
          <a href="project-delete.php?id=<?= $p['id'] ?>&token=<?= csrf_token() ?>" class="danger" data-confirm="هل تريد حذف هذا المشروع نهائيًا؟">حذف</a>
        </td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($projects)): ?>
        <tr><td colspan="6">لا توجد أعمال بعد — اضغط "إضافة عمل جديد"</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
