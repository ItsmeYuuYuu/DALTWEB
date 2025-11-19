<?php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/CartModel.php';

Auth::startSession();
CartModel::startSession();

$cartItems = CartModel::getCartWithDetails();
$totalPrice = CartModel::getTotalPrice();

$pageTitle = 'Giỏ hàng';

include __DIR__ . '/../views/layouts/header.php';
?>

<div class="cart-page">
    <h1>Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <div class="cart-empty">
            <p>Giỏ hàng của bạn đang trống.</p>
            <a href="<?= PUBLIC_ROOT ?>/index.php" class="btn">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <img src="<?= APP_ROOT ?>/assets/images/<?= htmlspecialchars($item['thumbnail']) ?>" 
                                         alt="<?= htmlspecialchars($item['name']) ?>" 
                                         class="cart-thumb">
                                <?php else: ?>
                                    <div class="cart-thumb-placeholder">No image</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= PUBLIC_ROOT ?>/product-detail.php?id=<?= $item['id'] ?>">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                            </td>
                            <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                            <td>
                                <form method="post" action="<?= PUBLIC_ROOT ?>/cart-update.php" class="cart-quantity-form">
                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" 
                                           min="1" class="quantity-input" onchange="this.form.submit()">
                                </form>
                            </td>
                            <td class="subtotal"><?= number_format($item['subtotal'], 0, ',', '.') ?> đ</td>
                            <td>
                                <a href="<?= PUBLIC_ROOT ?>/cart-remove.php?id=<?= $item['id'] ?>" 
                                   class="btn btn-small btn-danger"
                                   onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');">
                                    Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
                        <td colspan="2" class="total-price">
                            <strong><?= number_format($totalPrice, 0, ',', '.') ?> đ</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <a href="<?= PUBLIC_ROOT ?>/index.php" class="btn btn-secondary">Tiếp tục mua sắm</a>
                <?php if (Auth::check()): ?>
                    <a href="<?= PUBLIC_ROOT ?>/checkout.php" class="btn btn-primary">Thanh toán</a>
                <?php else: ?>
                    <a href="<?= PUBLIC_ROOT ?>/login.php?redirect=<?= urlencode(PUBLIC_ROOT . '/checkout.php') ?>" 
                       class="btn btn-primary">Đăng nhập để thanh toán</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
include __DIR__ . '/../views/layouts/footer.php';

