<?php
// أي زيارة لـ /admin مباشرة تُوجَّه تلقائيًا لصفحة الدخول
// (login.php نفسها تتحقق إن كانت هناك جلسة مفتوحة أو تحتاج تثبيت أول)
require_once __DIR__ . '/../includes/functions.php';
redirect('login.php');
