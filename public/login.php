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
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Vui lòng nhập email hợp lệ.';
    }

    if ($password === '') {
        $errors[] = 'Vui lòng nhập mật khẩu.';
    }

    if (empty($errors)) {
        $user = $userModel->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email hoặc mật khẩu không đúng.';
        } elseif ((int) $user['status'] !== 1) {
            $errors[] = 'Tài khoản đang bị khóa. Vui lòng liên hệ quản trị viên.';
        } else {
            Auth::login($user);
            $redirect = $_GET['redirect'] ?? (Auth::isAdmin() ? PUBLIC_ROOT . '/admin/products.php' : PUBLIC_ROOT . '/index.php');
            header('Location: ' . $redirect);
            exit;
        }
    }
}

$pageTitle = 'Đăng nhập';

include __DIR__ . '/../views/layouts/header.php';
?>

<div class="auth-wrapper">
    <h1>Đăng nhập</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $err): ?>
                <p><?= htmlspecialchars($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="form auth-form">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit" class="btn">Đăng nhập</button>
        <p class="form-note">
            Chưa có tài khoản? <a href="<?= PUBLIC_ROOT ?>/register.php">Đăng ký ngay</a>
        </p>
    </form>
</div>

<?php
include __DIR__ . '/../views/layouts/footer.php';

