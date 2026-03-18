#!/bin/bash

# UTF-8 локаль
export LANG=C.UTF-8
export LC_ALL=C.UTF-8

# Читаем POST-данные
read -n $CONTENT_LENGTH POST_DATA

# Извлечение параметров
NAME=$(echo "$POST_DATA" | grep -o 'name=[^&]*' | cut -d= -f2 | sed 's/+/ /g')
MESSAGE=$(echo "$POST_DATA" | grep -o 'message=[^&]*' | cut -d= -f2 | sed 's/+/ /g')

# Сохраняем в файл
echo "$(date '+%Y-%m-%d %H:%M:%S')|$NAME|$MESSAGE" >> /var/www/boardy/data/messages.txt

# Возвращаем HTML
echo "Content-Type: text/html; charset=utf-8"
echo ""
cat << EOF
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Boardy</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header><h1><a href="/">Boardy</a></h1></header>
    <main>
        <h2>Спасибо, $NAME!</h2>
        <p>Ваше сообщение получено.</p>
        <p><a href="/">На главную</a> | <a href="/cgi-bin/messages.sh">Все сообщения</a></p>
    </main>
</body>
</html>
EOF
