<?php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';

$productModel = new ProductModel();
$categoryModel = new CategoryModel();

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
$sortBy = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sortOrder = isset($_GET['order']) ? $_GET['order'] : 'DESC';

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
    $page = 1;
}
$limit = 12;
$offset = ($page - 1) * $limit;

if ($keyword !== '') {
    $products = $productModel->search($keyword);
    $totalProducts = count($products);
} else {
    $products = $productModel->getAll($limit, $offset, $categoryId, $sortBy, $sortOrder);
    $totalProducts = $productModel->countAllActive($categoryId);
}

$totalPages = (int) ceil($totalProducts / $limit);
$categories = $categoryModel->getAllActive();
$pageTitle = $keyword !== '' ? 'Tìm kiếm: ' . $keyword : 'Shop điện thoại';

include __DIR__ . '/../views/layouts/header.php';
?>

<aside class="sidebar">
    <h3>Danh mục</h3>
    <ul>
        <li>
            <?php 
            $allCategoryParams = $_GET;
            unset($allCategoryParams['category']);
            $allCategoryParams['page'] = 1;
            ?>
            <a href="?<?= http_build_query($allCategoryParams) ?>" 
               class="<?= $categoryId === null ? 'active' : '' ?>">
                Tất cả
            </a>
        </li>
        <?php foreach ($categories as $cat): ?>
            <li>
                <a href="?<?= http_build_query(array_merge($_GET, ['category' => $cat['id'], 'page' => 1])) ?>" 
                   class="<?= $categoryId === (int) $cat['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    
    <h3 style="margin-top: 20px;">Sắp xếp</h3>
    <form method="get" class="sort-form">
        <?php if ($keyword): ?>
            <input type="hidden" name="q" value="<?= htmlspecialchars($keyword) ?>">
        <?php endif; ?>
        <?php if ($categoryId): ?>
            <input type="hidden" name="category" value="<?= $categoryId ?>">
        <?php endif; ?>
        <div class="form-group">
            <select name="sort" onchange="this.form.submit()">
                <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Mới nhất</option>
                <option value="price" <?= $sortBy === 'price' ? 'selected' : '' ?>>Giá</option>
                <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
        </div>
        <div class="form-group">
            <select name="order" onchange="this.form.submit()">
                <option value="DESC" <?= $sortOrder === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                <option value="ASC" <?= $sortOrder === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
            </select>
        </div>
    </form>
    
    <h3 style="margin-top: 20px;">Vị trí cửa hàng</h3>
    <div id="store-map" style="height: 250px; width: 100%; border-radius: 8px; margin-top: 10px;"></div>
    <p style="margin-top: 8px; font-size: 13px; color: #6b7280;">
        180 Cao Lỗ, phường 4, quận 8, TP.HCM
    </p>
</aside>

<section class="content">
    <?php include __DIR__ . '/../views/products/list.php'; ?>

    <?php if ($keyword === '' && $totalPages > 1): ?>
        <div class="pagination">
            <?php 
            $queryParams = $_GET;
            for ($i = 1; $i <= $totalPages; $i++): 
                $queryParams['page'] = $i;
            ?>
                <a href="?<?= http_build_query($queryParams) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</section>

<script>
    function initStoreMap() {
        const storeLat = 10.7406; // Vĩ độ của 180 Cao Lỗ, P4, Q8, HCM
        const storeLng = 106.6676; // Kinh độ của 180 Cao Lỗ, P4, Q8, HCM
        
        const storeMap = new google.maps.Map(document.getElementById("store-map"), {
            center: { lat: storeLat, lng: storeLng },
            zoom: 16,
        });

        const storeMarker = new google.maps.Marker({
            position: { lat: storeLat, lng: storeLng },
            map: storeMap,
            title: "Cửa hàng - 180 Cao Lỗ, phường 4, quận 8, TP.HCM",
            animation: google.maps.Animation.DROP
        });

        const storeInfoWindow = new google.maps.InfoWindow({
            content: "<div style='padding: 10px;'><strong>Cửa hàng điện thoại</strong><br>180 Cao Lỗ, phường 4, quận 8<br>Thành phố Hồ Chí Minh</div>"
        });
        
        storeMarker.addListener("click", () => {
            storeInfoWindow.open(storeMap, storeMarker);
        });
        
        // Mở InfoWindow mặc định
        storeInfoWindow.open(storeMap, storeMarker);
    }
</script>

<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY !== 'YOUR_GOOGLE_MAPS_API_KEY'): ?>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initStoreMap">
</script>
<?php else: ?>
<script>
    // Fallback nếu không có API Key
    function initStoreMap() {
        document.getElementById("store-map").innerHTML = '<div style="padding: 20px; text-align: center; color: #6b7280; background: #f3f4f6; border-radius: 8px; height: 100%; display: flex; align-items: center; justify-content: center;">Vui lòng cấu hình Google Maps API Key trong file config/config.php</div>';
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStoreMap);
    } else {
        initStoreMap();
    }
</script>
<?php endif; ?>

<?php
include __DIR__ . '/../views/layouts/footer.php';


