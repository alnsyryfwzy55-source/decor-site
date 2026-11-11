<?php
require_once __DIR__ . '/includes/functions.php';

$activePage    = 'about';
$canonicalPath = 'about.php';
$pageTitle     = get_setting($pdo, 'about_title') . ' | ' . get_setting($pdo, 'site_name');
$pageDescription = mb_substr(strip_tags(get_setting($pdo, 'about_text')), 0, 160);

require __DIR__ . '/includes/site-header.php';
?>
<main>
  <nav class="breadcrumb container" aria-label="مسار التصفح">
    <a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span><span>من نحن</span>
  </nav>

  <section class="page-header container">
    <p class="eyebrow">من نحن</p>
    <h1><?= $s('about_title') ?></h1>
    <p><?= $s('about_text') ?></p>
  </section>

  <section class="about">
    <div class="container about-inner">
      <div class="about-media" aria-hidden="true"><div class="frame"></div></div>
      <div class="about-text">
        <p class="eyebrow">لماذا تختارنا</p>
        <h2><?= $s('why_us_1_title', 'خبرة موثوقة') ?></h2>
        <p><?= $s('why_us_1_text') ?></p>
        <div class="stats">
          <div class="stat"><strong><?= $s('stat1_value') ?></strong><span><?= $s('stat1_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat2_value') ?></strong><span><?= $s('stat2_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat3_value') ?></strong><span><?= $s('stat3_label') ?></span></div>
        </div>
      </div>
    </div>
  </section>

  <section class="services">
    <div class="container">
      <p class="eyebrow center">لماذا نحن</p>
      <h2 class="center">ما يميزنا</h2>
      <div class="services-grid">
        <article class="service-card">
          <h3><?= $s('why_us_1_title', 'خبرة موثوقة') ?></h3>
          <p><?= $s('why_us_1_text', 'سنوات من الخبرة في تصميم وتنفيذ مساحات متنوعة.') ?></p>
        </article>
        <article class="service-card">
          <h3><?= $s('why_us_2_title', 'إشراف مباشر') ?></h3>
          <p><?= $s('why_us_2_text', 'نتابع كل مشروع بأنفسنا من أول يوم حتى التسليم.') ?></p>
        </article>
        <article class="service-card">
          <h3><?= $s('why_us_3_title', 'التزام بالمواعيد') ?></h3>
          <p><?= $s('why_us_3_text', 'نلتزم بالجدول الزمني المتفق عليه دون مفاجآت.') ?></p>
        </article>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container cta-band-inner">
      <h2>لنبدأ العمل على مساحتك</h2>
      <p>تواصل معنا اليوم واحصل على استشارة أولية.</p>
      <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">تواصل معنا</a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
