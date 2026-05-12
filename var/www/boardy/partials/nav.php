<nav class="navbar">
    <div class="nav-left">
        <div class="logo">Boardy</div>
        <a href="messages.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : '' ?>">Все посты</a>

        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="feedback.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'submit.php' ? 'active' : '' ?>">Добавить пост</a>
        <?php endif; ?>
    </div>

    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
        <span class="user-greeting">Привет, <?= htmlspecialchars($_SESSION['user_name']) ?>!</span>
        <a href="logout.php" class="nav-link auth-btn">Выйти</a>
        <?php else: ?>
        <a href="login.php" class="nav-link">Вход</a>
        <a href="register.php" class="nav-link auth-btn">Регистрация</a>
        <?php endif; ?>
    </div>
</nav>