<?php

namespace App\Http\Integrations;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application as ApplicationAlias;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RandomUsers implements UsersAdapterInterface
{
    public function formatUsers(array $users): array
    {
        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'fullname_key' => $user['name']['first'] . $user['name']['last'],
                'first_name' => $user['name']['first'],
                'last_name' => $user['name']['last'],
                'email' => $user['email'],
                'age' => $user['dob']['age'],
            ];
        }
        return $data;
    }

    public function getUsers(): array|Application|ResponseFactory|ApplicationAlias|Response
    {
        $countUsers = 5000;
        $params = [
            'verify' => false,
            'query' => ['results' => $countUsers]
        ];
        $url = 'https://randomuser.me/api/';
        $client = new Client();
        try {
            $request = $client->get($url, $params);
            return json_decode($request->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            return response(['error' => $e->getMessage()], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
