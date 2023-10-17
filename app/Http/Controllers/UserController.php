<?php

namespace App\Http\Controllers;

use App\Http\Integrations\RandomUsers;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index()
    {
        return [
            'result' =>
                ['users_count' => DB::table('users')->count()]
        ];
    }
    public static function getImportUsersData(): array
    {
        return (new RandomUsers())->getUsers();
    }
}
