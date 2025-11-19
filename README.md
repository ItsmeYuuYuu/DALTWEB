## Đồ án Web bán điện thoại (PHP thuần + MySQL + PDO)

Project này là web bán điện thoại đơn giản (clone ý tưởng từ sonpixel.vn) gồm:
- Frontend hiển thị sản phẩm, tìm kiếm.
- Backend quản trị sản phẩm (thêm/sửa/xóa/cập nhật trạng thái).
- PHP thuần, kết nối MySQL bằng PDO.

### Cấu trúc chính

- `public/` – các file người dùng truy cập (index, chi tiết, admin).
- `config/` – cấu hình kết nối CSDL.
- `models/` – lớp thao tác với CSDL (Product, Category, ...).
- `controllers/` – xử lý logic (nếu cần tách riêng).
- `views/` – giao diện HTML chia nhỏ header/footer, trang con.
- `assets/` – CSS/JS/images.

### CSDL

- Tên CSDL gợi ý: `shop_dienthoai`
- Xem file `database.sql` để tạo các bảng mẫu.

### Chạy project

1. Tạo database `shop_dienthoai` trong MySQL.
2. Import file `database.sql`.
3. Cấu hình tài khoản DB trong `config/database.php`.
4. **Cấu hình Google Maps API Key** (xem file `HUONG_DAN_GOOGLE_MAPS.md`):
   - Lấy API Key tại: https://console.cloud.google.com/
   - Mở `config/config.php` và thay `YOUR_GOOGLE_MAPS_API_KEY` bằng API Key của bạn
5. Trỏ webserver (XAMPP / WAMP) vào thư mục `public/`.

### ⚡ Cấu hình Google Maps nhanh (3 bước)

1. Truy cập: https://console.cloud.google.com/
2. Tạo project > Bật "Maps JavaScript API" > Tạo API Key
3. Copy API Key vào file `config/config.php`

Xem hướng dẫn chi tiết trong file `HUONG_DAN_GOOGLE_MAPS.md`


