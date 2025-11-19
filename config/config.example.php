<?php

// Đường dẫn gốc của project trên URL (sau localhost:port)
// Nếu bạn đổi tên thư mục project, hãy sửa lại hằng số này.
define('APP_ROOT', '/DoAnTHLTWEB');

// Thư mục public (nơi chứa index.php)
define('PUBLIC_ROOT', APP_ROOT . '/public');

// Google Maps API Key
// BƯỚC 1: Truy cập https://console.cloud.google.com/
// BƯỚC 2: Đăng nhập bằng tài khoản Google
// BƯỚC 3: Tạo project mới (hoặc chọn project có sẵn)
// BƯỚC 4: Vào "APIs & Services" > "Library"
// BƯỚC 5: Tìm và bật "Maps JavaScript API"
// BƯỚC 6: Vào "APIs & Services" > "Credentials"
// BƯỚC 7: Click "Create Credentials" > "API Key"
// BƯỚC 8: Copy API Key và dán vào dòng dưới đây
define('GOOGLE_MAPS_API_KEY', 'DAN_API_KEY_CUA_BAN_VAO_DAY');


