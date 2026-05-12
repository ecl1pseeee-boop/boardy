<?php

session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
]);
session_start();

require_once 'db.php';

if(isset($_SESSION['user_id'])) {
    header('Location: messages.php');
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)) {
        $error = 'Заполните поля';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: messages.php');
            exit;
        } else {
            $error = 'Неверный логин или пароль';
        }
    }
}

include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/nav.php';
?>
<div class="form-card">
    <h1>Вход</h1>
    <?php if (isset($error)): ?>
        <div class="error-message" style="color: red;"><?= $error ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="ivan@example.com">
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" placeholder="••••••••">
        </div>
        <button type="submit" class="btn-submit">Войти</button>
    </form>
    <p class="divider">или</p>
    <div class="github-login">
        <img src="images/github-logo.png" alt="github-logo" class="github-logo">
        <a href="/oauth-github.php" target="_top" class="github-text">Войти через GitHub</a>
    </div>
    <div class="form-footer">
        Нет аккаунта? <a href="register.php">Регистрация</a>
    </div>
</div>
<?php include __DIR__ . '/partials/foot.php'; ?>