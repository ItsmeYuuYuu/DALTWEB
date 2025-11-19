<?php

// Đường dẫn gốc của project trên URL (sau localhost:port)
// Nếu bạn đổi tên thư mục project, hãy sửa lại hằng số này.
define('APP_ROOT', '/DoAnTHLTWEB');

// Thư mục public (nơi chứa index.php)
define('PUBLIC_ROOT', APP_ROOT . '/public');

// Google Maps API Key
// ⚡ HƯỚNG DẪN NHANH (3 bước):
// 1. Truy cập: https://console.cloud.google.com/
// 2. Tạo project > Bật "Maps JavaScript API" > Tạo API Key
// 3. Copy API Key và dán vào dòng dưới đây (thay YOUR_GOOGLE_MAPS_API_KEY)
// 
// 📖 Xem hướng dẫn chi tiết: HUONG_DAN_GOOGLE_MAPS.md
define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');


