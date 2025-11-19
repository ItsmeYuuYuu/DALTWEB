<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/Auth.php';

class CartModel
{
    private static $conn = null;

    private static function getConnection()
    {
        if (self::$conn === null) {
            $db = new Database();
            self::$conn = $db->getConnection();
        }
        return self::$conn;
    }

    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // Load giỏ hàng từ database vào session
    public static function loadCartFromDatabase(?int $userId): void
    {
        self::startSession();
        
        if (!$userId) {
            $_SESSION['cart'] = [];
            return;
        }

        $conn = self::getConnection();
        $sql = "SELECT ci.product_id, ci.quantity, p.name, p.price, p.thumbnail
                FROM cart_items ci
                INNER JOIN products p ON ci.product_id = p.id
                WHERE ci.user_id = :user_id AND p.status = 1";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        $items = $stmt->fetchAll();

        $_SESSION['cart'] = [];
        foreach ($items as $item) {
            $_SESSION['cart'][$item['product_id']] = [
                'id' => $item['product_id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'thumbnail' => $item['thumbnail'],
                'quantity' => (int) $item['quantity'],
            ];
        }
    }

    // Lưu giỏ hàng từ session vào database
    public static function saveCartToDatabase(?int $userId): void
    {
        if (!$userId) {
            return;
        }

        $conn = self::getConnection();
        $cart = $_SESSION['cart'] ?? [];

        // Lấy danh sách product_id hiện tại trong giỏ hàng
        $currentProductIds = array_keys($cart);
        
        // Xóa các sản phẩm không còn trong giỏ hàng
        if (!empty($currentProductIds)) {
            $placeholders = implode(',', array_fill(0, count($currentProductIds), '?'));
            $sql = "DELETE FROM cart_items WHERE user_id = ? AND product_id NOT IN ($placeholders)";
            $stmt = $conn->prepare($sql);
            $params = array_merge([$userId], $currentProductIds);
            $stmt->execute($params);
        } else {
            // Nếu giỏ hàng trống, xóa hết
            $sql = "DELETE FROM cart_items WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => $userId]);
        }

        // Thêm hoặc cập nhật giỏ hàng
        if (!empty($cart)) {
            $sql = "INSERT INTO cart_items (user_id, product_id, quantity) 
                    VALUES (:user_id, :product_id, :quantity)
                    ON DUPLICATE KEY UPDATE quantity = :quantity, updated_at = NOW()";
            $stmt = $conn->prepare($sql);
            
            foreach ($cart as $item) {
                $stmt->execute([
                    ':user_id' => $userId,
                    ':product_id' => $item['id'],
                    ':quantity' => $item['quantity'],
                ]);
            }
        }
    }

    public static function getCart(): array
    {
        self::startSession();
        return $_SESSION['cart'] ?? [];
    }

    public static function addItem(int $productId, int $quantity = 1): bool
    {
        self::startSession();
        
        $productModel = new ProductModel();
        $product = $productModel->find($productId);
        
        if (!$product || (int) $product['status'] !== 1) {
            return false;
        }

        $user = Auth::user();
        $userId = $user ? (int) $user['id'] : null;

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'thumbnail' => $product['thumbnail'],
                'quantity' => $quantity,
            ];
        }

        // Lưu vào database nếu user đã đăng nhập
        if ($userId) {
            self::saveCartToDatabase($userId);
        }

        return true;
    }

    public static function updateQuantity(int $productId, int $quantity): bool
    {
        self::startSession();
        
        if ($quantity <= 0) {
            return self::removeItem($productId);
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
            
            // Lưu vào database nếu user đã đăng nhập
            $user = Auth::user();
            if ($user) {
                self::saveCartToDatabase((int) $user['id']);
            }
            
            return true;
        }

        return false;
    }

    public static function removeItem(int $productId): bool
    {
        self::startSession();
        
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            
            // Xóa khỏi database nếu user đã đăng nhập
            $user = Auth::user();
            if ($user) {
                $conn = self::getConnection();
                $sql = "DELETE FROM cart_items WHERE user_id = :user_id AND product_id = :product_id";
                $stmt = $conn->prepare($sql);
                $stmt->execute([
                    ':user_id' => (int) $user['id'],
                    ':product_id' => $productId,
                ]);
            }
            
            return true;
        }

        return false;
    }

    public static function clear(): void
    {
        self::startSession();
        
        $user = Auth::user();
        if ($user) {
            $conn = self::getConnection();
            $sql = "DELETE FROM cart_items WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => (int) $user['id']]);
        }
        
        $_SESSION['cart'] = [];
    }

    public static function getTotalItems(): int
    {
        $cart = self::getCart();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['quantity'];
        }
        return $total;
    }

    public static function getTotalPrice(): float
    {
        $cart = self::getCart();
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total;
    }

    public static function getCartWithDetails(): array
    {
        $cart = self::getCart();
        $items = [];
        
        foreach ($cart as $item) {
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'thumbnail' => $item['thumbnail'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity'],
            ];
        }
        
        return $items;
    }
}

