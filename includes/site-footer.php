<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-col">
      <div class="logo"><?= $s('site_name') ?> <span><?= $s('site_tagline') ?></span></div>
      <p class="footer-desc"><?= $s('site_tagline') ?> — <?= $s('address') ?></p>
    </div>
    <div class="footer-col">
      <h4>روابط سريعة</h4>
      <a href="<?= e(asset_path('about.php')) ?>">من نحن</a>
      <a href="<?= e(asset_path('services.php')) ?>">الخدمات</a>
      <a href="<?= e(asset_path('portfolio.php')) ?>">أعمالنا</a>
      <a href="<?= e(asset_path('contact.php')) ?>">تواصل معنا</a>
    </div>
    <div class="footer-col">
      <h4>تواصل معنا</h4>
      <a href="tel:<?= e($phone_href) ?>"><?= $s('phone') ?></a>
      <a href="mailto:<?= $s('email') ?>"><?= $s('email') ?></a>
      <div class="social">
        <a href="<?= e($settings['instagram_url'] ?? '#') ?>" target="_blank" rel="noopener">Instagram</a>
        <a href="<?= e($settings['facebook_url'] ?? '#') ?>" target="_blank" rel="noopener">Facebook</a>
      </div>
    </div>
  </div>
  <div class="container footer-bottom"><?= $s('footer_text') ?></div>
</footer>

<a href="https://wa.me/<?= e($settings['whatsapp'] ?? '') ?>" class="float-whatsapp" target="_blank" rel="noopener" aria-label="تواصل عبر واتساب">
  <svg width="26" height="26" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.9-2-1-.3-.1-.5-.1-.6.1-.2.3-.7 1-.9 1.2-.2.2-.3.2-.6.1-.3-.1-1.3-.5-2.4-1.5-.9-.8-1.5-1.8-1.7-2.1-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.1.2-.3.2-.4.1-.2 0-.4 0-.5-.1-.1-.6-1.4-.8-1.9-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.5.1-.7.3-.2.3-.9.9-.9 2.1s.9 2.4 1 2.6c.1.2 1.8 2.8 4.4 3.9.6.3 1.1.4 1.5.5.6.2 1.2.2 1.6.1.5-.1 1.7-.7 1.9-1.3.2-.6.2-1.2.2-1.3-.1-.1-.3-.2-.5-.3zM12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2z"/></svg>
</a>

<script src="<?= e(asset_path('js/script.js')) ?>"></script>
</body>
</html>
