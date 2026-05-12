<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();


if(empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_decode(['error' => 'Not authenticated']);
    exit;
}

$jwt = generate_jwt($_SESSION['user_id'], $_SESSION['user_name']);
header('Content-Type: application/json');
echo json_encode(['token' => $jwt]);

function generate_jwt(int $user_id, string $user_name): string
{
    $secret_key = 'VKWrxWcbTCe8VxKGji76UxreYTPV0XWRyDaWGT17oL+ZK3K+6fZcRW966L4AYqAs';
    $header = rtrim(strtr(base64_encode(json_encode( ['alg' => 'HS256', 'typ' => 'JWT'] )), '+/', '-_'), '=');

    $payload = rtrim(strtr(base64_encode(json_encode([
        'user_id' => $user_id,
        'name' => $user_name,
        'exp' => time() + 3600,
    ])), '+/', '-_'), '=');

    $signature = rtrim(strtr(base64_encode(
        hash_hmac('sha256', "$header.$payload", $secret_key, true)
    ), '+/', '-_'), '=');

    $jwt = "$header.$payload.$signature";
    return $jwt;
}