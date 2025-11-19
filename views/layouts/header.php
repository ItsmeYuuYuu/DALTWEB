<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../helpers/Auth.php';
require_once __DIR__ . '/../../models/CartModel.php';

Auth::startSession();
CartModel::startSession();
$currentUser = Auth::user();
$userInitial = null;
if ($currentUser) {
    $firstChar = function_exists('mb_substr')
        ? mb_substr($currentUser['name'], 0, 1, 'UTF-8')
        : substr($currentUser['name'], 0, 1);
    $userInitial = strtoupper($firstChar);
}

$cartCount = CartModel::getTotalItems();

if (!isset($pageTitle)) {
    $pageTitle = 'Shop điện thoại';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= APP_ROOT ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <div class="logo">
            <a href="<?= PUBLIC_ROOT ?>/index.php" title="Trang chủ Cuồng Điện Thoại">
                <img src="<?= APP_ROOT ?>/assets/images/CuongDienThoaiLogo.png" alt="Cuồng Điện Thoại">
            </a>
        </div>
        <form class="search-form" action="<?= PUBLIC_ROOT ?>/index.php" method="get">
            <input type="text" name="q" placeholder="Tìm kiếm sản phẩm..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
            <button type="submit" aria-label="Tìm kiếm">
                <span>🔍</span>
            </button>
        </form>
        <div class="header-actions">
            <?php if ($currentUser): ?>
                <div class="action-item user-info">
                    <div class="user-avatar">
                        <?= htmlspecialchars($userInitial) ?>
                    </div>
                    <div>
                        <div class="action-text">Xin chào</div>
                        <strong><?= htmlspecialchars($currentUser['name']) ?></strong>
                    </div>
                </div>
                <a class="action-item" href="<?= PUBLIC_ROOT ?>/logout.php">
                    <span class="action-icon">↩</span>
                    <span class="action-text">Đăng xuất</span>
                </a>
            <?php else: ?>
                <a class="action-item" href="<?= PUBLIC_ROOT ?>/login.php">
                    <span class="action-icon">👤</span>
                    <span class="action-text">Đăng nhập</span>
                </a>
                <a class="action-item" href="<?= PUBLIC_ROOT ?>/register.php">
                    <span class="action-icon">🔑</span>
                    <span class="action-text">Đăng ký</span>
                </a>
            <?php endif; ?>
            <a class="action-item cart" href="<?= PUBLIC_ROOT ?>/cart.php">
                <span class="action-icon">🛒</span>
                <span class="action-text">Giỏ hàng</span>
                <?php if ($cartCount > 0): ?>
                    <span class="cart-count"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>
        </div>
    </div>
    <nav class="main-nav container">
        <a href="<?= PUBLIC_ROOT ?>/index.php">Trang chủ</a>
        <?php if (Auth::isAdmin()): ?>
            <a href="<?= PUBLIC_ROOT ?>/admin/products.php">Quản trị</a>
        <?php endif; ?>
    </nav>
</header>
<main class="site-main container">


