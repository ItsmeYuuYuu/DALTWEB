<?php

require_once __DIR__ . '/../helpers/Auth.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../models/OrderModel.php';

Auth::requireLogin();
CartModel::startSession();

$cartItems = CartModel::getCartWithDetails();
$totalPrice = CartModel::getTotalPrice();

if (empty($cartItems)) {
    header('Location: ' . PUBLIC_ROOT . '/cart.php');
    exit;
}

$currentUser = Auth::user();
$errors = [];
$success = false;
$orderId = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $customerPhone = trim($_POST['customer_phone'] ?? '');
    $customerEmail = trim($_POST['customer_email'] ?? '');
    $shippingAddress = trim($_POST['shipping_address'] ?? '');
    $shippingLat = !empty($_POST['shipping_lat']) ? (float) $_POST['shipping_lat'] : null;
    $shippingLng = !empty($_POST['shipping_lng']) ? (float) $_POST['shipping_lng'] : null;
    $notes = trim($_POST['notes'] ?? '');

    // Validation
    if ($customerName === '') {
        $errors[] = 'Vui lòng nhập họ tên.';
    }
    if ($customerPhone === '') {
        $errors[] = 'Vui lòng nhập số điện thoại.';
    }
    if ($customerEmail === '' || !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vui lòng nhập email hợp lệ.';
    }
    if ($shippingAddress === '') {
        $errors[] = 'Vui lòng nhập địa chỉ giao hàng.';
    }

    if (empty($errors)) {
        try {
            $orderModel = new OrderModel();
            $orderId = $orderModel->create([
                'user_id' => $currentUser['id'],
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'customer_email' => $customerEmail,
                'shipping_address' => $shippingAddress,
                'shipping_lat' => $shippingLat,
                'shipping_lng' => $shippingLng,
                'total_amount' => $totalPrice,
                'status' => 'pending',
                'notes' => $notes,
                'items' => array_map(function($item) {
                    return [
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'product_price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                    ];
                }, $cartItems),
            ]);

            // Xóa giỏ hàng sau khi đặt hàng thành công
            CartModel::clear();
            $success = true;
        } catch (Exception $e) {
            $errors[] = 'Có lỗi xảy ra khi tạo đơn hàng. Vui lòng thử lại.';
        }
    }
}

$pageTitle = 'Thanh toán';

include __DIR__ . '/../views/layouts/header.php';
?>

<div class="checkout-page">
    <h1>Thanh toán</h1>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <h2>Đặt hàng thành công!</h2>
            <p>Mã đơn hàng của bạn: <strong>#<?= $orderId ?></strong></p>
            <p>Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi. Chúng tôi sẽ liên hệ với bạn sớm nhất có thể.</p>
            <a href="<?= PUBLIC_ROOT ?>/index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <div class="checkout-container">
            <div class="checkout-left">
                <h2>Thông tin giao hàng</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $err): ?>
                            <p><?= htmlspecialchars($err) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="checkout-form">
                    <div class="form-group">
                        <label>Họ tên *</label>
                        <input type="text" name="customer_name" 
                               value="<?= htmlspecialchars($_POST['customer_name'] ?? $currentUser['name']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại *</label>
                        <input type="tel" name="customer_phone" 
                               value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="customer_email" 
                               value="<?= htmlspecialchars($_POST['customer_email'] ?? $currentUser['email']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ giao hàng *</label>
                        <textarea name="shipping_address" rows="3" required><?= htmlspecialchars($_POST['shipping_address'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Chọn vị trí giao hàng trên bản đồ (tùy chọn)</label>
                        <div id="shipping-map" style="height: 300px; width: 100%; border-radius: 8px; margin-top: 10px;"></div>
                        <input type="hidden" name="shipping_lat" id="shipping_lat">
                        <input type="hidden" name="shipping_lng" id="shipping_lng">
                        <small style="display: block; margin-top: 5px; color: #6b7280;">
                            Click vào bản đồ hoặc kéo marker để chọn vị trí giao hàng của bạn
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Ghi chú</label>
                        <textarea name="notes" rows="3"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-large">Đặt hàng</button>
                </form>
            </div>

            <div class="checkout-right">
                <h2>Đơn hàng của bạn</h2>
                <div class="order-summary">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="order-item">
                            <div class="order-item-info">
                                <strong><?= htmlspecialchars($item['name']) ?></strong>
                                <span><?= $item['quantity'] ?> x <?= number_format($item['price'], 0, ',', '.') ?> đ</span>
                            </div>
                            <div class="order-item-total">
                                <?= number_format($item['subtotal'], 0, ',', '.') ?> đ
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div class="order-total">
                        <strong>Tổng cộng: <?= number_format($totalPrice, 0, ',', '.') ?> đ</strong>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    let map;
    let marker;
    const defaultLat = 10.7406; // Vĩ độ của 180 Cao Lỗ, P4, Q8, HCM
    const defaultLng = 106.6676; // Kinh độ của 180 Cao Lỗ, P4, Q8, HCM

    function initShippingMap() {
        map = new google.maps.Map(document.getElementById("shipping-map"), {
            center: { lat: defaultLat, lng: defaultLng },
            zoom: 15,
        });

        // Marker cố định cho cửa hàng (màu đỏ)
        const storeMarker = new google.maps.Marker({
            position: { lat: defaultLat, lng: defaultLng },
            map: map,
            title: "Cửa hàng - 180 Cao Lỗ, phường 4, quận 8, TP.HCM",
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png"
            },
            zIndex: 1
        });

        // Marker có thể kéo để chọn vị trí giao hàng (màu xanh, ban đầu ở vị trí cửa hàng)
        marker = new google.maps.Marker({
            position: { lat: defaultLat, lng: defaultLng },
            map: map,
            title: "Kéo marker này để chọn vị trí giao hàng",
            draggable: true,
            icon: {
                url: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            },
            zIndex: 2
        });

        // InfoWindow cho cửa hàng
        const storeInfoWindow = new google.maps.InfoWindow({
            content: "<div style='padding: 8px;'><strong>📍 Cửa hàng</strong><br>180 Cao Lỗ, phường 4, quận 8, TP.HCM</div>"
        });
        storeMarker.addListener("click", () => {
            storeInfoWindow.open(map, storeMarker);
        });

        // Xử lý khi click vào bản đồ
        map.addListener("click", (e) => {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            
            // Di chuyển marker đến vị trí click
            marker.setPosition({ lat: lat, lng: lng });
            
            // Cập nhật input hidden
            document.getElementById("shipping_lat").value = lat;
            document.getElementById("shipping_lng").value = lng;
        });

        // Xử lý khi kéo marker
        marker.addListener("dragend", (e) => {
            const lat = e.latLng.lat();
            const lng = e.latLng.lng();
            
            document.getElementById("shipping_lat").value = lat;
            document.getElementById("shipping_lng").value = lng;
        });
    }
</script>

<?php if (defined('GOOGLE_MAPS_API_KEY') && GOOGLE_MAPS_API_KEY !== 'YOUR_GOOGLE_MAPS_API_KEY'): ?>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=<?= GOOGLE_MAPS_API_KEY ?>&callback=initShippingMap">
</script>
<?php else: ?>
<div class="alert alert-danger">
    <p><strong>Lưu ý:</strong> Vui lòng cấu hình Google Maps API Key trong file <code>config/config.php</code></p>
    <p>Xem hướng dẫn trong file <code>GOOGLE_MAPS_SETUP.md</code></p>
</div>
<script>
    // Fallback nếu không có API Key
    function initShippingMap() {
        document.getElementById("shipping-map").innerHTML = '<div style="padding: 20px; text-align: center; color: #6b7280; background: #f3f4f6; border-radius: 8px; height: 100%; display: flex; align-items: center; justify-content: center;">Vui lòng cấu hình Google Maps API Key để sử dụng bản đồ</div>';
    }
    // Gọi ngay để hiển thị thông báo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initShippingMap);
    } else {
        initShippingMap();
    }
</script>
<?php endif; ?>

<?php
include __DIR__ . '/../views/layouts/footer.php';
