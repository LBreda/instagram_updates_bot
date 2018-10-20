<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckAndSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igud:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the webhook for the bot';

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
        $response = Telegram::setWebhook([
            'url' => env('APP_URL') . '/api/' . env('TELEGRAM_KEY') . '/webhook',
        ]);

        if($response->ok) {
            $this->info($response->description);
        } else {
            $this->error($response->description);
        }
    }
}
