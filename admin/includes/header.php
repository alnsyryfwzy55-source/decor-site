<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle ?? 'لوحة التحكم') ?> | لوحة التحكم</title>
<link href="https://fonts.googleapis.com/css2?family=Almarai:wght@400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-wrap">

  <aside class="admin-sidebar" id="adminSidebar">
    <div class="admin-logo"><?= e(get_setting($pdo, 'site_name', 'الموقع')) ?><span>لوحة التحكم</span></div>
    <nav class="admin-nav">
      <a href="dashboard.php"  class="<?= ($activeNav ?? '') === 'dashboard'  ? 'active' : '' ?>">🏠 الرئيسية</a>
      <a href="projects.php"   class="<?= ($activeNav ?? '') === 'projects'   ? 'active' : '' ?>">🖼️ الأعمال</a>
      <a href="categories.php" class="<?= ($activeNav ?? '') === 'categories' ? 'active' : '' ?>">🏷️ التصنيفات</a>
      <a href="services.php"   class="<?= ($activeNav ?? '') === 'services'   ? 'active' : '' ?>">🛠️ الخدمات</a>
      <a href="messages.php"   class="<?= ($activeNav ?? '') === 'messages'   ? 'active' : '' ?>">✉️ الرسائل</a>
      <a href="settings.php"   class="<?= ($activeNav ?? '') === 'settings'   ? 'active' : '' ?>">⚙️ إعدادات الموقع</a>
      <a href="profile.php"    class="<?= ($activeNav ?? '') === 'profile'    ? 'active' : '' ?>">👤 حسابي</a>
      <hr>
      <a href="../index.php" target="_blank">↗ عرض الموقع</a>
      <a href="logout.php" class="logout-link">⏻ تسجيل الخروج</a>
    </nav>
  </aside>

  <div class="admin-main">
    <header class="admin-topbar">
      <button id="sidebarToggle" class="sidebar-toggle" aria-label="فتح القائمة">☰</button>
      <h1><?= e($pageTitle ?? '') ?></h1>
      <span class="topbar-user">مرحبًا، <?= e($current_admin_username ?? '') ?></span>
    </header>

    <div class="admin-content">
      <?php if ($msg = flash_get('success')): ?><div class="alert alert-ok"><?= e($msg) ?></div><?php endif; ?>
      <?php if ($msg = flash_get('error')): ?><div class="alert alert-err"><?= e($msg) ?></div><?php endif; ?>
