{{-- resources/views/posts/index.blade.php --}}

@extends('layouts.app') {{-- Предполагаем, что у вас есть базовый макет --}}

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Последние посты</h1>

        <div id="posts-feed" class="space-y-8">
            @forelse ($posts as $post)
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    {{-- Заголовок поста --}}
                    <h2 class="text-2xl font-semibold text-blue-600 mb-2">
                        <a href="{{ route('posts.show', $post->id) }}" class="hover:underline">
                            {{ $post->title }}
                        </a>
                    </h2>

                    {{-- Мета-данные (Автор и Дата) --}}
                    <p class="text-gray-500 text-sm mb-4">
                        Опубликовано: {{ $post->created_at->format('d.m.Y H:i') }}
                        | Автор: <strong>{{ $post->author->name ?? 'Аноним' }}</strong>
                    </p>

                    {{-- Содержимое поста --}}
                    <div class="text-gray-700 leading-relaxed mb-6">
                        {{ Str::limit($post->body, 200) }}
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500">Постов пока нет.</p>
            @endforelse
        </div>

        {{-- Пагинация --}}
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>

    <script>
        const wsUrl = 'ws://localhost:8000/ws'
        const API = '{{ app()->environment("production") ? "https://".config("app.fastapi_domain") : "http://localhost:8000" }}'

        function escapeHtml(str) {
            const d = document.createElement('div')
            d.textContent = str == null ? '' : String(str)
            return d.innerHTML
        }

        const formatDate = (isoString) => {
            if (!isoString) return ''
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit', month: '2-digit', year: 'numeric',
                hour: '2-digit', minute: '2-digit',
            }).format(new Date(isoString)).replace(',', '')
        }

        // --- WebSocket: слушаем new_comment ---
        function connect() {
            const ws = new WebSocket(wsUrl)
            ws.onopen = () => console.log('[WS] connected')
            ws.onmessage = (e) => {
                const msg = JSON.parse(e.data)
                if (msg.type === 'new_comment' && Number(msg.comment.post_id) === CURRENT_POST_ID) {
                    appendComment(msg.comment)
                }
            }
            ws.onclose = () => setTimeout(connect, 3000)
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
        }

        connect()
    </script>

    {{-- Загрузка PKCE auth + обработчик формы (модуль, чтобы import работал) --}}
    <script type="module">
        import { handleCallback } from '{{ asset("js/auth.js") }}'
        handleCallback().then(t => {
            if (t) {
                localStorage.setItem('access_token', t)
                console.log('[OAuth] access_token сохранён, длина =', t.length)
            }
        }).catch(e => console.error('[OAuth callback]', e))
    </script>
@endsection
