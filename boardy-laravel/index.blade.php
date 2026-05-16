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

        @if(app()->environment('production'))
            const wsUrl = 'wss://{{ config("app.fastapi_domain") }}/ws'
        @else
            const wsUrl = 'ws://localhost:8000/ws'
        @endif


        function connect() {
            const ws = new WebSocket(wsUrl)
            ws.onopen = () => console.log('WS connected')
            ws.onmessage = (e) => {
                const msg = JSON.parse(e.data)
                if (msg.type === 'new_post') prependPost(msg.post)
            }

            ws.onclose = () => setTimeout(connect, 3000)
        }

        function prependPost(post) {
            const feed = document.getElementById('posts-feed')
            if (!feed) return
            const el = document.createElement('div')
            el.className = 'bg-white p-6 rounded-lg shadow-md border border-gray-200'
            el.innerHTML = `
<h2 class="text-2xl font-semibold text-blue-600 mb-2">
    <a href="/posts/${escapeHtml(post.id)}" class="hover:underline"> ${escapeHtml(post.title)} </a>
</h2>

<p class="text-gray-500 text-sm mb-4">
    Опубликовано: ${formatDate(escapeHtml(post.created_at))}
    Автор: <strong>${escapeHtml(post.author ? post.author : 'Аноним')}</strong>
</p>

<div class="text-gray-700 leading-relaxed mb-6">
    ${escapeHtml(post.body.length > 200 ? post.body.substring(0, 200) + '...' : post.body)}
</div>
`
            feed.prepend(el)
        }

        function escapeHtml(str) {
            const d = document.createElement('div')
            d.textContent = str
            return d.innerHTML
        }

        const formatDate = (isoString) => {
            if (!isoString) return '';

            const date = new Date(isoString);

            // Форматируем дату под российский стандарт (ДД.ММ.ГГГГ ЧЧ:ММ)
            return new Intl.DateTimeFormat('ru-RU', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
            }).format(date).replace(',', ''); // убираем лишнюю запятую, если она появится между датой и временем
        };

        connect()
    </script>
@endsection
