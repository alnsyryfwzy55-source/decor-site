<?php
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'إعدادات الموقع';
$activeNav = 'settings';

$fields = [
    'site_name'        => 'اسم الموقع/الشعار النصي',
    'site_tagline'      => 'الوصف المختصر أسفل الاسم',
    'meta_title'        => 'عنوان الصفحة في نتائج البحث (SEO)',
    'meta_description'  => 'وصف الصفحة في نتائج البحث (SEO)',
    'phone'             => 'رقم الهاتف الظاهر للزوار',
    'whatsapp'          => 'رقم واتساب (بدون + وبدون مسافات، مثال: 970599000000)',
    'email'             => 'البريد الإلكتروني',
    'address'           => 'العنوان / المنطقة',
    'instagram_url'     => 'رابط إنستغرام',
    'facebook_url'      => 'رابط فيسبوك',
    'hero_title'        => 'العنوان الرئيسي في الصفحة الأولى',
    'hero_desc'         => 'الوصف تحت العنوان الرئيسي',
    'about_title'       => 'عنوان قسم "من نحن"',
    'about_text'        => 'نص قسم "من نحن"',
    'stat1_value'       => 'رقم الإحصائية 1', 'stat1_label' => 'وصف الإحصائية 1',
    'stat2_value'       => 'رقم الإحصائية 2', 'stat2_label' => 'وصف الإحصائية 2',
    'stat3_value'       => 'رقم الإحصائية 3', 'stat3_label' => 'وصف الإحصائية 3',
    'why_us_1_title'    => 'عنوان ميزة 1 (صفحة من نحن)', 'why_us_1_text' => 'نص ميزة 1',
    'why_us_2_title'    => 'عنوان ميزة 2 (صفحة من نحن)', 'why_us_2_text' => 'نص ميزة 2',
    'why_us_3_title'    => 'عنوان ميزة 3 (صفحة من نحن)', 'why_us_3_text' => 'نص ميزة 3',
    'map_embed_url'     => 'رابط تضمين خرائط جوجل (من Google Maps: مشاركة ← تضمين خريطة ← انسخ رابط src فقط)',
    'footer_text'       => 'نص أسفل الموقع (حقوق الملكية)',
];
$textareaFields = ['meta_description', 'hero_desc', 'about_text', 'why_us_1_text', 'why_us_2_text', 'why_us_3_text'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    foreach ($fields as $key => $label) {
        if (isset($_POST[$key])) {
            set_setting($pdo, $key, trim($_POST[$key]));
        }
    }
    flash_set('success', 'تم حفظ الإعدادات بنجاح');
    redirect('settings.php');
}

$settings = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll(PDO::FETCH_KEY_PAIR);

require __DIR__ . '/includes/header.php';
?>

<div class="panel">
  <form method="post" class="admin-form settings-form">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <?php foreach ($fields as $key => $label): ?>
      <label>
        <?= e($label) ?>
        <?php if (in_array($key, $textareaFields, true)): ?>
          <textarea name="<?= $key ?>" rows="3"><?= e($settings[$key] ?? '') ?></textarea>
        <?php else: ?>
          <input type="text" name="<?= $key ?>" value="<?= e($settings[$key] ?? '') ?>">
        <?php endif; ?>
      </label>
    <?php endforeach; ?>
    <div class="form-actions">
      <button type="submit" class="btn-admin">حفظ الإعدادات</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
