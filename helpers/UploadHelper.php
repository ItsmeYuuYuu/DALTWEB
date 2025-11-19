<?php

require_once __DIR__ . '/../config/config.php';

class UploadHelper
{
    private static $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    private static $maxFileSize = 5 * 1024 * 1024; // 5MB
    private static $uploadDir = __DIR__ . '/../assets/images/';

    public static function uploadImage(array $file, string $oldFileName = null): array
    {
        // Kiểm tra lỗi upload
        if (!isset($file['error']) || is_array($file['error'])) {
            return ['success' => false, 'message' => 'Tham số upload không hợp lệ.'];
        }

        // Nếu không có file upload, trả về tên file cũ
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['success' => true, 'filename' => $oldFileName];
        }

        // Kiểm tra lỗi upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => self::getUploadErrorMessage($file['error'])];
        }

        // Kiểm tra kích thước file
        if ($file['size'] > self::$maxFileSize) {
            return ['success' => false, 'message' => 'File quá lớn. Kích thước tối đa: 5MB.'];
        }

        // Kiểm tra loại file
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, self::$allowedTypes)) {
            return ['success' => false, 'message' => 'Chỉ chấp nhận file ảnh: JPG, PNG, GIF, WEBP.'];
        }

        // Tạo tên file mới (tránh trùng lặp)
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid('product_', true) . '.' . $extension;
        $targetPath = self::$uploadDir . $newFileName;

        // Upload file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Không thể upload file. Vui lòng thử lại.'];
        }

        // Xóa file cũ nếu có
        if ($oldFileName && file_exists(self::$uploadDir . $oldFileName)) {
            @unlink(self::$uploadDir . $oldFileName);
        }

        return ['success' => true, 'filename' => $newFileName];
    }

    private static function getUploadErrorMessage(int $errorCode): string
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'File quá lớn.';
            case UPLOAD_ERR_PARTIAL:
                return 'File chỉ được upload một phần.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Thiếu thư mục tạm.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Không thể ghi file.';
            case UPLOAD_ERR_EXTENSION:
                return 'Upload bị dừng bởi extension.';
            default:
                return 'Lỗi upload không xác định.';
        }
    }

    public static function deleteImage(string $filename): bool
    {
        if ($filename && file_exists(self::$uploadDir . $filename)) {
            return @unlink(self::$uploadDir . $filename);
        }
        return false;
    }
}

