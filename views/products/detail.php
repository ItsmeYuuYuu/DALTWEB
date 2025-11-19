<?php /** @var array $product */ ?>

<?php if (!$product): ?>
    <h1>Không tìm thấy sản phẩm</h1>
    <p>Sản phẩm bạn yêu cầu không tồn tại hoặc đã bị xoá.</p>
<?php else: ?>
    <div class="product-detail">
        <div class="detail-left">
            <?php if (!empty($product['thumbnail'])): ?>
                <img src="<?= APP_ROOT ?>/assets/images/<?= htmlspecialchars($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php else: ?>
                <div class="thumb-placeholder large">No image</div>
            <?php endif; ?>
        </div>
        <div class="detail-right">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <div class="detail-prices">
                <span class="price-current"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                <?php if (!empty($product['old_price']) && $product['old_price'] > $product['price']): ?>
                    <span class="price-old"><?= number_format($product['old_price'], 0, ',', '.') ?> đ</span>
                <?php endif; ?>
            </div>
            <?php if (!empty($product['category_name'])): ?>
                <p class="detail-category">Danh mục: <?= htmlspecialchars($product['category_name']) ?></p>
            <?php endif; ?>
            <div class="detail-description">
                <h3>Mô tả sản phẩm</h3>
                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
            <div class="detail-actions">
                <a href="<?= PUBLIC_ROOT ?>/cart-add.php?id=<?= $product['id'] ?>&redirect=<?= urlencode(PUBLIC_ROOT . '/cart.php') ?>" 
                   class="btn btn-buy">Thêm vào giỏ hàng</a>
                <a href="<?= PUBLIC_ROOT ?>/index.php" class="btn btn-secondary">Quay lại trang chủ</a>
            </div>
        </div>
    </div>
<?php endif; ?>


