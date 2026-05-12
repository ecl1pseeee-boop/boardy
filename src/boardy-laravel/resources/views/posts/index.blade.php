{{-- resources/views/posts/index.blade.php --}}

@extends('layouts.app') {{-- Предполагаем, что у вас есть базовый макет --}}

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold mb-6">Последние посты</h1>

        <div class="space-y-8">
            @forelse ($posts as $post)
                <article class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
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

                    {{-- Секция комментариев --}}
                    <div class="bg-gray-50 p-4 rounded-md">
                        <h3 class="font-medium mb-3 text-gray-800">
                            Комментарии ({{ $post->comments->count() }}):
                        </h3>

                        @if($post->comments->isEmpty())
                            <p class="text-gray-400 italic text-sm">К этому посту пока нет комментариев.</p>
                        @else
                            <ul class="space-y-2">
                                @foreach ($post->comments->take(3) as $comment) {{-- Показываем только последние 3 --}}
                                <li class="text-sm border-b border-gray-100 pb-2">
                                    <span class="font-semibold text-gray-600">{{ $comment->user->name ?? 'Гость' }}:</span>
                                    {{ $comment->body }}
                                </li>
                                @endforeach
                            </ul>

                            @if($post->comments->count() > 3)
                                <a href="{{ route('posts.show', $post->id) }}" class="text-xs text-blue-500 mt-2 inline-block">
                                    Посмотреть все комментарии...
                                </a>
                            @endif
                        @endif
                    </div>
                </article>
            @empty
                <p class="text-center text-gray-500">Постов пока нет.</p>
            @endforelse
        </div>

        {{-- Пагинация --}}
        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
@endsection
