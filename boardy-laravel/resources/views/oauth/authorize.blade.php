<!DOCTYPE html>
<html lang="ru">
<head><meta charset="utf-8"><title>Авторизация — Boardy</title></head>
<body>
<h1>Запрос доступа</h1>
<p><strong>{{ $client->name }}</strong> запрашивает доступ к вашему аккаунту.</p>

@if (count($scopes) > 0)
    <p>Запрашиваемые права:</p>
    <ul>
        @foreach ($scopes as $scope)
            <li>{{ $scope->description }}</li>
        @endforeach
    </ul>
@endif

{{-- Разрешить: POST /oauth/authorize --}}
<form method="post" action="/oauth/authorize">
    @csrf
    <input type="hidden" name="state" value="{{ $request->state }}">
    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
    <input type="hidden" name="auth_token" value="{{ $authToken }}">
    <button type="submit">Разрешить</button>
</form>

{{-- Отклонить: DELETE /oauth/authorize --}}
<form method="post" action="/oauth/authorize">
    @csrf
    @method('DELETE')
    <input type="hidden" name="state" value="{{ $request->state }}">
    <input type="hidden" name="client_id" value="{{ $client->getKey() }}">
    <input type="hidden" name="auth_token" value="{{ $authToken }}">
    <button type="submit">Отклонить</button>
</form>
</body>
</html>
