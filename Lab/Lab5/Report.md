# Отчет по лабораторной работе:

### 1. Установка certbot
![certbot](screenshots/01-certbot-installed.png)

### 2. Получение сертификата
![certbot](screenshots/02-certbot-success.png)

### 3. Проверка в браузере
![browser](screenshots/03-browser-lock.png)
![cert info](screenshots/04-certificate-info.png)

### 4. Редирект
![redirect](screenshots/05-redirect.png)

### 5. Конфиг после certbot
![config](screenshots/06-nginx-ssl-config.png)

### 6. Сертификат для api-поддомена
![certbot for api](screenshots/07-api-certbot.png)

### 7. Проверка обоих доменов
![domen check](screenshots/08-both-https.png)

### 8. TLS handshake
![tls](screenshots/09-tls-handshake.png)

### 9. Цепочка доверия
Браузер проверяет снизу вверх. Сертификат - Let's Encrypt. Let's Encrypt подписан ISRG Root X1. ISRG Root встроен в ОС.
Если все сходится, то появляется замочек.
![chain](screenshots/10-chain.png)

### 10. Сравнение сертификатов
Сертификаты отличаются subject.
![compare](screenshots/11-compare-certs.png)

### 11. HSTS
Браузер запоминает, что сайт — только HTTPS. Если же пользователь решит зайти через "http://gorgeous.ai-info.ru", то браузер сам заменит на "https://gorgeous.ai-info.ru" еще до отправки запроса
![hsts](screenshots/12-hsts.png)

### 12. Кэширование и gzip
![cache, gzip](screenshots/13-cache-gzip.png)

### 13. Автообновление
![renew](screenshots/14-renew.png)