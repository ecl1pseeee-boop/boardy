<!DOCTYPE html>
<html lang="ru"><head><meta charset="UTF-8"><title>OAuth</title></head>
<body>
<p style="text-align:center;padding:40px;font-family:sans-serif">
    Завершаем вход через OAuth…
</p>
<script type="module">
    import { handleCallback } from '{{ asset("js/auth.js") }}'
    try {
        const t = await handleCallback()
        if (t) {
            localStorage.setItem('access_token', t)
            console.log('[OAuth] access_token сохранён, длина =', t.length)
        }
        window.location.replace('/posts')
    } catch (e) {
        console.error('[OAuth callback]', e)
        document.body.insertAdjacentHTML('beforeend',
            '<p style="color:red;text-align:center">Ошибка: ' + e.message + '</p>')
    }
</script>
</body></html>
