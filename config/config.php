<?php

// ---------- بيانات الاتصال بقاعدة البيانات ----------
define('DB_HOST', 'sql300.infinityfree.com');
define('DB_NAME', 'if0_41297933_decor_site');
define('DB_USER', 'if0_41297933');
define('DB_PASS', 'I5r0vQFvdiI');

// ---------- بيئة التشغيل ----------
define('APP_ENV', 'production');

// ---------- الرابط الأساسي للموقع (بدون / في النهاية) ----------
define('BASE_URL', 'https://athr.infinityfreeapp.com');

// ---------- إعدادات البريد الإلكتروني (لاسترجاع كلمة المرور) ----------
define('MAIL_FROM', 'no-reply@yoursite.com');
define('MAIL_FROM_NAME', 'لوحة التحكم');

// ---------- مفتاح سري خاص بكل عميل ----------
// غيّر هذه القيمة لأي نص عشوائي طويل مختلف عند تركيب الموقع لعميل جديد
define('APP_SECRET', 'fawzi-mohammed-naji-alnusairi-for-websited-2026-7');

// ---------- إعدادات رفع وضغط الصور ----------
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 ميجابايت كحد أقصى للصورة
define('WEBP_QUALITY', 80);                  // جودة ضغط WebP (0-100)
define('MAX_IMAGE_WIDTH', 1600);             // أقصى عرض للصورة بعد الرفع (px)

// ============================================================
// لا حاجة لتعديل ما بعد هذا السطر عادة
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', APP_ENV === 'local' ? '1' : '0');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Amman');

// ---------- شبكة أمان: يمنع "الشاشة البيضاء" عند أي خطأ غير متوقع ----------
set_exception_handler(function ($e) {
    error_log($e->getMessage() . ' في ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    if (APP_ENV === 'local') {
        echo '<div style="font-family:sans-serif;direction:rtl;background:#fee;border:1px solid #c00;padding:18px;margin:20px;border-radius:8px;">';
        echo '<b>⚠️ خطأ غير متوقع (وضع التطوير المحلي فقط):</b><br>';
        echo htmlspecialchars($e->getMessage()) . '<br>';
        echo '<small>' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . '</small>';
        echo '</div>';
    } else {
        echo 'عذرًا، حدث خطأ غير متوقع. حاول مرة أخرى لاحقًا.';
    }
});
