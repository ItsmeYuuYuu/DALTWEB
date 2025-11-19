<?php

require_once __DIR__ . '/../models/CartModel.php';

CartModel::startSession();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($productId > 0) {
    CartModel::removeItem($productId);
}

header('Location: ' . PUBLIC_ROOT . '/cart.php');
exit;

