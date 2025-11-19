<?php /** @var array $products */ ?>
<h1 class="page-title">Điện thoại nổi bật</h1>
<div class="product-grid">
    <?php if (empty($products)): ?>
        <p>Không tìm thấy sản phẩm.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <?php
            $hasOldPrice = !empty($product['old_price']) && $product['old_price'] > $product['price'];
            $discountPercent = $hasOldPrice
                ? round((($product['old_price'] - $product['price']) / $product['old_price']) * 100)
                : 0;
            ?>
            <div class="product-card">
                <a href="<?= PUBLIC_ROOT ?>/product-detail.php?id=<?= $product['id'] ?>">
                    <div class="product-thumb">
                        <?php if ($hasOldPrice && $discountPercent > 0): ?>
                            <span class="product-badge">-<?= $discountPercent ?>%</span>
                        <?php endif; ?>
                        <?php if (!empty($product['thumbnail'])): ?>
                            <img src="<?= APP_ROOT ?>/assets/images/<?= htmlspecialchars($product['thumbnail']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <?php else: ?>
                            <div class="thumb-placeholder">No image</div>
                        <?php endif; ?>
                    </div>
                    <h3 class="product-name"><?= htmlspecialchars($product['name']) ?></h3>
                </a>
                <div class="product-prices">
                    <span class="price-current"><?= number_format($product['price'], 0, ',', '.') ?> đ</span>
                    <?php if ($hasOldPrice): ?>
                        <span class="price-old"><?= number_format($product['old_price'], 0, ',', '.') ?> đ</span>
                    <?php endif; ?>
                </div>
                <?php if (!empty($product['category_name'])): ?>
                    <div class="product-category-pill">
                        <?= htmlspecialchars($product['category_name']) ?>
                    </div>
                <?php endif; ?>
                <div class="product-card-footer">
                    <a href="<?= PUBLIC_ROOT ?>/product-detail.php?id=<?= $product['id'] ?>" class="btn btn-outline btn-small">Xem chi tiết</a>
                    <a href="<?= PUBLIC_ROOT ?>/cart-add.php?id=<?= $product['id'] ?>&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>" 
                       class="btn btn-buy btn-small">Thêm vào giỏ</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

