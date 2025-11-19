# 🗺️ Hướng dẫn cấu hình Google Maps API Key (5 phút)

## ⚡ Cách nhanh nhất (3 bước)

### Bước 1: Lấy API Key (2 phút)
1. Truy cập: **https://console.cloud.google.com/**
2. Đăng nhập bằng tài khoản Google
3. Click **"Select a project"** > **"New Project"**
   - Tên project: `Shop Dien Thoai` (hoặc tên bất kỳ)
   - Click **"Create"**
4. Chờ vài giây, sau đó chọn project vừa tạo
5. Vào menu **☰** > **"APIs & Services"** > **"Library"**
6. Tìm kiếm: **"Maps JavaScript API"**
7. Click vào **"Maps JavaScript API"**
8. Click nút **"ENABLE"** (Bật API)
9. Vào **"APIs & Services"** > **"Credentials"**
10. Click **"+ CREATE CREDENTIALS"** > **"API key"**
11. **Copy API Key** vừa tạo (dạng: `AIzaSy...`)

### Bước 2: Cấu hình trong project (30 giây)
1. Mở file: `config/config.php`
2. Tìm dòng: `define('GOOGLE_MAPS_API_KEY', 'YOUR_GOOGLE_MAPS_API_KEY');`
3. Thay `YOUR_GOOGLE_MAPS_API_KEY` bằng API Key vừa copy
4. Lưu file

### Bước 3: Kiểm tra (30 giây)
1. Refresh lại trang chủ
2. Bản đồ sẽ hiển thị ngay! ✅

---

## 🔒 Bảo mật API Key (Tùy chọn - Khuyến nghị)

Để tránh lạm dụng API Key, bạn nên giới hạn:

1. Vào **"APIs & Services"** > **"Credentials"**
2. Click vào API Key vừa tạo
3. Trong **"Application restrictions"**:
   - Chọn **"HTTP referrers (web sites)"**
   - Thêm: `localhost/*` (cho test)
   - Thêm: `yourdomain.com/*` (khi deploy)
4. Trong **"API restrictions"**:
   - Chọn **"Restrict key"**
   - Chỉ chọn **"Maps JavaScript API"**
5. Click **"Save"**

---

## 💰 Chi phí

- **Miễn phí**: $200 credit/tháng từ Google
- **Giới hạn**: ~28,000 lượt load bản đồ/tháng (miễn phí)
- **Đủ dùng**: Cho website nhỏ và vừa

Xem chi tiết: https://mapsplatform.google.com/pricing/

---

## ❓ Lỗi thường gặp

### Lỗi: "This API key is not authorized"
→ **Giải pháp**: Bật "Maps JavaScript API" trong Google Cloud Console

### Lỗi: "RefererNotAllowedMapError"
→ **Giải pháp**: Thêm domain của bạn vào HTTP referrers restrictions

### Bản đồ không hiển thị
→ **Giải pháp**: 
1. Kiểm tra API Key đã đúng chưa
2. Kiểm tra console trình duyệt (F12) xem có lỗi gì
3. Đảm bảo đã bật "Maps JavaScript API"

---

## 📞 Cần hỗ trợ?

Nếu gặp vấn đề, kiểm tra:
1. API Key đã được copy đầy đủ chưa (không thiếu ký tự)
2. Đã bật "Maps JavaScript API" chưa
3. File `config/config.php` đã được lưu chưa


