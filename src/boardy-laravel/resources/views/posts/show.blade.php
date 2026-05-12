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

        <div class="space-y-6">
            @forelse($post->comments as $comment)
                <div class="flex space-x-4 p-4 rounded-lg bg-gray-50 border border-gray-100">
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
                <div class="text-center py-8 text-gray-400 italic">
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

            <form action="{{ route('comments.store') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Скрытое поле для передачи ID поста --}}
                <input type="hidden" name="post_id" value="{{ $post->id }}">

                <textarea
                    name="body"
                    rows="3"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('body') border-red-500 @enderror"
                    placeholder="Ваш комментарий..."
                    required
                >{{ old('body') }}</textarea>

                @error('body')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition shadow-md">
                    Отправить
                </button>
            </form>
        </div>
    </section>
</div>

</body>
</html>
