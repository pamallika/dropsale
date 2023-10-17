<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class WebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:web-socket';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'WebSocket server up';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new \App\Http\WebSocket\WebSocket()
                )
            ),
            8080
        );

        $server->run();
    }
}
