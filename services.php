<?php
require_once __DIR__ . '/includes/functions.php';

$activePage    = 'services';
$canonicalPath = 'services.php';
$pageTitle     = 'خدماتنا | ' . get_setting($pdo, 'site_name');
$pageDescription = 'تعرّف على خدمات التصميم الداخلي والإشراف على التنفيذ والأثاث والاستشارات التي نقدمها.';

$services = $pdo->query('SELECT * FROM services ORDER BY sort_order')->fetchAll();

require __DIR__ . '/includes/site-header.php';
?>
<main>
  <nav class="breadcrumb container" aria-label="مسار التصفح">
    <a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span><span>الخدمات</span>
  </nav>

  <section class="page-header container">
    <p class="eyebrow">ماذا نقدم</p>
    <h1>خدماتنا</h1>
    <p>من الفكرة الأولى وحتى تسليم المفتاح، نرافقك في كل مرحلة من مراحل تحويل مساحتك.</p>
  </section>

  <section class="services" style="padding-top:0;">
    <div class="container">
      <div class="services-grid">
        <?php foreach ($services as $sv): ?>
        <article class="service-card">
          <div class="service-icon"><?= service_icon_svg($sv['icon']) ?></div>
          <h3><?= e($sv['title']) ?></h3>
          <p><?= e($sv['description']) ?></p>
        </article>
        <?php endforeach; ?>
        <?php if (empty($services)): ?><p>لم تتم إضافة خدمات بعد.</p><?php endif; ?>
      </div>
    </div>
  </section>

  <section class="process">
    <div class="container">
      <p class="eyebrow center">آلية العمل</p>
      <h2 class="center">كيف نبدأ معك؟</h2>
      <div class="steps">
        <div class="step"><span class="step-num">01</span><h3>استشارة أولية</h3><p>نزور المساحة ونفهم احتياجاتك وميزانيتك.</p></div>
        <div class="step"><span class="step-num">02</span><h3>التصميم والمخطط</h3><p>نجهز المخططات ولوحة الخامات والألوان.</p></div>
        <div class="step"><span class="step-num">03</span><h3>التنفيذ</h3><p>إشراف يومي على فريق التنفيذ حتى أدق التفاصيل.</p></div>
        <div class="step"><span class="step-num">04</span><h3>التسليم</h3><p>جولة تسليم نهائية والتأكد من رضاك التام.</p></div>
      </div>
    </div>
  </section>

  <section class="cta-band">
    <div class="container cta-band-inner">
      <h2>مهتم بإحدى خدماتنا؟</h2>
      <p>راسلنا وسنرد عليك بتفاصيل الأسعار والخطوات القادمة.</p>
      <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">اطلب الخدمة</a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
