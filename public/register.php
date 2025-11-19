<?php

require_once __DIR__ . '/../models/UserModel.php';
require_once __DIR__ . '/../helpers/Auth.php';

Auth::startSession();

if (Auth::check()) {
    header('Location: ' . PUBLIC_ROOT . '/index.php');
    exit;
}

$userModel = new UserModel();
$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($name === '') {
        $errors[] = 'Vui lòng nhập họ tên.';
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vui lòng nhập email hợp lệ.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }

    if ($password !== $password_confirm) {
        $errors[] = 'Xác nhận mật khẩu không khớp.';
    }

    if (empty($errors) && $userModel->findByEmail($email)) {
        $errors[] = 'Email đã được sử dụng.';
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userId = $userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => $hashedPassword,
            'role' => 'customer',
        ]);

        if ($userId) {
            $newUser = $userModel->findById($userId);
            Auth::login($newUser);
            header('Location: ' . PUBLIC_ROOT . '/index.php');
            exit;
        } else {
            $errors[] = 'Không thể tạo tài khoản. Vui lòng thử lại.';
        }
    }
}

$pageTitle = 'Đăng ký';

include __DIR__ . '/../views/layouts/header.php';
?>

<div class="auth-wrapper">
    <h1>Đăng ký tài khoản</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form auth-form">
        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Nhập lại mật khẩu</label>
            <input type="password" name="password_confirm" required>
        </div>
        <button type="submit" class="btn">Đăng ký</button>
        <p class="form-note">
            Đã có tài khoản? <a href="<?= PUBLIC_ROOT ?>/login.php">Đăng nhập</a>
        </p>
    </form>
</div>

<?php
include __DIR__ . '/../views/layouts/footer.php';

