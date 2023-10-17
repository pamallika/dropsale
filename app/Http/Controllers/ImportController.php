<?php

namespace App\Http\Controllers;

use App\Events\ImportEvent;
use App\Http\Integrations\RandomUsers;
use App\Http\WebSocket\WebSocket;
use App\Jobs\ImportUsersJob;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ImportController extends Controller
{
    public function randomUsersImport(): Application|Response|ApplicationAlias|ResponseFactory
    {
        $users = UserController::getImportUsersData();
        ImportController::addJob($users['results']);
        return response('Import Started');
    }
    /**
     * Функция делит массив с пользователями и создаёт Job
     */
    public static function addJob(array $data): void
    {
        $countChunk = ceil(count($data) / 2);
        $chunkedResponse = array_chunk($data, $countChunk);
        sendWebSocketData('import_user_count', ['import_user_count' => count($data)]);
        foreach ($chunkedResponse as $chunk) {
            $users = (new RandomUsers())->formatUsers($chunk);
            ImportUsersJob::dispatch($users);
        }
    }
}
