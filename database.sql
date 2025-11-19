-- Tạo database (nếu chưa có)
CREATE DATABASE IF NOT EXISTS shop_dienthoai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shop_dienthoai;

-- Bảng danh mục sản phẩm
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(150) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(200) NOT NULL,
    category_id INT NOT NULL,
    price DECIMAL(12,2) NOT NULL,
    old_price DECIMAL(12,2) DEFAULT NULL,
    thumbnail VARCHAR(255) DEFAULT NULL,
    description TEXT,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng người dùng
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm một số danh mục mẫu
INSERT INTO categories (name, slug, status) VALUES
('iPhone', 'iphone', 1),
('Samsung', 'samsung', 1),
('Xiaomi', 'xiaomi', 1),
('OPPO', 'oppo', 1),
('Realme', 'realme', 1),
('Vivo', 'vivo', 1);

-- Thêm nhiều sản phẩm mẫu (phục vụ demo giao diện)
INSERT INTO products (name, slug, category_id, price, old_price, thumbnail, description, status)
VALUES
-- iPhone
('iPhone 15 Pro Max 256GB', 'iphone-15-pro-max-256gb', 1, 32990000, 36990000, 'iphone-15-pro-max.jpg', 'iPhone 15 Pro Max 256GB chính hãng VN/A, khung titan sang trọng, chip A17 Pro mạnh mẽ, camera cải tiến cho trải nghiệm chụp ảnh chuyên nghiệp.', 1),
('iPhone 15 128GB', 'iphone-15-128gb', 1, 22990000, 24990000, 'iphone-15.jpg', 'iPhone 15 128GB với cổng USB-C, màn hình Super Retina XDR 6.1 inch, thiết kế Dynamic Island hiện đại.', 1),
('iPhone 14 Pro 256GB', 'iphone-14-pro-256gb', 1, 25990000, 28990000, 'iphone-14-pro.jpg', 'iPhone 14 Pro 256GB với màn hình ProMotion 120Hz, Always-On Display và cụm 3 camera chuyên nghiệp.', 1),

-- Samsung
('Samsung Galaxy S24 Ultra 256GB', 'samsung-galaxy-s24-ultra-256gb', 2, 28990000, 31990000, 's24-ultra.jpg', 'Samsung Galaxy S24 Ultra với bút S Pen, màn hình Dynamic AMOLED 2X 6.8 inch và camera zoom ấn tượng.', 1),
('Samsung Galaxy S24 5G 256GB', 'samsung-galaxy-s24-5g-256gb', 2, 21990000, 23990000, 's24.jpg', 'Samsung Galaxy S24 5G hiệu năng mạnh, thiết kế mỏng nhẹ, tối ưu cho công việc và giải trí.', 1),
('Samsung Galaxy A55 5G 128GB', 'samsung-galaxy-a55-5g-128gb', 2, 9490000, 10490000, 'a55-5g.jpg', 'Galaxy A55 5G với màn hình Super AMOLED 120Hz, pin 5000mAh phù hợp cho học sinh, sinh viên.', 1),

-- Xiaomi
('Xiaomi 14 256GB', 'xiaomi-14-256gb', 3, 16990000, 18990000, 'xiaomi-14.jpg', 'Xiaomi 14 256GB trang bị Snapdragon mới, camera Leica, sạc nhanh công suất cao.', 1),
('Xiaomi Redmi Note 13 Pro 5G 256GB', 'xiaomi-redmi-note-13-pro-5g-256gb', 3, 8990000, 9990000, 'redmi-note-13-pro.jpg', 'Redmi Note 13 Pro 5G với màn hình 1.5K, camera 200MP và sạc nhanh 67W trong tầm giá tầm trung.', 1),
('Xiaomi Redmi 13C 128GB', 'xiaomi-redmi-13c-128gb', 3, 3490000, 3990000, 'redmi-13c.jpg', 'Redmi 13C là lựa chọn tiết kiệm, pin lớn, phù hợp nhu cầu dùng cơ bản hàng ngày.', 1),

-- OPPO
('OPPO Reno11 5G 256GB', 'oppo-reno11-5g-256gb', 4, 11990000, 12990000, 'oppo-reno11.jpg', 'OPPO Reno11 5G thiết kế mỏng nhẹ, cụm camera đẹp mắt, tối ưu chụp chân dung.', 1),
('OPPO A79 5G 128GB', 'oppo-a79-5g-128gb', 4, 6490000, 6990000, 'oppo-a79-5g.jpg', 'OPPO A79 5G màn hình lớn, loa kép, pin trâu phù hợp xem phim và chơi game nhẹ.', 1),

-- Realme
('Realme 12 Pro+ 5G 256GB', 'realme-12-pro-plus-5g-256gb', 5, 11990000, 12990000, 'realme-12-pro-plus.jpg', 'Realme 12 Pro+ 5G với camera tele, thiết kế mặt lưng giả da sang trọng, hiệu năng ổn định.', 1),
('Realme C67 8GB/128GB', 'realme-c67-8-128', 5, 5290000, 5790000, 'realme-c67.jpg', 'Realme C67 có màn hình 90Hz, pin 5000mAh, sạc nhanh 33W trong tầm giá phổ thông.', 1),

-- Vivo
('Vivo V30 5G 256GB', 'vivo-v30-5g-256gb', 6, 10990000, 11990000, 'vivo-v30.jpg', 'Vivo V30 5G với thiết kế mỏng, camera chân dung, tối ưu cho giới trẻ thích chụp ảnh.', 1),
('Vivo Y100 5G 128GB', 'vivo-y100-5g-128gb', 6, 6290000, 6790000, 'vivo-y100-5g.jpg', 'Vivo Y100 5G hỗ trợ kết nối 5G, pin lớn, thiết kế thời trang phù hợp học sinh, sinh viên.', 1);

-- Bảng giỏ hàng
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_cart_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_product (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng đơn hàng
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    shipping_address TEXT NOT NULL,
    shipping_lat DECIMAL(10, 8) DEFAULT NULL,
    shipping_lng DECIMAL(11, 8) DEFAULT NULL,
    total_amount DECIMAL(12,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipping', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
    notes TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bảng chi tiết đơn hàng
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_price DECIMAL(12,2) NOT NULL,
    quantity INT NOT NULL,
    subtotal DECIMAL(12,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tài khoản admin mặc định
INSERT INTO users (name, email, password, role, status)
VALUES ('Quản trị viên', 'admin@example.com', '$2y$12$neR50kFf6IcgQpa9uLEqb.MqQWQChQNaUpX.zIGK0WGjmCD1nOGwK', 'admin', 1)
ON DUPLICATE KEY UPDATE email = VALUES(email);


