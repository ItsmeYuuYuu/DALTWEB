<?php

require_once __DIR__ . '/../models/CartModel.php';

CartModel::startSession();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 1;

if ($productId > 0 && $quantity > 0) {
    CartModel::addItem($productId, $quantity);
}

$redirect = $_GET['redirect'] ?? PUBLIC_ROOT . '/index.php';
header('Location: ' . $redirect);
exit;

