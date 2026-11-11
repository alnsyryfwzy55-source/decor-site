<?php
require_once __DIR__ . '/includes/functions.php';

$activePage    = 'portfolio';
$canonicalPath = 'portfolio.php';
$pageTitle     = 'أعمالنا | ' . get_setting($pdo, 'site_name');
$pageDescription = 'استعرض مشاريعنا المنفذة في التصميم الداخلي: صالات، مطابخ، غرف نوم، ومكاتب.';

$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();

$catSlug = $_GET['cat'] ?? '';
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 9;
$offset  = ($page - 1) * $perPage;

$where  = "WHERE p.status = 'published'";
$params = [];
if ($catSlug !== '') {
    $where .= ' AND c.slug = ?';
    $params[] = $catSlug;
}

$countStmt = $pdo->prepare("SELECT COUNT(*) c FROM projects p LEFT JOIN categories c ON c.id = p.category_id $where");
$countStmt->execute($params);
$total = (int) $countStmt->fetch()['c'];
$totalPages = max(1, (int) ceil($total / $perPage));

$sql = "SELECT p.*, c.slug AS cat_slug, c.name AS cat_name
        FROM projects p LEFT JOIN categories c ON c.id = p.category_id
        $where
        ORDER BY p.is_featured DESC, p.created_at DESC
        LIMIT $perPage OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

require __DIR__ . '/includes/site-header.php';

function portfolio_page_url($catSlug, $page)
{
    $q = [];
    if ($catSlug !== '') $q['cat'] = $catSlug;
    if ($page > 1) $q['page'] = $page;
    return 'portfolio.php' . ($q ? '?' . http_build_query($q) : '');
}
?>
<main>
  <nav class="breadcrumb container" aria-label="مسار التصفح">
    <a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span><span>أعمالنا</span>
  </nav>

  <section class="page-header container">
    <p class="eyebrow">معرض الأعمال</p>
    <h1>مشاريع تحمل توقيعنا</h1>
    <p>كل مشروع له طابعه الخاص — تصفّح أعمالنا حسب نوع المساحة.</p>
  </section>

  <section class="portfolio" style="padding-top:0;">
    <div class="container">
      <div class="filters">
        <a href="<?= e(portfolio_page_url('', 1)) ?>" class="filter-btn <?= $catSlug === '' ? 'active' : '' ?>">الكل</a>
        <?php foreach ($categories as $cat): ?>
          <a href="<?= e(portfolio_page_url($cat['slug'], 1)) ?>" class="filter-btn <?= $catSlug === $cat['slug'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
        <?php endforeach; ?>
      </div>

      <div class="gallery">
        <?php foreach ($projects as $i => $p): ?>
        <article class="gallery-item<?= $i === 0 && $page === 1 ? ' tall' : '' ?>">
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
        <?php if (empty($projects)): ?><p>لا توجد أعمال ضمن هذا التصنيف حاليًا.</p><?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
      <nav class="pagination" aria-label="ترقيم الصفحات">
        <?php if ($page > 1): ?><a href="<?= e(portfolio_page_url($catSlug, $page - 1)) ?>">‹ السابق</a><?php endif; ?>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php if ($i === $page): ?><span class="current"><?= $i ?></span>
          <?php else: ?><a href="<?= e(portfolio_page_url($catSlug, $i)) ?>"><?= $i ?></a><?php endif; ?>
        <?php endfor; ?>
        <?php if ($page < $totalPages): ?><a href="<?= e(portfolio_page_url($catSlug, $page + 1)) ?>">التالي ›</a><?php endif; ?>
      </nav>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
