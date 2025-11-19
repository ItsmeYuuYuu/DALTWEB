<?php

require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../helpers/UploadHelper.php';
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';

Auth::requireAdmin();

$productModel = new ProductModel();
$categoryModel = new CategoryModel();
$categories = $categoryModel->getAllActive();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $category_id = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $old_price = (float) ($_POST['old_price'] ?? 0);
    $thumbnail = trim($_POST['thumbnail'] ?? ''); // URL hoặc tên file cũ
    $description = trim($_POST['description'] ?? '');
    $status = isset($_POST['status']) ? 1 : 0;

    if ($name === '') {
        $errors[] = 'Tên sản phẩm không được để trống.';
    }
    if ($slug === '') {
        $slug = strtolower(preg_replace('/\s+/', '-', $name));
    }
    if ($category_id <= 0) {
        $errors[] = 'Vui lòng chọn danh mục.';
    }
    if ($price <= 0) {
        $errors[] = 'Giá phải lớn hơn 0.';
    }

    // Xử lý upload ảnh
    if (isset($_FILES['thumbnail_file']) && $_FILES['thumbnail_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        $uploadResult = UploadHelper::uploadImage($_FILES['thumbnail_file']);
        if ($uploadResult['success']) {
            $thumbnail = $uploadResult['filename'];
        } else {
            $errors[] = $uploadResult['message'];
        }
    }

    if (empty($errors)) {
        $productModel->create([
            'name' => $name,
            'slug' => $slug,
            'category_id' => $category_id,
            'price' => $price,
            'old_price' => $old_price ?: null,
            'thumbnail' => $thumbnail ?: null,
            'description' => $description,
            'status' => $status,
        ]);
        header('Location: ' . PUBLIC_ROOT . '/admin/products.php');
        exit;
    }
}

$pageTitle = 'Thêm sản phẩm';

include __DIR__ . '/../../views/layouts/header.php';
?>

<h1>Thêm sản phẩm mới</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $err): ?>
            <p><?= htmlspecialchars($err) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<form method="post" class="form" enctype="multipart/form-data">
    <div class="form-group">
        <label>Tên sản phẩm</label>
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Slug (không bắt buộc)</label>
        <input type="text" name="slug" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Danh mục</label>
        <select name="category_id">
            <option value="0">-- Chọn danh mục --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && (int) $_POST['category_id'] === (int) $cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Giá hiện tại</label>
        <input type="number" name="price" step="1000" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Giá cũ (nếu có)</label>
        <input type="number" name="old_price" step="1000" value="<?= htmlspecialchars($_POST['old_price'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label>Ảnh sản phẩm</label>
        <input type="file" name="thumbnail_file" id="thumbnail_file" accept="image/*" onchange="previewImage(this)">
        <small style="display: block; margin-top: 5px; color: #6b7280;">
            Chọn ảnh từ máy tính (JPG, PNG, GIF, WEBP - tối đa 5MB)
        </small>
        <div id="image-preview" style="margin-top: 10px; display: none;">
            <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #e5e7eb;">
        </div>
        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #e5e7eb;">
            <small style="color: #6b7280;">Hoặc nhập URL ảnh:</small>
            <input type="text" name="thumbnail" value="<?= htmlspecialchars($_POST['thumbnail'] ?? '') ?>" 
                   placeholder="https://example.com/image.jpg" style="margin-top: 5px;">
        </div>
    </div>
    <div class="form-group">
        <label>Mô tả</label>
        <textarea name="description" rows="5"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    </div>
    <div class="form-group">
        <label>
            <input type="checkbox" name="status" <?= (isset($_POST['status']) ? 'checked' : 'checked') ?>>
            Hiển thị
        </label>
    </div>
    <button type="submit" class="btn">Lưu</button>
    <a href="<?= PUBLIC_ROOT ?>/admin/products.php" class="btn btn-secondary">Hủy</a>
</form>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php
include __DIR__ . '/../../views/layouts/footer.php';


