<?php

require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/CategoryModel.php';

Auth::requireAdmin();

$productModel = new ProductModel();
$categoryModel = new CategoryModel();

// Xoá sản phẩm
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'delete') {
    $id = (int) $_GET['id'];
    if ($id > 0) {
        $productModel->delete($id);
    }
    header('Location: ' . PUBLIC_ROOT . '/admin/products.php');
    exit;
}

$products = $productModel->getAll(100, 0);
$categories = $categoryModel->getAllActive();
$pageTitle = 'Quản trị sản phẩm';

include __DIR__ . '/../../views/layouts/header.php';
?>

<h1>Quản trị sản phẩm</h1>

<p>
    <a href="<?= PUBLIC_ROOT ?>/admin/product_add.php" class="btn">Thêm sản phẩm mới</a>
    <a href="<?= PUBLIC_ROOT ?>/index.php" class="btn btn-secondary">Về trang chủ</a>
</p>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Ảnh</th>
            <th>Tên</th>
            <th>Giá</th>
            <th>Danh mục</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td>
                    <?php if (!empty($p['thumbnail'])): ?>
                        <img src="<?= APP_ROOT ?>/assets/images/<?= htmlspecialchars($p['thumbnail']) ?>" alt="" style="width:60px;height:60px;object-fit:cover;">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= number_format($p['price'], 0, ',', '.') ?> đ</td>
                <td><?= htmlspecialchars($p['category_name'] ?? '') ?></td>
                <td><?= $p['status'] ? 'Hiển thị' : 'Ẩn' ?></td>
                <td>
                    <a class="btn btn-small" href="<?= PUBLIC_ROOT ?>/admin/product_edit.php?id=<?= $p['id'] ?>">Sửa</a>
                    <a class="btn btn-small btn-danger" href="<?= PUBLIC_ROOT ?>/admin/products.php?action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Xóa sản phẩm này?');">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
include __DIR__ . '/../../views/layouts/footer.php';


