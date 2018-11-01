<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class BroadcastMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igud:broadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a broadcast message to all the users';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::get();
        $message = $this->ask('Message text:');

        $bar = $this->output->createProgressBar($users->count());
        $users->each(function (User $user) use ($message, $bar) {
            Telegram::sendMessage([
                'chat_id' => $user->telegram_id,
                'text' => '🤖 ' . $message,
            ]);
            $bar->advance();
        });
        $bar->finish();
    }
}
