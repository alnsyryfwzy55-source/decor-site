<?php
require_once __DIR__ . '/../config/db.php';

/* ========================================================
   دوال عامة
   ======================================================== */

/** طباعة نص آمن ضد XSS */
function e($str)
{
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/** تحويل نص عربي/إنجليزي إلى slug صالح للاستخدام في الروابط */
function slugify($text)
{
    $text = trim($text);
    $text = preg_replace('/[^\p{L}\p{N}]+/u', '-', $text);
    $text = trim($text, '-');
    $text = mb_strtolower($text, 'UTF-8');
    if ($text === '') {
        $text = 'item-' . time();
    }
    return $text;
}

/** التأكد من عدم تكرار slug داخل جدول معيّن (يضيف -2, -3 عند التكرار) */
function unique_slug(PDO $pdo, $table, $slug, $ignoreId = null)
{
    $base = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM `$table` WHERE slug = ?";
        $params = [$slug];
        if ($ignoreId) {
            $sql .= ' AND id != ?';
            $params[] = $ignoreId;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        if (!$stmt->fetch()) {
            return $slug;
        }
        $slug = $base . '-' . $i;
        $i++;
    }
}

function redirect($url)
{
    header('Location: ' . $url);
    exit;
}

/** رسائل تنبيه مؤقتة تُعرض مرة واحدة بعد إعادة التوجيه (Flash Messages) */
function flash_set($type, $message)
{
    $_SESSION['flash'][$type] = $message;
}

function flash_get($type)
{
    $msg = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $msg;
}

/* ========================================================
   الإعدادات العامة (جدول settings)
   ======================================================== */

function get_all_settings(PDO $pdo)
{
    static $cache = null;
    if ($cache !== null) {
        return $cache;
    }
    $cache = [];
    foreach ($pdo->query('SELECT setting_key, setting_value FROM settings') as $row) {
        $cache[$row['setting_key']] = $row['setting_value'];
    }
    return $cache;
}

function get_setting(PDO $pdo, $key, $default = '')
{
    $all = get_all_settings($pdo);
    return $all[$key] ?? $default;
}

function set_setting(PDO $pdo, $key, $value)
{
    $stmt = $pdo->prepare(
        'INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
         ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
    );
    $stmt->execute([$key, $value]);
}

/* ========================================================
   الحماية: تسجيل الدخول و CSRF
   ======================================================== */

function require_login()
{
    if (empty($_SESSION['admin_id'])) {
        redirect('login.php');
    }
}

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        die('⚠️ انتهت صلاحية الجلسة أو الطلب غير صالح. أعد تحميل الصفحة وحاول مجددًا.');
    }
}

/* ========================================================
   رفع الصور وضغطها تلقائيًا إلى WebP (لتسريع الموقع)
   ======================================================== */

/**
 * يرفع صورة من $_FILES، يصغّرها إذا لزم، ويحوّلها إلى WebP مضغوط.
 * يرجع اسم الملف الناتج (مثال: img_65abf12.webp) أو null إذا لم يُرفع ملف.
 * يرمي Exception عند وجود خطأ (حجم كبير / صيغة غير مدعومة).
 */
function upload_image_as_webp($fileInputName, $targetDir, $maxWidth = null)
{
    if (empty($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // لم يختر المستخدم ملفًا - ليس خطأ
    }

    // فحص مبكر لمكتبة GD لتفادي Fatal Error غير مفهوم للمستخدم
    if (!extension_loaded('gd')) {
        throw new Exception(
            'مكتبة معالجة الصور (GD) غير مفعّلة في إعدادات PHP على السيرفر. ' .
            'في XAMPP: افتح php.ini وأزل الفاصلة المنقوطة قبل ";extension=gd" ثم أعد تشغيل Apache.'
        );
    }

    $file = $_FILES[$fileInputName];
    $maxWidth = $maxWidth ?: MAX_IMAGE_WIDTH;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('حدث خطأ أثناء رفع الصورة (كود: ' . $file['error'] . ')');
    }

    if ($file['size'] > MAX_UPLOAD_SIZE) {
        $mb = round(MAX_UPLOAD_SIZE / 1024 / 1024, 1);
        throw new Exception("حجم الصورة أكبر من الحد المسموح ({$mb} ميجابايت)");
    }

    $info = @getimagesize($file['tmp_name']);
    if (!$info) {
        throw new Exception('الملف المرفوع ليس صورة صالحة');
    }

    switch ($info['mime']) {
        case 'image/jpeg':
            if (!function_exists('imagecreatefromjpeg')) {
                throw new Exception('السيرفر لا يدعم معالجة صور JPG حاليًا (دعم GD-JPEG غير مفعّل).');
            }
            $src = imagecreatefromjpeg($file['tmp_name']);
            break;
        case 'image/png':
            if (!function_exists('imagecreatefrompng')) {
                throw new Exception('السيرفر لا يدعم معالجة صور PNG حاليًا (دعم GD-PNG غير مفعّل).');
            }
            $src = imagecreatefrompng($file['tmp_name']);
            imagepalettetotruecolor($src);
            break;
        case 'image/webp':
            if (!function_exists('imagecreatefromwebp')) {
                throw new Exception('السيرفر لا يدعم معالجة صور WebP حاليًا (دعم GD-WebP غير مفعّل).');
            }
            $src = imagecreatefromwebp($file['tmp_name']);
            break;
        default:
            throw new Exception('صيغة الصورة غير مدعومة. الرجاء استخدام JPG أو PNG أو WebP');
    }

    if (!$src) {
        throw new Exception('تعذّر قراءة الصورة، جرّب ملفًا آخر');
    }

    // الحفاظ على الشفافية عند PNG
    imagealphablending($src, true);
    imagesavealpha($src, true);

    // تصغير الصورة إن كانت أعرض من الحد الأقصى (تسريع + تقليل الحجم)
    $width  = imagesx($src);
    $height = imagesy($src);
    if ($width > $maxWidth) {
        $newHeight = (int) round($height * ($maxWidth / $width));
        $resized = imagecreatetruecolor($maxWidth, $newHeight);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $src, 0, 0, 0, 0, $maxWidth, $newHeight, $width, $height);
        imagedestroy($src);
        $src = $resized;
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    if (function_exists('imagewebp')) {
        $filename = uniqid('img_', true) . '.webp';
        $fullPath = rtrim($targetDir, '/') . '/' . $filename;
        imagewebp($src, $fullPath, WEBP_QUALITY);
    } else {
        // احتياطي إن لم يدعم السيرفر WebP (نادر الحدوث)
        $filename = uniqid('img_', true) . '.jpg';
        $fullPath = rtrim($targetDir, '/') . '/' . $filename;
        imagejpeg($src, $fullPath, WEBP_QUALITY);
    }

    imagedestroy($src);
    return $filename;
}

/**
 * يرفع عدة صور دفعة واحدة (لمعرض صور المشروع) ويحوّل كل واحدة إلى WebP.
 * يرجع مصفوفة بأسماء الملفات الناتجة.
 */
function upload_multiple_images_as_webp($fileInputName, $targetDir)
{
    if (empty($_FILES[$fileInputName]) || empty($_FILES[$fileInputName]['name'][0])) {
        return [];
    }

    $count = count($_FILES[$fileInputName]['name']);
    $uploaded = [];

    for ($i = 0; $i < $count; $i++) {
        if ($_FILES[$fileInputName]['error'][$i] === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        // نعيد استخدام upload_image_as_webp عبر إدخال مؤقت شبيه بـ $_FILES العادي
        $_FILES['__gallery_tmp__'] = [
            'name'     => $_FILES[$fileInputName]['name'][$i],
            'type'     => $_FILES[$fileInputName]['type'][$i],
            'tmp_name' => $_FILES[$fileInputName]['tmp_name'][$i],
            'error'    => $_FILES[$fileInputName]['error'][$i],
            'size'     => $_FILES[$fileInputName]['size'][$i],
        ];
        $filename = upload_image_as_webp('__gallery_tmp__', $targetDir);
        unset($_FILES['__gallery_tmp__']);
        if ($filename) {
            $uploaded[] = $filename;
        }
    }

    return $uploaded;
}

function delete_uploaded_file($targetDir, $filename)
{
    if (!$filename) {
        return;
    }
    $path = rtrim($targetDir, '/') . '/' . $filename;
    if (is_file($path)) {
        @unlink($path);
    }
}

/* ========================================================
   معرض صور المشروع (project_images)
   ======================================================== */
function get_project_images(PDO $pdo, $projectId)
{
    $stmt = $pdo->prepare('SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order, id');
    $stmt->execute([$projectId]);
    return $stmt->fetchAll();
}

/* ========================================================
   أدوات SEO
   ======================================================== */

/** رابط كامل صحيح لصفحة داخل الموقع (يُستخدم في canonical و sitemap) */
function full_url($path = '')
{
    return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
}

/** مسار نسبي لأي ملف داخل جذر الموقع (كل الصفحات بمستوى واحد حاليًا) */
function asset_path($path)
{
    return ltrim($path, '/');
}

/* ========================================================
   الأيقونات
   ======================================================== */

function service_icon_svg($key)
{
    $icons = [
        'design' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 21h18M4 21V8l8-5 8 5v13M9 21v-6h6v6"/></svg>',
        'tools' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.77z"/></svg>',
        'furniture' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V4h8v3"/></svg>',
        'chat' => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
    ];
    return $icons[$key] ?? $icons['design'];
}

/** ألوان احتياطية لعرض بطاقات الأعمال التي لا تملك صورة بعد */
function placeholder_tone($seed)
{
    $tones = ['#9C7A46', '#3A4A3E', '#6E5A3C', '#4A4038', '#5C4530', '#2F4538'];
    return $tones[$seed % count($tones)];
}
