<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать новый пост</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 antialiased">

<div class="container mx-auto max-w-2xl p-6">
    {{-- Навигация назад --}}
    <div class="mb-6">
        <a href="{{ route('posts.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center gap-2 transition">
            <span>&larr; Вернуться к списку</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Новая публикация</h1>

        {{-- Вывод ошибок валидации --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('posts.store') }}" method="POST" class="space-y-6">
            @csrf {{-- Обязательный токен защиты Laravel --}}

            {{-- Заголовок --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                    Заголовок поста
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('title') border-red-500 @enderror"
                    placeholder="Введите броский заголовок"
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
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition @error('body') border-red-500 @enderror"
                    placeholder="О чем вы хотите рассказать?"
                    required
                >{{ old('body') }}</textarea>
            </div>

            {{-- Кнопки --}}
            <div class="flex items-center justify-end gap-4 pt-4 border-t">
                <a href="{{ route('posts.index') }}" class="text-gray-600 hover:text-gray-800 font-medium transition">
                    Отмена
                </a>
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-md hover:shadow-lg transform active:scale-95 transition"
                >
                    Опубликовать
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
