import { generateVerifier, generateChallenge, generateState } from './pkce.js'

const CLIENT_ID    = '019e7a3e-d76e-7107-9511-cb75f960a915 '
const LARAVEL_BASE = window.location.origin
const REDIRECT_URI = LARAVEL_BASE + '/oauth/callback'

export async function startLogin() {
    const verifier  = generateVerifier()
    const challenge = await generateChallenge(verifier)
    const state     = generateState()

    sessionStorage.setItem('pkce_verifier', verifier)
    sessionStorage.setItem('oauth_state', state)

    const params = new URLSearchParams({
        client_id: CLIENT_ID,
        response_type: 'code',
        redirect_uri: REDIRECT_URI,
        code_challenge: challenge,
        code_challenge_method: 'S256',
        state: state,
        scope: '*',
    })
    window.location = LARAVEL_BASE + '/oauth/authorize?' + params
}

export async function handleCallback() {
    const params = new URLSearchParams(window.location.search)
    const code  = params.get('code')
    const state = params.get('state')
    if (!code) return null

    // проверка state — защита от CSRF
    const savedState = sessionStorage.getItem('oauth_state')
    if (!state || state !== savedState) {
        throw new Error('Invalid state — возможна CSRF-атака')
    }
    const verifier = sessionStorage.getItem('pkce_verifier')
    if (!verifier) throw new Error('Нет code_verifier в sessionStorage')

    // тело — x-www-form-urlencoded (URLSearchParams), НЕ JSON.
    // Passport читает только form-параметры; с JSON будет unsupported_grant_type.
    const res = await fetch(LARAVEL_BASE + '/oauth/token', {
        method: 'POST',
        credentials: 'include',          // чтобы применился Set-Cookie с refresh
        body: new URLSearchParams({
            grant_type: 'authorization_code',
            client_id: CLIENT_ID,
            code: code,
            code_verifier: verifier,
            redirect_uri: REDIRECT_URI,
        }),
    })
    if (!res.ok) throw new Error('Обмен code не удался: ' + res.status)
    const data = await res.json()

    sessionStorage.removeItem('pkce_verifier')
    sessionStorage.removeItem('oauth_state')

    return data.access_token             // refresh_token уже ушёл в HttpOnly-cookie
}

export async function refreshToken() {
    // refresh_token из JS НЕ передаём — он в HttpOnly-cookie,
    // его подставит в тело запроса Laravel-middleware RefreshTokenCookie.
    const res = await fetch(LARAVEL_BASE + '/oauth/token', {
        method: 'POST',
        credentials: 'include',          // HttpOnly-cookie уедет автоматически
        body: new URLSearchParams({
            grant_type: 'refresh_token',
            client_id: CLIENT_ID,
            scope: '*',
        }),
    })
    if (!res.ok) {
        startLogin()                     // refresh протух → на логин
        return null
    }
    const data = await res.json()
    return data.access_token
}


// 1) Клик «Войти через OAuth»
document.getElementById('login-btn')?.addEventListener('click', startLogin);

// 3) Для отладки из консоли
window.startLogin     = startLogin;
window.handleCallback = handleCallback;
window.refreshToken   = refreshToken;


