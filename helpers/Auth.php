<?php

require_once __DIR__ . '/../config/config.php';

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function login(array $user): void
    {
        self::startSession();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];
        
        // Load giỏ hàng từ database vào session
        require_once __DIR__ . '/../models/CartModel.php';
        CartModel::loadCartFromDatabase((int) $user['id']);
    }

    public static function logout(): void
    {
        self::startSession();
        
        // Lưu giỏ hàng vào database trước khi logout (nếu user đã đăng nhập)
        if (isset($_SESSION['user'])) {
            require_once __DIR__ . '/../models/CartModel.php';
            CartModel::saveCartToDatabase((int) $_SESSION['user']['id']);
        }
        
        // Chỉ xóa thông tin user, giữ lại giỏ hàng trong database
        unset($_SESSION['user']);
        
        // Xóa session cart (sẽ load lại từ database khi đăng nhập)
        unset($_SESSION['cart']);
        
        // Không destroy session để giữ session ID (có thể dùng cho guest cart)
        // Chỉ destroy nếu muốn xóa hoàn toàn
        // session_destroy();
    }

    public static function isAdmin(): bool
    {
        $user = self::user();
        return $user && $user['role'] === 'admin';
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: ' . PUBLIC_ROOT . '/login.php');
            exit;
        }
    }

    public static function requireAdmin(): void
    {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            echo 'Bạn không có quyền truy cập trang này.';
            exit;
        }
    }
}


