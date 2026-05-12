<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

require_once 'db.php';

const CLIENT_SECRET='94b634dad3c833ba862f9c30a3d67b2281bc1eb9';
const CLIENT_ID='Ov23liroSRylOpwfB6QV';

if(($_GET['state'] ?? '') !== ($_SESSION['oauth_state'] ?? '')) {
    die('Invalid state — possible CSRF attack');
}


$ch = curl_init('https://github.com/login/oauth/access_token');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'code' => $_GET['code'],
    ]),
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);

$response = json_decode(curl_exec($ch), true);
curl_close($ch);
$access_token = $response['access_token'];

$ch = curl_init('https://api.github.com/user');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer $access_token",
        "User-Agent: Boardy"
    ],
    CURLOPT_RETURNTRANSFER => true,
]);

$profile = json_decode(curl_exec($ch), true);
curl_close($ch);

$stmt = $pdo->prepare('SELECT id, name FROM users WHERE github_id = ?');
$stmt->execute([$profile['id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user) {
    $stmt = $pdo->prepare('INSERT INTO users (name, github_id, email, password) VALUES (?, ?, ?, ?)');
    $stmt->execute([$profile['login'], $profile['id'], 'github@github.com', 'github_password']);
    $user = ['id' => $pdo->lastInsertId(), 'name' => $profile['login']];
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
header('Location: /messages.php');
exit;