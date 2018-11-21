<?php

namespace App\TelegramCommands;

use App\Models\User;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle()
    { dump($this->getUpdate()->message->from);
        $sender = $this->getUpdate()->message->from;
        $user = User::withTrashed()->where('telegram_id', '=', $sender->id)->first();

        if ($user) {
            $user->update([
                'first_name' => $sender->first_name,
                'last_name'  => $sender->last_name,
                'username'   => $sender->username,
                'deleted_at' => null,
            ]);
            $this->replyWithMessage(['text' => '🤖 Hello! Welcome back! You can manage your account on ' . env('APP_URL')]);
        }
        else {
            $user = new User([
                'first_name' => $sender->first_name,
                'last_name'  => $sender->last_name,
                'username'   => $sender->username,
                'deleted_at' => null,
            ]);
            $user->save();
            $this->replyWithMessage(['text' => '🤖 Hello! Welcome! You can manage your account on ' . env('APP_URL')]);
        }
    }
}
