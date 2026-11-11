<?php
require_once __DIR__ . '/includes/functions.php';

$activePage    = 'contact';
$canonicalPath = 'contact.php';
$pageTitle     = 'تواصل معنا | ' . get_setting($pdo, 'site_name');
$pageDescription = 'تواصل معنا عبر الهاتف أو واتساب أو البريد الإلكتروني، أو أرسل رسالتك مباشرة وسنعاود الاتصال بك.';

$mapEmbed = get_setting($pdo, 'map_embed_url');

require __DIR__ . '/includes/site-header.php';
?>
<main class="contact-page">
  <nav class="breadcrumb container" aria-label="مسار التصفح">
    <a href="<?= e(asset_path('index.php')) ?>">الرئيسية</a><span>/</span><span>تواصل معنا</span>
  </nav>

  <section class="page-header container">
    <p class="eyebrow">لنبدأ مشروعك</p>
    <h1>تواصل معنا</h1>
    <p>راسلنا أو اتصل مباشرة، ونرد عليك خلال ساعات العمل.</p>
  </section>

  <section class="contact" style="padding-top:0;">
    <div class="container contact-inner">
      <div class="contact-info">
        <ul class="contact-list">
          <li><a href="tel:<?= e($phone_href) ?>">📞 <?= $s('phone') ?></a></li>
          <li><a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" target="_blank" rel="noopener">💬 واتساب: <?= $s('phone') ?></a></li>
          <li><a href="mailto:<?= $s('email') ?>">✉️ <?= $s('email') ?></a></li>
          <li>📍 <?= $s('address') ?></li>
        </ul>

        <?php if ($mapEmbed): ?>
          <div class="map-embed" style="margin-top:24px;">
            <iframe src="<?= e($mapEmbed) ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="الموقع على الخريطة"></iframe>
          </div>
        <?php endif; ?>
      </div>

      <form class="contact-form" id="contactForm">
        <label>الاسم<input type="text" name="name" required maxlength="120"></label>
        <label>رقم الهاتف<input type="tel" name="phone" required maxlength="40"></label>
        <label>رسالتك<textarea name="message" rows="4" required maxlength="3000"></textarea></label>
        <button type="submit" class="btn btn-accent">إرسال الطلب</button>
        <p class="form-status" id="formStatus" role="status"></p>
      </form>
    </div>
  </section>
</main>
<?php require __DIR__ . '/includes/site-footer.php'; ?>
