# Отчет по лабораторной работе

### 1. Установка Nginx
![Nginx](screenshots/01-nginx-status.png)

### 2. Страница по IP
![IP](screenshots/02-browser-ip.png)

### 3. Curl
![Curl](screenshots/03-curl.png)

### 4. Директория и права
![Директория](screenshots/04-permissions.png)

### 5. Конфигурация Nginx
``` listen 80 default_server ``` - какой порт слушать
``` root /var/www/html ``` - откуда брать файлы
``` server_name _ ``` - имя хоста
``` index index.html index.htm index.nginx-debian.html ``` - где лежит index файл

## Далее будет работа с DNS-зонами. Но пока что у меня нет доступа к gorgeous.ai-info.ru, поэтому я взял dns зону, которую предлагает netangels
### 6. DNS-зона
![DNS](screenshots/05-dns-zone.png)

### 7. A-запись
![A-запись](screenshots/06-a-record.png)

### 8. ping
![A-запись](screenshots/07-ping.png)

### 9. dig
![dig](screenshots/08-dig-wsl.png)
![dig](screenshots/08-dig.png)

### 10. dig + trace
![dig+trace](screenshots/09-dig-trace.png)
![dig+trace+wsl](screenshots/09-dig-trace-wsl.png)

### 11. Сайт по домену
![browser-domain](screenshots/10-browser-domain.png)
