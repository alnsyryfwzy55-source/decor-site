<?php
require_once __DIR__ . '/includes/auth.php';

$id = $_GET['id'] ?? null;
$project = null;

if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if (!$project) {
        flash_set('error', 'المشروع غير موجود');
        redirect('projects.php');
    }
}

$pageTitle  = $project ? 'تعديل عمل' : 'إضافة عمل جديد';
$activeNav  = 'projects';
$categories = $pdo->query('SELECT * FROM categories ORDER BY sort_order')->fetchAll();
$errors     = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $location    = trim($_POST['location'] ?? '');
    $category_id = $_POST['category_id'] !== '' ? (int) $_POST['category_id'] : null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status      = $_POST['status'] === 'draft' ? 'draft' : 'published';

    if ($title === '') {
        $errors[] = 'عنوان المشروع مطلوب';
    }

    if (empty($errors)) {
        try {
            $coverImage = $project['cover_image'] ?? null;
            $uploadDir  = __DIR__ . '/../uploads/portfolio';

            $newImage = upload_image_as_webp('cover_image', $uploadDir);
            if ($newImage) {
                delete_uploaded_file($uploadDir, $coverImage);
                $coverImage = $newImage;
            }

            $slugBase = slugify($title);

            if ($project) {
                $slug = unique_slug($pdo, 'projects', $slugBase, $project['id']);
                $stmt = $pdo->prepare(
                    'UPDATE projects SET title=?, slug=?, description=?, location=?, category_id=?, cover_image=?, is_featured=?, status=? WHERE id=?'
                );
                $stmt->execute([$title, $slug, $description, $location, $category_id, $coverImage, $is_featured, $status, $project['id']]);
                $projectId = $project['id'];
                flash_set('success', 'تم تحديث المشروع بنجاح');
            } else {
                $slug = unique_slug($pdo, 'projects', $slugBase);
                $stmt = $pdo->prepare(
                    'INSERT INTO projects (title, slug, description, location, category_id, cover_image, is_featured, status) VALUES (?,?,?,?,?,?,?,?)'
                );
                $stmt->execute([$title, $slug, $description, $location, $category_id, $coverImage, $is_featured, $status]);
                $projectId = $pdo->lastInsertId();
                flash_set('success', 'تمت إضافة المشروع بنجاح');
            }

            // رفع صور إضافية لمعرض المشروع (اختياري)
            $galleryFiles = upload_multiple_images_as_webp('gallery_images', $uploadDir);
            if ($galleryFiles) {
                $stmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order),0) m FROM project_images WHERE project_id = ?');
                $stmt->execute([$projectId]);
                $nextOrder = (int) $stmt->fetch()['m'] + 1;

                $insertImg = $pdo->prepare('INSERT INTO project_images (project_id, image_path, sort_order) VALUES (?, ?, ?)');
                foreach ($galleryFiles as $imgFile) {
                    $insertImg->execute([$projectId, $imgFile, $nextOrder]);
                    $nextOrder++;
                }
            }

            redirect('project-edit.php?id=' . $projectId);
        } catch (Throwable $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$galleryImages = $project ? get_project_images($pdo, $project['id']) : [];

require __DIR__ . '/includes/header.php';
?>

<div class="panel-header">
  <a href="projects.php" class="btn-admin secondary">‹ كل الأعمال</a>
</div>

<div class="panel">
  <form class="admin-form" method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <?php foreach ($errors as $err): ?><div class="alert alert-err"><?= e($err) ?></div><?php endforeach; ?>

    <label>عنوان المشروع
      <input type="text" name="title" required value="<?= e($project['title'] ?? '') ?>">
    </label>

    <label>الوصف
      <textarea name="description" rows="4"><?= e($project['description'] ?? '') ?></textarea>
    </label>

    <label>الموقع/المدينة
      <input type="text" name="location" value="<?= e($project['location'] ?? '') ?>">
    </label>

    <label>التصنيف
      <select name="category_id">
        <option value="">بدون تصنيف</option>
        <?php foreach ($categories as $c): ?>
          <option value="<?= $c['id'] ?>" <?= (($project['category_id'] ?? null) == $c['id']) ? 'selected' : '' ?>><?= e($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>

    <label>صورة الغلاف <small>(تُضغط وتُحوَّل تلقائيًا لصيغة WebP السريعة)</small>
      <input type="file" name="cover_image" accept="image/png, image/jpeg, image/webp">
    </label>
    <?php if (!empty($project['cover_image'])): ?>
      <img src="../uploads/portfolio/<?= e($project['cover_image']) ?>" class="thumb-preview" alt="">
    <?php endif; ?>

    <label>صور إضافية لمعرض المشروع <small>(يمكنك اختيار أكثر من صورة معًا، وتُضغط تلقائيًا)</small>
      <input type="file" name="gallery_images[]" accept="image/png, image/jpeg, image/webp" multiple>
    </label>

    <label class="checkbox-label">
      <input type="checkbox" name="is_featured" <?= !empty($project['is_featured']) ? 'checked' : '' ?>>
      مشروع مميز (يظهر أولًا في المعرض)
    </label>

    <label>حالة النشر
      <select name="status">
        <option value="published" <?= (($project['status'] ?? 'published') === 'published') ? 'selected' : '' ?>>منشور</option>
        <option value="draft" <?= (($project['status'] ?? '') === 'draft') ? 'selected' : '' ?>>مسودة (مخفي عن الزوار)</option>
      </select>
    </label>

    <div class="form-actions">
      <button type="submit" class="btn-admin">حفظ</button>
      <a href="projects.php" class="btn-admin secondary">إلغاء</a>
    </div>
  </form>
</div>

<?php if ($project && !empty($galleryImages)): ?>
<div class="panel">
  <h2>صور المعرض الحالية</h2>
  <div class="gallery-manage">
    <?php foreach ($galleryImages as $img): ?>
      <div class="gallery-manage-item">
        <img src="../uploads/portfolio/<?= e($img['image_path']) ?>" alt="">
        <a href="gallery-image-delete.php?id=<?= $img['id'] ?>&project_id=<?= $project['id'] ?>&token=<?= csrf_token() ?>" class="danger" data-confirm="حذف هذه الصورة من المعرض؟">حذف</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
