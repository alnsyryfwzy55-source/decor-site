<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: application/xml; charset=utf-8');

$staticPages = [
    ['loc' => 'index.php',    'priority' => '1.0'],
    ['loc' => 'about.php',    'priority' => '0.7'],
    ['loc' => 'services.php', 'priority' => '0.8'],
    ['loc' => 'portfolio.php','priority' => '0.9'],
    ['loc' => 'contact.php',  'priority' => '0.7'],
];

$projects = $pdo->query("SELECT slug, created_at FROM projects WHERE status = 'published'")->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <?php foreach ($staticPages as $p): ?>
  <url>
    <loc><?= e(full_url($p['loc'])) ?></loc>
    <priority><?= $p['priority'] ?></priority>
  </url>
  <?php endforeach; ?>
  <?php foreach ($projects as $proj): ?>
  <url>
    <loc><?= e(full_url('project.php?slug=' . $proj['slug'])) ?></loc>
    <lastmod><?= date('Y-m-d', strtotime($proj['created_at'])) ?></lastmod>
    <priority>0.6</priority>
  </url>
  <?php endforeach; ?>
</urlset>
