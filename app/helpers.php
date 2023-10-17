<?php

use WebSocket\Client;

if (! function_exists('sendWebSocketData')) {

    /**
     * Возвращает данные по вебсокету на фронт
     */
    function sendWebSocketData(string $type, array $data): void
    {
        $result = [
            'type' => $type,
            'data' => $data
        ];
        $client = new Client(env('VITE_WEBSOCKET_URI', 'ws://localhost:8080'));
        $client->text(json_encode($result));
        $client->receive();
        $client->close();
    }
}
