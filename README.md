DB_PASSWORD=root
php artisan queue:work
php artisan app:web-socket
npm run dev
php artisan serve
docker-compose up -d
VITE_WEBSOCKET_URI="ws://localhost:8080"
