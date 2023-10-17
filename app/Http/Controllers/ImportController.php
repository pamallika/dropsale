<?php

namespace App\Http\Controllers;

use App\Http\Integrations\RandomUsers;
use App\Jobs\ImportUsersJob;
use Illuminate\Contracts\Foundation\Application as ApplicationAlias;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\Response;

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
