<?php
/**
 * هيدر مشترك لكل صفحات الموقع.
 * يتوقع أن الصفحة المستدعية عرّفت (اختياريًا) قبل تضمينه:
 * $pageTitle, $pageDescription, $activePage, $canonicalPath, $breadcrumbSchema
 */
$settings   = get_all_settings($pdo);
$s          = fn($key, $default = '') => e($settings[$key] ?? $default);
$phone_href = preg_replace('/\s+/', '', $settings['phone'] ?? '');

$finalTitle       = $pageTitle ?? $s('meta_title', $s('site_name'));
$finalDescription = $pageDescription ?? $s('meta_description');
$canonical        = full_url($canonicalPath ?? '');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($finalTitle) ?></title>
<meta name="description" content="<?= e($finalDescription) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">
<meta property="og:title" content="<?= e($finalTitle) ?>">
<meta property="og:description" content="<?= e($finalDescription) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e($canonical) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=El+Messiri:wght@500;600;700&family=Almarai:wght@300;400;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset_path('css/style.css')) ?>">

<?php if (!empty($breadcrumbSchema)): ?>
<script type="application/ld+json"><?= json_encode($breadcrumbSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<?php endif; ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "InteriorDesigner",
  "name": "<?= $s('site_name') ?> <?= $s('site_tagline') ?>",
  "telephone": "<?= $s('phone') ?>",
  "email": "<?= $s('email') ?>",
  "areaServed": "<?= $s('address') ?>",
  "url": "<?= e(full_url()) ?>"
}
</script>
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <a href="tel:<?= e($phone_href) ?>" class="topbar-link">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.81.31 1.6.57 2.36a2 2 0 0 1-.45 2.11L8.09 9.09a16 16 0 0 0 6 6l.9-.9a2 2 0 0 1 2.11-.45c.76.26 1.55.45 2.36.57A2 2 0 0 1 22 16.92z"/></svg>
      <span><?= $s('phone') ?></span>
    </a>
    <a href="mailto:<?= $s('email') ?>" class="topbar-link topbar-email">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="m22 6-10 7L2 6"/></svg>
      <span><?= $s('email') ?></span>
    </a>
  </div>
</div>

<header class="site-header">
  <div class="container header-inner">
    <a href="<?= e(asset_path('index.php')) ?>" class="logo"><?= $s('site_name') ?> <span><?= $s('site_tagline') ?></span></a>
    <nav class="main-nav" id="mainNav">
      <a href="<?= e(asset_path('index.php')) ?>"     class="<?= ($activePage ?? '') === 'home'      ? 'is-active' : '' ?>">الرئيسية</a>
      <a href="<?= e(asset_path('about.php')) ?>"     class="<?= ($activePage ?? '') === 'about'     ? 'is-active' : '' ?>">من نحن</a>
      <a href="<?= e(asset_path('services.php')) ?>"  class="<?= ($activePage ?? '') === 'services'  ? 'is-active' : '' ?>">الخدمات</a>
      <a href="<?= e(asset_path('portfolio.php')) ?>" class="<?= ($activePage ?? '') === 'portfolio' ? 'is-active' : '' ?>">أعمالنا</a>
      <a href="<?= e(asset_path('contact.php')) ?>"   class="<?= ($activePage ?? '') === 'contact'   ? 'is-active' : '' ?>">تواصل معنا</a>
    </nav>
    <a href="tel:<?= e($phone_href) ?>" class="btn btn-accent header-cta">اتصل الآن</a>
    <button class="nav-toggle" id="navToggle" aria-label="فتح القائمة" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
