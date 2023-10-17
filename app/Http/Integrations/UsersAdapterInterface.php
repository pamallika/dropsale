<?php

namespace App\Http\Integrations;

interface UsersAdapterInterface
{
    public function formatUsers(array $users): array;
}
