<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Azate\LaravelTelegramLoginAuth\TelegramLoginAuth;

class TelegramController extends Controller
{
    /**
     * @var TelegramLoginAuth
     */
    protected $telegram;

    /**
     * AuthController constructor.
     *
     * @param TelegramLoginAuth $telegram
     */
    public function __construct(TelegramLoginAuth $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Get user info and log in (hypothetically)
     *
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function handleTelegramCallback()
    {
        if ($this->telegram->validate()) {
            $telegramUser = $this->telegram->user();
            $existingUser = User::where('telegram_id', $telegramUser['id'])->first();

            if ($existingUser) {
                auth()->login($existingUser, true);
            } else {
                $newUser = new User([
                    'telegram_id' => $telegramUser['id'],
                    'first_name'  => $telegramUser['first_name'] ?? '',
                    'last_name'   => $telegramUser['last_name'] ?? '',
                    'username'    => $telegramUser['username'] ?? '',
                ]);
                $newUser->save();
                auth()->login($newUser, true);
            }
        }

        return route('home');
    }
}
