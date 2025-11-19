<?php

require_once __DIR__ . '/../models/CartModel.php';

CartModel::startSession();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;

    if ($productId > 0) {
        CartModel::updateQuantity($productId, $quantity);
    }
}

header('Location: ' . PUBLIC_ROOT . '/cart.php');
exit;

