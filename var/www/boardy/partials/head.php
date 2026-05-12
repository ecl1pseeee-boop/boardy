<?php
session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
// Должен быть вызван до того, как отправится весь html-код.
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Boardy</title>
    <link rel="stylesheet" href="css/new_style.css">
</head>
<body>