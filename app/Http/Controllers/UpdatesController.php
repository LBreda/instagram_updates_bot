<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class UpdatesController extends Controller
{

    /**
     * Webhook handler.
     *
     * @return String
     */
    public function updates(): string
    {
        $updates = Telegram::commandsHandler(true);
        return 'ok';
    }
}
