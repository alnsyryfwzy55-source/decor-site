<?php
require_once __DIR__ . '/config.php';

try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    if (APP_ENV === 'local') {
        die('❌ خطأ في الاتصال بقاعدة البيانات: ' . $e->getMessage() .
            '<br>تأكد أنك أنشأت قاعدة البيانات واستوردت ملف database/schema.sql');
    }
    die('عذرًا، حدث خطأ مؤقت في الموقع. حاول مرة أخرى لاحقًا.');
}
