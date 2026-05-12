<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать: {{ $post->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 antialiased">

<div class="container mx-auto max-w-2xl p-6">
    <div class="mb-6">
        <a href="{{ route('posts.show', $post->id) }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 transition">
            <span>&larr; Отмена и назад</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Редактирование</h1>
            <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded text-gray-500">ID: {{ $post->id }}</span>
        </div>

        {{-- Ошибки валидации --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('posts.update', $post->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT') {{-- Маскируем POST под PUT для Laravel --}}

            {{-- Заголовок --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Заголовок поста
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title', $post->title) }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition @error('title') border-red-500 @enderror"
                    placeholder="Введите заголовок"
                    required
                >
            </div>

            {{-- Содержимое --}}
            <div>
                <label for="body" class="block text-sm font-semibold text-gray-700 mb-2">
                    Текст публикации
                </label>
                <textarea
                    name="body"
                    id="body"
                    rows="10"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none transition @error('body') border-red-500 @enderror"
                    placeholder="Текст поста..."
                    required
                >{{ old('body', $post->body) }}</textarea>
            </div>

            {{-- Кнопки управления --}}
            <div class="flex items-center justify-between pt-4 border-t">
                {{-- Кнопка удаления (опционально, требует отдельной формы или JS) --}}
                <span class="text-xs text-gray-400 italic">
                        Создано: {{ $post->created_at->format('d.m.Y') }}
                    </span>

                <div class="flex gap-4">
                    <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-gray-800 font-medium self-center">
                        Отмена
                    </a>
                    <button
                        type="submit"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transform active:scale-95 transition"
                    >
                        Обновить пост
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

</body>
</html>
