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

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if(empty($name) || empty($email) || empty($password)) {
        $error = 'Заполните поля';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Почта уже используется!';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare('INSERT INTO users (email, name, password) VALUES (?, ?, ?)');
            $stmt->execute([$email, $name, $hashedPassword]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_name'] = $name;

            header("Location: messages.php");
            exit;
        }
    }
}


include 'partials/head.php';
include 'partials/nav.php';
?>
<div class="form-card">
    <h1>Регистрация</h1>
    <?php if (isset($error)): ?>
        <div class="error-message" style="color: red;"><?= $error ?></div>
    <?php endif; ?>
    <form action="register.php" method="POST">
        <div class="form-group">
            <label>Имя</label>
            <input type="text" name="name" placeholder="Иванов Иван">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="ivan@example.com">
        </div>
        <div class="form-group">
            <label>Пароль</label>
            <input type="password" name="password" placeholder="••••••••">
        </div>
        <button type="submit" class="btn-submit">Зарегистрироваться</button>
    </form>
    <p class="divider">или</p>
    <div class="github-login">
        <img src="images/github-logo.png" alt="github-logo" class="github-logo">
        <a href="/oauth-github.php" class="github-text">Войти через GitHub</a>
    </div>
    <div class="form-footer">
        Уже есть аккаунт? <a href="login.php">Войти</a>
    </div>
</div>
<?php include 'partials/foot.php'; ?>