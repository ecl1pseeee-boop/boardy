<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 antialiased">

<div class="container mx-auto max-w-4xl p-6">
    {{-- Кнопка назад --}}
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
            <span>&larr; Назад к списку</span>
        </a>
    </div>

    {{-- Основной контент поста --}}
    <article class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-8">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-4xl font-extrabold text-gray-900 leading-tight">
                    {{ $post->title }}
                </h1>

                {{-- Кнопки управления для автора --}}
                @auth
                    @if(auth()->id() === $post->user_id)
                        <div class="flex gap-2">
                            <a href="{{ route('posts.edit', $post) }}"
                               class="text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition">
                                Редактировать
                            </a>

                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Вы уверены?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg transition">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    @endif
                @endauth
            </div>

            <div class="flex items-center text-sm text-gray-500 mb-8 pb-6 border-b">
                <div class="flex items-center">
                    <span class="font-medium text-gray-900">Автор: {{ $post->author->name ?? 'Аноним' }}</span>
                </div>
                <span class="mx-3">&bull;</span>
                <time>{{ $post->created_at->translatedFormat('j F Y, H:i') }}</time>
            </div>

            <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed">
                {!! nl2br(e($post->body)) !!}
            </div>
        </div>
    </article>

    {{-- Секция комментариев --}}
    <section class="bg-white rounded-xl shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">
            Комментарии <span class="text-gray-400">({{ $post->comments->count() }})</span>
        </h2>

        <div class="space-y-6" id="comments-list">
            @forelse($post->comments as $comment)
                <div data-comment-id="{{ $comment->id }}" class="flex space-x-4 p-4 rounded-lg bg-gray-50 border border-gray-100">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                            {{ Str::upper(Str::substr($comment->author->name ?? 'Г', 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-grow">
                        <div class="flex items-center justify-between mb-1">
                            <h4 class="font-bold text-gray-900 text-sm">
                                {{ $comment->author->name ?? 'Гость' }}
                            </h4>
                            <span class="text-xs text-gray-400">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                        </div>
                        <p class="text-gray-700 text-sm leading-relaxed">
                            {{ $comment->body }}
                        </p>
                    </div>
                </div>
            @empty
                <div id="comments-empty" class="text-center py-8 text-gray-400 italic">   {{-- ★ добавили id --}}
                    Здесь пока пусто. Станьте первым, кто оставит комментарий!
                </div>
            @endforelse
        </div>

        {{-- Форма добавления комментария --}}
        <div class="mt-10 pt-8 border-t">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Оставить комментарий</h3>

            {{-- Вывод сообщения об успехе --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form id="comment-form" class="space-y-4"
                  data-post-id="{{ $post->id }}"
                  data-user-name="{{ auth()->user()->name ?? 'Гость' }}">
    <textarea
        id="comment-body"
        rows="3"
        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
        placeholder="Ваш комментарий..."
        required
    ></textarea>
                <div class="flex items-center gap-4">
                    <button type="submit" id="comment-submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition shadow-md">
                        Отправить
                    </button>
                    <button type="button" id="comment-login"
                            class="hidden bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg">
                        Войти через OAuth для комментирования
                    </button>
                    <span id="comment-status" class="text-sm text-gray-500"></span>
                </div>
            </form>
        </div>
    </section>
</div>
<script>
    const CURRENT_HOST = window.location.host;
    const PROTOCOL     = window.location.protocol; // http: или https:
    const WS_PROTOCOL  = PROTOCOL === 'https:' ? 'wss:' : 'ws:';

    // Запросы идут на шлюз Nginx, который сам проксирует их в FastAPI на порт 8000
    const API   = `${PROTOCOL}//${CURRENT_HOST}`;
    const wsUrl = `${WS_PROTOCOL}//${CURRENT_HOST}/ws`;

    const CURRENT_POST_ID = {{ $post->id }};

    function escapeHtml(str) {
        const d = document.createElement('div')
        d.textContent = str == null ? '' : String(str)
        return d.innerHTML
    }

    const formatDate = (iso) => {
        if (!iso) return ''
        return new Intl.DateTimeFormat('ru-RU', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
        }).format(new Date(iso)).replace(',', '')
    }

    function appendComment(comment) {
        const list = document.getElementById('comments-list')
        if (!list) return
        // защита от дублей (свой же эхо)
        if (list.querySelector(`[data-comment-id="${comment.id}"]`)) return
        // убираем "пусто" если был
        document.getElementById('comments-empty')?.remove()

        const el = document.createElement('div')
        el.dataset.commentId = comment.id
        el.className = 'flex space-x-4 p-4 rounded-lg bg-gray-50 border border-gray-100'
        const authorName = comment.author_name || comment.author?.name || 'Гость'
        el.innerHTML = `
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                    ${escapeHtml(authorName[0].toUpperCase())}
                </div>
            </div>
            <div class="flex-grow">
                <div class="flex items-center justify-between mb-1">
                    <h4 class="font-bold text-gray-900 text-sm">${escapeHtml(authorName)}</h4>
                    <span class="text-xs text-gray-400">${escapeHtml(formatDate(comment.created_at))}</span>
                </div>
                <p class="text-gray-700 text-sm leading-relaxed">${escapeHtml(comment.body)}</p>
            </div>
        `
        list.appendChild(el)

        // обновляем счётчик
        const counter = document.getElementById('comments-count')
        if (counter) {
            const n = list.querySelectorAll('[data-comment-id]').length
            counter.textContent = `(${n})`
        }

        updateCounter()
    }

    function connect() {
        const ws = new WebSocket(wsUrl)
        ws.onmessage = (e) => {
            const msg = JSON.parse(e.data)
            if (msg.type === 'new_comment' && Number(msg.comment.post_id) === CURRENT_POST_ID) {
                appendComment(msg.comment)
            }
        }
        ws.onclose = () => setTimeout(connect, 3000)
    }
    connect()

    async function loadComments() {
        const res = await fetch(`${API}/api/posts/${CURRENT_POST_ID}/comments`)
        if (!res.ok) return
        const comments = await res.json()
        const empty = document.getElementById('comments-empty')

        if (!comments.length) {
            // Добавили знак вопроса (опциональная цепочка) — если empty null, код не упадёт
            if (empty) {
                empty.textContent = 'Здесь пока пусто. Станьте первым, кто оставит комментарий!'
            }
            return
        }
        empty?.remove()
        comments.forEach(appendComment)
        updateCounter()
    }

    function updateCounter() {
        const n = document.querySelectorAll('#comments-list [data-comment-id]').length
        const counter = document.getElementById('comments-count')
        if (counter) counter.textContent = `(${n})`
    }

    loadComments()
</script>

<script type="module">
    import { startLogin, handleCallback, refreshToken } from '{{ asset("js/auth.js") }}'

    const form     = document.getElementById('comment-form')
    const textarea = document.getElementById('comment-body')
    const submit   = document.getElementById('comment-submit')
    const loginBtn = document.getElementById('comment-login')
    const status   = document.getElementById('comment-status')

    function syncButtons() {
        const hasToken = !!localStorage.getItem('access_token')
        submit.classList.toggle('hidden', !hasToken)
        loginBtn.classList.toggle('hidden', hasToken)
    }
    syncButtons()

    loginBtn.addEventListener('click', startLogin)

    async function authedFetch(url, options = {}) {
        const t = localStorage.getItem('access_token')
        const withAuth = (tok) => ({
            ...options,
            headers: { ...options.headers, 'Authorization': 'Bearer ' + tok },
        })
        let res = await fetch(url, withAuth(t))
        if (res.status === 401) {
            const fresh = await refreshToken()
            if (!fresh) return null
            localStorage.setItem('access_token', fresh)
            res = await fetch(url, withAuth(fresh))
        }
        return res
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault()   // ← вот это убирает дефолтный submit браузера
        const body = textarea.value.trim()
        if (!body) return

        status.textContent = 'Отправка...'
        const postId   = form.dataset.postId
        const userName = form.dataset.userName

        const res = await authedFetch(`${API}/api/posts/${postId}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ body, author_name: userName }),
        })

        if (!res)    { status.textContent = 'Войди через OAuth'; syncButtons(); return }
        if (!res.ok) { status.textContent = 'Ошибка ' + res.status; return }

        textarea.value = ''
        status.textContent = 'Отправлено ✓ (ждём WS-эхо)'
    })


</script>
</body>
</html>
