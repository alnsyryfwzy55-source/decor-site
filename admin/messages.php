<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'رسائل التواصل';
$activeNav = 'messages';

if (isset($_GET['read'])) {
    $stmt = $pdo->prepare('UPDATE messages SET is_read = 1 WHERE id = ?');
    $stmt->execute([$_GET['read']]);
    redirect('messages.php');
}

if (isset($_GET['delete']) && hash_equals($_SESSION['csrf_token'] ?? '', $_GET['token'] ?? '')) {
    $stmt = $pdo->prepare('DELETE FROM messages WHERE id = ?');
    $stmt->execute([$_GET['delete']]);
    flash_set('success', 'تم حذف الرسالة');
    redirect('messages.php');
}

$messages = $pdo->query('SELECT * FROM messages ORDER BY created_at DESC')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="panel">
  <table class="admin-table">
    <thead><tr><th></th><th>الاسم</th><th>الهاتف</th><th>الرسالة</th><th>التاريخ</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($messages as $m): ?>
        <tr class="<?= $m['is_read'] ? '' : 'unread-row' ?>">
          <td><?= $m['is_read'] ? '' : '<span class="dot" title="غير مقروءة"></span>' ?></td>
          <td><?= e($m['name']) ?></td>
          <td dir="ltr"><a href="tel:<?= e($m['phone']) ?>"><?= e($m['phone']) ?></a></td>
          <td><?= e($m['message']) ?></td>
          <td><?= e($m['created_at']) ?></td>
          <td class="actions">
            <?php if (!$m['is_read']): ?><a href="messages.php?read=<?= $m['id'] ?>">تمييز كمقروءة</a><?php endif; ?>
            <a href="messages.php?delete=<?= $m['id'] ?>&token=<?= csrf_token() ?>" class="danger" data-confirm="حذف هذه الرسالة نهائيًا؟">حذف</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($messages)): ?><tr><td colspan="6">لا توجد رسائل بعد</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
