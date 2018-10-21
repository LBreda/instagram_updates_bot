<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SetWebHook extends Command
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
        $url = env('APP_URL') . '/api/' . env('TELEGRAM_KEY') . '/webhook';

        $response = Telegram::setWebhook(['url' => $url]);

        if($response) {
            $this->info("Webhook set to {$url}");
        } else {
            $this->error("Error setting webhook to {$url}");
        }
    }
}
