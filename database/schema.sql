
CREATE DATABASE IF NOT EXISTS if0_41297933_decor_site
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE if0_41297933_decor_site;

-- ---------- المسؤولون (لوحة التحكم) ----------
CREATE TABLE admins (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    reset_token   VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME     DEFAULT NULL,
    failed_attempts INT DEFAULT 0,
    locked_until    DATETIME DEFAULT NULL,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- تصنيفات الأعمال ----------
CREATE TABLE categories (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100) NOT NULL,
    slug       VARCHAR(120) NOT NULL UNIQUE,
    sort_order INT DEFAULT 0
) ENGINE=InnoDB;

-- ---------- الأعمال / المشاريع ----------
CREATE TABLE projects (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(180) NOT NULL,
    slug         VARCHAR(200) NOT NULL UNIQUE,
    description  TEXT,
    location     VARCHAR(120),
    category_id  INT DEFAULT NULL,
    cover_image  VARCHAR(255) DEFAULT NULL,
    is_featured  TINYINT(1) DEFAULT 0,
    status       ENUM('published','draft') DEFAULT 'published',
    sort_order   INT DEFAULT 0,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------- صور إضافية لكل مشروع (معرض صور المشروع) ----------
CREATE TABLE project_images (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    project_id  INT NOT NULL,
    image_path  VARCHAR(255) NOT NULL,
    sort_order  INT DEFAULT 0,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------- الخدمات ----------
CREATE TABLE services (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    icon        VARCHAR(30) DEFAULT 'design',
    sort_order  INT DEFAULT 0
) ENGINE=InnoDB;

-- ---------- رسائل نموذج التواصل ----------
CREATE TABLE messages (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120) NOT NULL,
    phone      VARCHAR(40)  NOT NULL,
    message    TEXT NOT NULL,
    is_read    TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------- إعدادات عامة (نصوص الموقع، الأرقام، السوشيال ميديا) ----------
CREATE TABLE settings (
    setting_key   VARCHAR(100) PRIMARY KEY,
    setting_value TEXT
) ENGINE=InnoDB;


-- ============================================================
--  بيانات ابتدائية (Seed Data)
-- ============================================================

INSERT INTO categories (name, slug, sort_order) VALUES
('صالات',    'salon',   1),
('مطابخ',    'kitchen', 2),
('غرف نوم',  'bedroom', 3),
('مكاتب',    'office',  4);

INSERT INTO services (title, description, icon, sort_order) VALUES
('التصميم الداخلي',   'مخططات ثلاثية الأبعاد ولوحات خامات تعكس ذوقك واحتياجاتك.',              'design',    1),
('الإشراف والتنفيذ',  'متابعة يومية لضمان مطابقة التنفيذ للمخطط بأعلى جودة.',                    'tools',     2),
('الأثاث والتأثيث',    'اختيار وتوريد قطع الأثاث والإكسسوارات المناسبة للمساحة.',                'furniture', 3),
('استشارات ديكور',    'جلسة استشارية لتقييم مساحتك وتقديم توصيات عملية وسريعة.',                 'chat',      4);

-- مشروعان تجريبيان (بدون صورة بعد - أضف الصور الحقيقية من لوحة التحكم)
INSERT INTO projects (title, slug, description, location, category_id, is_featured, status) VALUES
('صالة استقبال حديثة', 'modern-majlis-demo',  'مشروع تجريبي — عدّله أو احذفه من لوحة التحكم.', 'عمّان',    1, 1, 'published'),
('مطبخ عصري',          'modern-kitchen-demo', 'مشروع تجريبي — عدّله أو احذفه من لوحة التحكم.', 'رام الله', 2, 0, 'published');

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name',         'أَثَر'),
('site_tagline',      'للتصميم الداخلي'),
('meta_title',        'أَثَر للتصميم الداخلي | مهندس ديكور وتصميم داخلي'),
('meta_description',  'مكتب أَثَر للتصميم الداخلي — تصميم وتنفيذ الشقق والفلل والمكاتب. تواصل معنا مباشرة.'),
('phone',              '0900000000'),
('whatsapp',           '970000000000'),
('email',              'info@athar-design.com'),
('address',            'فلسطين'),
('instagram_url',      '#'),
('facebook_url',       '#'),
('hero_title',         'مساحتك تحكي حكايتك'),
('hero_desc',          'نحوّل الشقق والمكاتب والفلل إلى مساحات تعكس شخصيتك، من المخطط الأول وحتى آخر لمسة تنفيذ.'),
('about_title',        'هندسة تفاصيل، لا مجرد ديكور'),
('about_text',         'أَثَر مكتب متخصص بالتصميم الداخلي والإشراف على التنفيذ، نؤمن أن كل مساحة لها طابعها الخاص، ونعمل معك خطوة بخطوة من الفكرة الأولى حتى التسليم النهائي.'),
('stat1_value', '+8'),   ('stat1_label', 'سنوات خبرة'),
('stat2_value', '+120'), ('stat2_label', 'مشروع منجز'),
('stat3_value', '100%'), ('stat3_label', 'إشراف مباشر'),
('why_us_1_title', 'خبرة موثوقة'),  ('why_us_1_text', 'سنوات من الخبرة في تصميم وتنفيذ مساحات متنوعة بأساليب مختلفة.'),
('why_us_2_title', 'إشراف مباشر'),  ('why_us_2_text', 'نتابع كل مشروع بأنفسنا من أول يوم حتى التسليم النهائي.'),
('why_us_3_title', 'التزام بالمواعيد'), ('why_us_3_text', 'نلتزم بالجدول الزمني المتفق عليه دون مفاجآت أو تأخير.'),
('map_embed_url', ''),
('footer_text',        '© 2026 أَثَر للتصميم الداخلي');

-- ملاحظة: لا يوجد حساب مسؤول افتراضي بكلمة مرور ثابتة (لأسباب أمنية).
-- عند أول زيارة لصفحة admin/login.php سيتم توجيهك تلقائيًا لصفحة
-- admin/install.php لإنشاء أول حساب مسؤول باسم مستخدم وكلمة مرور من اختيارك.
