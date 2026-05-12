<?php
require_once 'db.php';


$stmt = $pdo->query(

'SELECT posts.body, users.name, posts.created_at

FROM posts

JOIN users ON posts.author_id = users.id

ORDER BY posts.created_at DESC'

);

$messages = $stmt->fetchAll();

?>


<?php include __DIR__ . '/partials/head.php'; ?>
<?php include __DIR__ . '/partials/nav.php'; ?>

<div class="container">
    <h1>Все посты</h1>

    <?php if (empty($messages)): ?>

        <p>Сообщений пока нет.</p>

    <?php else: ?>

    <?php foreach ($messages as $msg): ?>
    <div class="post-card">
        <div class="post-header">
            <span class="post-author"><?= htmlspecialchars($msg['name']) ?></span>
            <span class="post-time"><?= htmlspecialchars($msg['created_at']) ?></span>
        </div>
        <p><?= htmlspecialchars($msg['body']) ?></p>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
</div>

<?php include 'partials/foot.php'; ?>