# Отчет по лабораторной работе №12

## Блок A: Composer + laravel + структура папок

### 1. Composer и PHP-расширение
![composer-php](screenshots/01-composer-php.png)

### 2. Переезд папок
![JSON-ответ с token](screenshots/02-folders-and-03-laravel-version.png)

### 3. Структура Laravel
public/ — единственная папка, которую видит nginx.
app/ — где пишете код (Models, Controllers, Policies).
routes/web.php — описание URL → контроллер.
resources/views/ — Blade-шаблоны.
database/migrations/ — миграции. database/seeders/ — тестовые данные.
.env — секреты. 
.env.example — шаблон без секретов (коммитим).


### 4. Nginx-конфиг
![nginx-conf](screenshots/04-nginx-config.png)
![laravel welcome](screenshots/05-laravel-welcome.png)

Nginx должен сначала попробовать отдать файл, а если не нашёл — отдать запрос на index.php.
location / { try_files $uri $uri/ /index.php?$query_string; }
Сначала пробуй отдать файл по $uri. Попробуй отдать папку $uri/. Если есть — отдай index.html в ней.
/index.php?$query_string — иначе передай управление в index.php с параметрами URL.
Без неё /posts/3 даст 404.

## Блок B: БД, миграции, сидер

### 5. Создание БД boardy_main
![databases](screenshots/06-databases.png)
У старой boardy схема под чистый PHP: password_hash вместо password, может быть username вместо name. 
Подгонять под Laravel-конвенции дороже, чем создать с нуля.![img.png](screenshots/08-migrate-status.png)

### 6. Подключение Laravel к БД
![pdo](screenshots/07-tinker-pdo.png)

### 7. Миграции posts и comments
![migrate](screenshots/08-migrate-status.png)
![tables](screenshots/09-show-tables.png)

### 8. Модели со связями
![model-relations](screenshots/10-model-relations.png)

### 9. Сидер
![seed](screenshots/11-seed-counts.png)


## Блок С: CRUD постов и комментариев

В следующих скриншотах будет открыто SSH-соединение через терминал в PhpStorm для удобства разработки.

### 10. Маршруты
![routes](screenshots/12-route-list.png)

### 11. Лента постов
Пагинация расположена в правом нижнем углу, посты отрисованы (есть посты выше по странице)
![post-index](screenshots/13-posts-index.png)

### 12. Страница поста с комментариями
![post-show](screenshots/14-post-show.png)

### 13. Создание поста
![post-create](screenshots/15-post-create.png)

На этом этапе создание поста требует пользователя:
$post = $request->user()->posts()->create($validated);
Поэтому временно я этот пункт оставлю, сделаю авторизацию и вернусь к этому пункту, прикреплю скриншот, что post создается.
Middleware 'auth' создавался только для создания comments, а для posts не создавался, поэтому laravel возвращает мне ошибку при создании post.
Однако форма создания уже работает и показана на 15-post-create.png

![post-created](screenshots/16-post-after-create.png)

### 14. Policy и редактирование
Добавил кнопки "Редактировать", "Удалить".
![edit-own](screenshots/17-edit-own.png)
![403](screenshots/18-edit-foreign-403.png)

Строчек кода на чистом PHP было намного больше. Каждый раз нам приходилось проверять $_SESSION, лезть в базу по session_id. В laravel это 1 строка в контроллере. Вся остальная работа с cookie скрыта под капотом в laravel.

### 15. Удаление поста
Post "test" удален и пропал из ленты.
![post deleted](screenshots/19-post-deleted.png)

### 16. Комментарий через Blade
![comment created](screenshots/20-comment-created.png)

### Часть D. Breeze + Socialite

### 17. Установка Breeze
![register](screenshots/21-register.png)
![login](screenshots/22-login.png)

### 18. Регистрация и вход
В правом верхнем углу "gorgeous". Мое имя.
![after_reg](screenshots/23-after-register.png)

### 19. GitHub OAuth-приложение
![github app](screenshots/24-github-app.png)

### 20. Socialite
![login github](screenshots/25-login-with-github.png)

### 21. Полный OAuth flow
![github authorize](screenshots/26-github-authorize.png)
![after_github_login](screenshots/27-after-github-login.png)
![mysql](screenshots/28-mysql-github-id.png)

Разница в объеме и сложности кода колоссальна.
1. Сравнение количества строк (примерно):
Lab11 (Чистый PHP): 100–150 строчек кода
    вручную формировать URL для редиректа
    использовать curl для обмена временного code на access_token,
    делать еще один запрос к API GitHub для получения данных пользователя
    вручную обрабатывать JSON и т.д
2. Lab12 (Socialite): 10–15 строк в контроллере.

2. Что сократилось?
Формирование запросов: Больше не нужно вручную прописывать эндпоинты GitHub (github.com/login/oauth/authorize, и т.д.) и заголовки Accept: application/json
Ошибки: Socialite автоматически обрабатывает неудачные попытки авторизации
Безопасность: Фреймворк сам генерирует и проверяет параметр state в сессии.
Парсинг: Метод user() сам делает запрос к API и возвращает объект. Не нужно делать json_decode.

Сокращение: Laravel Socialite, инкапсулирующий всю логику протокола OAuth 2.0.
Socialite::driver('github')->redirect() и Socialite::driver('github')->user().

### 22. Что осталось от прошлых практик
У вас на VPS лежат /var/www/boardy-legacy/ (старый PHP) и БД boardy. Зачем мы их не удалили? Что произойдёт, если попробовать открыть https://фамилия.ai-info.ru/login.php (старый PHP-логин)?
1. Старые проекты в реальной разработке оставляют по нескольким причинам:
a. Можно обратиться в старую БД и перенести оттуда пользователей или другую важную информацию.
б. Можно сравнить старый код на чистом PHP и Framework.
в. Если вдруг в новом проекте мы обнаружим критический баг, то можно вернуться к старой версии.
2. При попытке открыть https://gorgeous.ai-info.ru/login.php мы получим 404 ошибку. 
В конфиге nginx мы указали root /var/www/boardy/public. Веб-сервер ищет файлы только внутри этой папки, а файла login.php там нет. Nginx вообще не знает о существовании /var/www/boardy-legacy, если не указать это в конфигурации.

### 23. FastAPI и React

FastAPI продолжает работать на api.фамилия.ai-info.ru, а React-файлы лежат в Lab9–11. Но в Laravel-проекте мы их не используем. Почему сейчас не используем — что мешает интегрировать? Где они нам пригодятся в Lab13?
1. Почему не используем старые файлы?
а. Laravel использует blade-шаблоны (SSR), а React (CSR). Чтоб их объединить, React-компоненты внутрь Blade-шаблонов (что усложняет сборку через Vite), либо полностью переходить на API-интерфейс, отказываясь от Blade.
б. React настроен на работу с JWT-токенами через заголовок Authorization, а в laravel мы используем сессии и куки. Так что придется настраивать единый центр авторизации (laravel - поставщик токеном для FastApi), чтоб они могли узнавать одного и того же пользователя 

2. Где пригодятся в Lab13?
Я предположу, что в lab13 нас ждут WebSockets. В таком случае будет оправдано следующее разделение:
Laravel продолжит отвечать за "тяжелые" задачи: регистрацию, хранение постов и общую структуру сайта.
FastAPI - быстрый асинхронного сервера для Real-time событий (сокетов).
React - front, который объединяет их и сможет одновременно отображать данные из Laravel и мгновенно обновлять их при получении сигналов от FastAPI без перезагрузки страницы.
### 24. Реалтайм
Сейчас комментарии появляются только после F5. Какое архитектурное решение нам нужно, чтобы один пользователь видел новый комментарий другого без перезагрузки? Какие два сервера-кандидата для этого решения и почему именно они?
Архитектурное решение: WebSockets
Я предположу, что "два сервера-кандидата" - это относится к тому, какая именно технология будет физически держать открытое соединение. В таком случае есть несколько вариантов:
1. FastAPI(Python)
Почему: FastAPI асинхронен. Он может удерживать тысячи открытых соединений одновременно, потребляя минимум оперативной памяти.
Роль: Laravel записывает комментарий в базу и уведомляет FastAPI (через Redis), а FastAPI рассылает это событие всем подключенным React-клиентам.
2. Laravel Reverb
Это высокопроизводительный WebSocket-сервер, написанный на самом PHP, но работающий в режиме постоянного цикла (event loop).
Почему: Обеспечивает идеальную интеграцию с Laravel Echo на фронтенде. Вам достаточно прописать в коде broadcast(new CommentCreated($comment)), и Reverb сам позаботится о доставке.
3. Есть еще другие варианты, к примеру, Node.js, но в контексте нашего проекта он нам не понадобится.  

Если же мы говорим про то, что у нас будет запущено, то, конечно, это:
1. Laravel, который отвечает за тяжелые задачи
2. FastAPI - будет держать открытое соединение.