import { startLogin, handleCallback, refreshToken } from './auth.js'

const CURRENT_HOST = window.location.host;
const PROTOCOL     = window.location.protocol; // http: или https:
const WS_PROTOCOL  = PROTOCOL === 'https:' ? 'wss:' : 'ws:';

const API   = `${PROTOCOL}//${CURRENT_HOST}`;
const WSURL = `${WS_PROTOCOL}//${CURRENT_HOST}/ws`;

function Comments({ postId, userName }) {
    const [token, setToken]       = React.useState(localStorage.getItem('access_token'))
    const [comments, setComments] = React.useState([])
    const [text, setText]         = React.useState('')
    const wsRef = React.useRef(null)

    // 1. Обработать OAuth callback
    React.useEffect(() => {
        handleCallback().then(t => {
            if (t) {
                localStorage.setItem('access_token', t)
                setToken(t)
            }
        })
    }, [])

    // 2. Начальная загрузка + WebSocket
    React.useEffect(() => {
        // начальная подгрузка
        fetch(`${API}/api/posts/${postId}/comments`)
            .then(r => r.json())
            .then(setComments)

        // подключение WebSocket
        // если WS требует токен — добавь ?token=${token}
        const ws = new WebSocket(`${WSURL}/ws/posts/${postId}/comments`)
        wsRef.current = ws

        ws.onmessage = (ev) => {
            const newComment = JSON.parse(ev.data)
            setComments(prev => {
                // защита от дублей (если бэк присылает echo нашего же сообщения)
                if (prev.some(c => c.id === newComment.id)) return prev
                return [...prev, newComment]
            })
        }
        ws.onerror = (e) => console.error('[WS error]', e)
        ws.onclose = () => console.log('[WS] closed')

        return () => ws.close()
    }, [postId])

    async function authedFetch(url, options = {}) {
        const withAuth = (t) => ({
            ...options,
            headers: { ...options.headers, 'Authorization': 'Bearer ' + t },
        })
        let res = await fetch(url, withAuth(token))
        if (res.status === 401) {
            const fresh = await refreshToken()
            if (!fresh) return null
            localStorage.setItem('access_token', fresh)
            setToken(fresh)
            res = await fetch(url, withAuth(fresh))
        }
        return res
    }

    async function submit(e) {
        e.preventDefault()
        const res = await authedFetch(`${API}/api/posts/${postId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body: text, author_name: userName }),
        })
        if (!res || !res.ok) return
        setText('')
        // ничего больше делать не нужно — WS пришлёт нам же echo и добавит в state
    }

    return (
        <div>
            {/* Список */}
            <div className="space-y-6 mb-6">
                {comments.length === 0 && (
                    <p className="text-center py-8 text-gray-400 italic">
                        Здесь пока пусто. Станьте первым!
                    </p>
                )}
                {comments.map(c => (
                    <div key={c.id} className="flex space-x-4 p-4 rounded-lg bg-gray-50 border border-gray-100">
                        <div className="flex-shrink-0">
                            <div className="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                {(c.author_name || 'Г')[0].toUpperCase()}
                            </div>
                        </div>
                        <div className="flex-grow">
                            <h4 className="font-bold text-gray-900 text-sm">{c.author_name || 'Гость'}</h4>
                            <p className="text-gray-700 text-sm">{c.body}</p>
                        </div>
                    </div>
                ))}
            </div>

            {/* Форма */}
            {!token ? (
                <button onClick={startLogin}
                        className="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-6 rounded-lg">
                    Войти через OAuth для комментирования
                </button>
            ) : (
                <form onSubmit={submit} className="space-y-3">
                      <textarea
                          rows="3"
                          value={text}
                          onChange={e => setText(e.target.value)}
                          className="w-full px-4 py-3 border border-gray-300 rounded-lg"
                          placeholder="Ваш комментарий (через API + WS)..."
                          required
                      />
                    <button type="submit" className="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg">
                        Отправить
                    </button>
                </form>
            )}
        </div>
    )
}

const el = document.getElementById('comments-root')
if (el) {
    ReactDOM.createRoot(el).render(
        <Comments postId={el.dataset.postId} userName={el.dataset.userName} />
    )
}
