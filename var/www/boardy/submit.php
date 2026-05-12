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

if(empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$message = $_POST['message'] ?? '';
$user_id = $_SESSION['user_id'];


// Создаём пост (prepared statement!)

$stmt = $pdo->prepare(

'INSERT INTO posts (title, body, author_id) VALUES (?, ?, ?)'

);

$stmt->execute(['Сообщение', $message, $user_id]);


?>

<!DOCTYPE html>

<html lang="ru">

<head><meta charset="utf-8"><title>Boardy</title>

<link rel="stylesheet" href="/css/style.css"></head>

<body>

<header><h1><a href="/">Boardy</a></h1></header>

<main>

<h2>Спасибо, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h2>

<p><a href="/">На главную</a> |

<a href="/messages.php">Все сообщения</a></p>

</main>

</body></html>
