<?php
require_once __DIR__ . '/includes/functions.php';

$activePage    = 'home';
$canonicalPath = '';

$featuredProjects = $pdo->query(
    "SELECT p.*, c.slug AS cat_slug, c.name AS cat_name
     FROM projects p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.status = 'published'
     ORDER BY p.is_featured DESC, p.created_at DESC LIMIT 4"
)->fetchAll();
$services = $pdo->query('SELECT * FROM services ORDER BY sort_order LIMIT 4')->fetchAll();

require __DIR__ . '/includes/site-header.php';
?>
<main>
  <section class="hero">
    <div class="container hero-inner">
      <div class="hero-text">
        <p class="eyebrow">تصميم داخلي · هندسة ديكور</p>
        <h1><?= nl2br($s('hero_title')) ?></h1>
        <p class="hero-desc"><?= $s('hero_desc') ?></p>
        <div class="hero-actions">
          <a href="<?= e(asset_path('portfolio.php')) ?>" class="btn btn-accent">شاهد أعمالنا</a>
          <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-ghost">احجز استشارة</a>
        </div>
      </div>
      <div class="hero-visual">
        <svg class="room-sketch" viewBox="0 0 480 420" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="رسم خطي لغرفة معيشة">
          <rect x="20" y="20" width="440" height="380" rx="4" stroke="var(--ink)" stroke-width="1.5" opacity="0.5"/>
          <line x1="20" y1="300" x2="460" y2="300" stroke="var(--ink)" stroke-width="1.5" opacity="0.5"/>
          <path d="M60 300 V120 H180 V300" stroke="var(--accent)" stroke-width="2"/>
          <path d="M75 300 V150 Q75 130 100 130 Q125 130 125 150 V300" stroke="var(--accent)" stroke-width="1.5"/>
          <rect x="230" y="230" width="150" height="70" rx="6" stroke="var(--ink)" stroke-width="2"/>
          <rect x="240" y="215" width="130" height="20" rx="4" stroke="var(--ink)" stroke-width="1.5"/>
          <circle cx="400" cy="90" r="26" stroke="var(--accent-2)" stroke-width="2"/>
          <line x1="400" y1="64" x2="400" y2="40" stroke="var(--accent-2)" stroke-width="1.5"/>
          <path d="M340 300 V240 Q340 220 360 220 Q380 220 380 240 V300" stroke="var(--ink)" stroke-width="1.2" opacity="0.6"/>
        </svg>
        <div class="moodboard" aria-hidden="true">
          <span class="swatch" style="--c:#9C7A46">نحاس معتّق</span>
          <span class="swatch" style="--c:#3A4A3E">أخضر غابي</span>
          <span class="swatch" style="--c:#B9AE9A">جص طبيعي</span>
          <span class="swatch" style="--c:#5C4530">خشب جوز</span>
        </div>
      </div>
    </div>
  </section>

  <section class="about">
    <div class="container about-inner">
      <div class="about-media" aria-hidden="true"><div class="frame"></div></div>
      <div class="about-text">
        <p class="eyebrow">من نحن</p>
        <h2><?= $s('about_title') ?></h2>
        <p><?= $s('about_text') ?></p>
        <div class="stats">
          <div class="stat"><strong><?= $s('stat1_value') ?></strong><span><?= $s('stat1_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat2_value') ?></strong><span><?= $s('stat2_label') ?></span></div>
          <div class="stat"><strong><?= $s('stat3_value') ?></strong><span><?= $s('stat3_label') ?></span></div>
        </div>
        <a href="<?= e(asset_path('about.php')) ?>" class="btn btn-ghost" style="margin-top:12px;">تعرّف علينا أكثر</a>
      </div>
    </div>
  </section>

  <section class="services">
    <div class="container">
      <p class="eyebrow center">ماذا نقدم</p>
      <h2 class="center">خدماتنا</h2>
      <div class="services-grid">
        <?php foreach ($services as $sv): ?>
        <article class="service-card">
          <div class="service-icon"><?= service_icon_svg($sv['icon']) ?></div>
          <h3><?= e($sv['title']) ?></h3>
          <p><?= e($sv['description']) ?></p>
        </article>
        <?php endforeach; ?>
      </div>
      <p class="center" style="margin-top:28px;"><a href="<?= e(asset_path('services.php')) ?>" class="btn btn-ghost">كل الخدمات</a></p>
    </div>
  </section>

  <section class="portfolio">
    <div class="container">
      <p class="eyebrow center">معرض الأعمال</p>
      <h2 class="center">مشاريع تحمل توقيعنا</h2>
      <div class="gallery">
        <?php foreach ($featuredProjects as $i => $p): ?>
        <article class="gallery-item<?= $i === 0 ? ' tall' : '' ?>">
          <a href="<?= e(asset_path('project.php?slug=' . $p['slug'])) ?>">
            <?php if ($p['cover_image']): ?>
              <img class="portfolio-img" src="<?= e(asset_path('uploads/portfolio/' . $p['cover_image'])) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="portfolio-img" style="--tone:<?= placeholder_tone($p['id']) ?>"></div>
            <?php endif; ?>
            <div class="gallery-caption"><span><?= e($p['title']) ?></span><small><?= e($p['location']) ?></small></div>
          </a>
        </article>
        <?php endforeach; ?>
        <?php if (empty($featuredProjects)): ?>
          <p>لم تتم إضافة أعمال بعد — أضف أول مشروع من لوحة التحكم.</p>
        <?php endif; ?>
      </div>
      <p class="center" style="margin-top:28px;"><a href="<?= e(asset_path('portfolio.php')) ?>" class="btn btn-ghost">شاهد كل الأعمال</a></p>
    </div>
  </section>

  <section class="process">
    <div class="container">
      <p class="eyebrow center">آلية العمل</p>
      <h2 class="center">من الفكرة إلى التسليم</h2>
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
      <h2>جاهز تبدأ مشروعك؟</h2>
      <p>احجز استشارة مجانية اليوم ونتواصل معك بأسرع وقت.</p>
      <a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">تواصل معنا الآن</a>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
