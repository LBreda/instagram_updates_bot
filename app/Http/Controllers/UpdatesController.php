<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\InstagramProfileHelper;
use Telegram\Bot\Laravel\Facades\Telegram;

class UpdatesController extends Controller
{
    use InstagramProfileHelper;

    /**
     * Webhook handler.
     *
     * @return String
     */
    public function updates(): string
    {
        $updates = Telegram::commandsHandler(true);

        foreach ($updates as $update) {
            if ($update['text'] and $update['from']['id']) {
                $user = User::find($update['from']['id']);

                if ($user) {
                    $response = self::addProfile($user, $update['text']);
                    if ($response['status']) {
                        Telegram::sendMessage([
                            'chat_id' => $update['from']['id'],
                            'text'    => "🤖 `OK! {$response['message']}`",
                        ]);
                    }
                    else {
                        Telegram::sendMessage([
                            'chat_id' => $update['from']['id'],
                            'text'    => "🤖 `ERROR! {$response['message']}`",
                        ]);
                    }

                } else {
                    Telegram::sendMessage([
                        'chat_id' => $update['from']['id'],
                        'text'    => "🤖 `I can't find your account. You have to subscribe on " . env('APP_URL') . "`",
                    ]);
                }
            }
        }

        return 'ok';
    }
}
