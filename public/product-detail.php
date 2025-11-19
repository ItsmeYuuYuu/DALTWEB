<?php

require_once __DIR__ . '/../models/ProductModel.php';

$productModel = new ProductModel();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = null;

if ($id > 0) {
    $product = $productModel->find($id);
}

$pageTitle = $product ? $product['name'] : 'Chi tiết sản phẩm';

include __DIR__ . '/../views/layouts/header.php';

include __DIR__ . '/../views/products/detail.php';

include __DIR__ . '/../views/layouts/footer.php';


