# Установка  
composer install --ignore-platform-reqs  
npm i  
cp .env.example .env  
DB_PASSWORD=root  
php artisan key:generate  
php artisan migrate  

# Запуск  
docker-compose up -d  
php artisan run serve  
npm run dev  
php artisan app:web-socket  
php artisan queue:work  

# Что под капотом  
PHP(Laravel)  
Guzzle для запроса со стронннего Api  
JS(Vue)  
Система очередей(встроенная в Laravel через database)  
WebSocket  

# Что хорошего 
1) Записать 5000 пользователей в базу пробуем пачками по 25000 через две задачи  
Если не получается этот массив делится на два и пробрасывается в новые jobs,    
до тех пор пока массив не будет состаять из одного пользователя(значит этот пользователь уже есть в БД и у него обновляем email и age)  
Это будет значительно быстрее чем записывать по-одному
2) Для формаирования данных с api использую паттерн адаптер
3) Всё работает без обновления страницы
4) Данные по выполнению импорта приходят по вебсокету, можно много что придумать(прелоадеры, какие-нибудь графики и т.д.)
5) Система очередей, которая ускоряет работу импорта

# Что можно улучшить  
По хорошему нужно было бы настроить supervisor, но пока без него(чтобы можно было гибко настраивать воркеры, а не запускать каждый в отдельном терминале)   
Систему очередй лучше было использовать Kafka, если мы готовимся к большой нагрузке  

# Задача  
- Интеграция с сервисом https://randomuser.me/api/  
- Получение оттуда пользователей(в особо крупном размере = 5000) и импорт в свою БД  
- Если пользователь повторяется - обновляем age, email  
- Отображать в веб интерфейсе -> сколько всего пользователей в БД, сколько добавлено и сколько обновлено  
- Основной фокус задачи направлен на импорт большого кол-ва записей. Поэтому постарайтесь уделить особое внимание скорости вставки и обновления записей в базе данных.
- Следует учесть что кол-во записей в базе данных со временем может достигать нескольких миллионов пользователей.
