<?php
require_once __DIR__ . '/includes/functions.php';
header('Content-Type: text/plain; charset=utf-8');
?>
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /config/
Disallow: /includes/
Disallow: /database/
Disallow: /api/
Disallow: /uploads/portfolio/README.txt

Sitemap: <?= full_url('sitemap.php') ?>
