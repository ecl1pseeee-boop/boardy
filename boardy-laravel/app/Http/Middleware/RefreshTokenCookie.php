<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RefreshTokenCookie
{
    public function handle(Request $request, Closure $next)
    {
        // === ВХОД: refresh-грант — достаём refresh_token из HttpOnly-cookie в тело запроса ===
        if ($request->is('oauth/token')
            && $request->input('grant_type') === 'refresh_token'
            && ! $request->filled('refresh_token')
            && $request->hasCookie('refresh_token')) {
            $request->request->set('refresh_token', $request->cookie('refresh_token'));
        }

        $response = $next($request);

        // === ВЫХОД: прячем refresh_token из JSON-ответа в HttpOnly-cookie ===
        if ($request->is('oauth/token') && $response->isOk()) {
            $data = json_decode($response->getContent(), true);
            if (is_array($data) && isset($data['refresh_token'])) {
                $refresh = $data['refresh_token'];
                unset($data['refresh_token']);
                $response->setContent(json_encode($data));

                $response->headers->setCookie(cookie(
                    'refresh_token', $refresh,
                    60 * 24 * 30,   // 30 дней в минутах
                    '/', null,
                    true,           // Secure (на http://localhost браузер cookie всё равно примет)
                    true,           // HttpOnly — JS не достанет
                    false,          // raw
                    'Strict'        // SameSite
                ));
            }
        }
        return $response;
    }
}
