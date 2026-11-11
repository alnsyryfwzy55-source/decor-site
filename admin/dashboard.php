<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'نظرة عامة';
$activeNav = 'dashboard';

$projectsCount   = $pdo->query('SELECT COUNT(*) c FROM projects')->fetch()['c'];
$categoriesCount = $pdo->query('SELECT COUNT(*) c FROM categories')->fetch()['c'];
$messagesCount   = $pdo->query('SELECT COUNT(*) c FROM messages')->fetch()['c'];
$unreadCount     = $pdo->query('SELECT COUNT(*) c FROM messages WHERE is_read = 0')->fetch()['c'];
$latestMessages  = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC LIMIT 5')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="stat-cards">
  <div class="stat-card"><span class="num"><?= $projectsCount ?></span><span class="label">مشروع</span></div>
  <div class="stat-card"><span class="num"><?= $categoriesCount ?></span><span class="label">تصنيف</span></div>
  <div class="stat-card"><span class="num"><?= $messagesCount ?></span><span class="label">رسالة تواصل</span></div>
  <div class="stat-card highlight"><span class="num"><?= $unreadCount ?></span><span class="label">رسالة غير مقروءة</span></div>
</div>

<div class="panel">
  <h2>أحدث رسائل التواصل</h2>
  <table class="admin-table">
    <thead><tr><th>الاسم</th><th>الهاتف</th><th>التاريخ</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($latestMessages as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread-row' ?>">
          <td><?= e($m['name']) ?></td>
          <td dir="ltr"><?= e($m['phone']) ?></td>
          <td><?= e($m['created_at']) ?></td>
          <td><a href="messages.php">عرض الكل</a></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($latestMessages)): ?>
        <tr><td colspan="4">لا توجد رسائل بعد</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
