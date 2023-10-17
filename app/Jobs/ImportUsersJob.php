<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $users;

    /**
     * Create a new job instance.
     */
    public function __construct(array $users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Пробуем Записать сразу всю пачку
            User::insert($this->users);
            sendWebSocketData('insert_user_count', ['insert_user_count' => count($this->users)]);
        } catch (\PDOException $e) {
            //Отлавливаем исключение что запись уже существует
            //Если юзер 1 - значит именно этот пользователь присутствует. Обновляем
            if (count($this->users) === 1) {
                sendWebSocketData('update_user_count', ['update_user_count' => 1]);
                $userData = $this->users[0];
                /** @var User $user */
                $user = User::where('fullname_key', $userData['fullname_key'])->first();
                $user->update(['email' => $userData['email'], 'age' => $userData['age']]);
                return;
            }
            //Если пользователь не один - делим массив пользователей и вызываем этот job рекурсивно,(разделяй и властвуй)
            //пока в массиве не останется 1 пользователь
            $users = array_chunk($this->users, ceil(count($this->users) / 2));
            foreach ($users as $user) {
                self::dispatch($user);
            }
            return;
        }
    }
}
