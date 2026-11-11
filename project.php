<?php
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare(
    "SELECT p.*, c.slug AS cat_slug, c.name AS cat_name
     FROM projects p LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.slug = ? AND p.status = 'published'"
);
$stmt->execute([$slug]);
$project = $stmt->fetch();

if (!$project) {
    http_response_code(404);
    $activePage = 'portfolio';
    $canonicalPath = 'portfolio.php';
    $pageTitle = 'المشروع غير موجود';
    require __DIR__ . '/includes/site-header.php';
    echo '<main><div class="container" style="padding:80px 0;text-align:center;">
            <h1>عذرًا، لم نجد هذا المشروع</h1>
            <p><a href="' . e(asset_path('portfolio.php')) . '" class="btn btn-accent">تصفّح كل الأعمال</a></p>
          </div></main>';
    require __DIR__ . '/includes/site-footer.php';
    exit;
}

$galleryImages = get_project_images($pdo, $project['id']);

$related = $pdo->prepare(
    "SELECT * FROM projects
     WHERE status = 'published' AND id != ? AND (category_id = ? OR ? IS NULL)
     ORDER BY created_at DESC LIMIT 3"
);
$related->execute([$project['id'], $project['category_id'], $project['category_id']]);
$relatedProjects = $related->fetchAll();

$activePage       = 'portfolio';
$canonicalPath    = 'project.php?slug=' . $project['slug'];
$pageTitle        = $project['title'] . ($project['location'] ? ' - ' . $project['location'] : '') . ' | ' . get_setting($pdo, 'site_name');
$pageDescription  = $project['description'] ? mb_substr(strip_tags($project['description']), 0, 160) : ('مشروع ' . $project['title'] . ' من تنفيذ ' . get_setting($pdo, 'site_name'));

$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'الرئيسية', 'item' => full_url()],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'أعمالنا', 'item' => full_url('portfolio.php')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $project['title'], 'item' => full_url('project.php?slug=' . $project['slug'])],
    ],
];

require __DIR__ . '/includes/site-header.php';
?>
<main>
  <nav class="breadcrumb container" aria-label="مسار التصفح">
    <a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span>
    <a href="<?= e(asset_path('portfolio.php')) ?>">أعمالنا</a><span>/</span>
    <span><?= e($project['title']) ?></span>
  </nav>

  <section class="project-hero container">
    <?php if ($project['cover_image']): ?>
      <img class="project-hero-img" src="<?= e(asset_path('uploads/portfolio/' . $project['cover_image'])) ?>" alt="<?= e($project['title']) ?>">
    <?php else: ?>
      <div class="project-hero-img" style="--tone:<?= placeholder_tone($project['id']) ?>;background:linear-gradient(160deg,var(--tone),color-mix(in srgb,var(--tone) 60%,black));"></div>
    <?php endif; ?>

    <h1 style="margin-top:24px;"><?= e($project['title']) ?></h1>
    <div class="project-meta">
      <?php if ($project['cat_name']): ?><span>التصنيف: <strong><?= e($project['cat_name']) ?></strong></span><?php endif; ?>
      <?php if ($project['location']): ?><span>الموقع: <strong><?= e($project['location']) ?></strong></span><?php endif; ?>
    </div>
    <?php if ($project['description']): ?><p><?= nl2br(e($project['description'])) ?></p><?php endif; ?>

    <?php if (!empty($galleryImages)): ?>
    <div class="project-gallery">
      <?php foreach ($galleryImages as $img): ?>
        <img src="<?= e(asset_path('uploads/portfolio/' . $img['image_path'])) ?>" alt="<?= e($project['title']) ?> - صورة إضافية" loading="lazy">
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <p style="margin-top:32px;"><a href="<?= e(asset_path('contact.php')) ?>" class="btn btn-accent">أعجبني هذا الأسلوب — تواصل معي</a></p>
  </section>

  <?php if (!empty($relatedProjects)): ?>
  <section class="related-projects">
    <div class="container">
      <p class="eyebrow center">أعمال ذات صلة</p>
      <h2 class="center">قد يعجبك أيضًا</h2>
      <div class="gallery">
        <?php foreach ($relatedProjects as $rp): ?>
        <article class="gallery-item">
          <a href="<?= e(asset_path('project.php?slug=' . $rp['slug'])) ?>">
            <?php if ($rp['cover_image']): ?>
              <img class="portfolio-img" src="<?= e(asset_path('uploads/portfolio/' . $rp['cover_image'])) ?>" alt="<?= e($rp['title']) ?>" loading="lazy">
            <?php else: ?>
              <div class="portfolio-img" style="--tone:<?= placeholder_tone($rp['id']) ?>"></div>
            <?php endif; ?>
            <div class="gallery-caption"><span><?= e($rp['title']) ?></span><small><?= e($rp['location']) ?></small></div>
          </a>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
