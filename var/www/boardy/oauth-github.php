<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
const CLIENT_ID='Ov23liroSRylOpwfB6QV';
$state = bin2hex(random_bytes(16));
$_SESSION['oauth_state'] = $state;


$params = http_build_query([
    'client_id' => CLIENT_ID,
    'redirect_uri' => 'https://gorgeous.ai-info.ru/oauth-callback.php',
    'scope' => 'read:user',
    'state' => $state,
]);

header("Location: https://github.com/login/oauth/authorize?$params");
exit;