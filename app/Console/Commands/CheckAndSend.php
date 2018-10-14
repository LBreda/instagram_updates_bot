<?php

namespace App\Console\Commands;

use App\Models\InstagramProfiles;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use GuzzleHttp\Client as Guzzle;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckAndSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igud:check-send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for new Instagram posts and send them to the users';

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
        $client = new Guzzle();

        InstagramProfiles::get()->each(function (InstagramProfiles $instagram_profile) use ($client) {

            // Gets the profile page
            $url = sprintf('https://www.instagram.com/%s/', $instagram_profile->name);
            $request_time = Carbon::now();
            $response = $client->request('GET', $url);

            // Does magic parsing (sigh)
            preg_match('/<script type="text\/javascript">window\._sharedData = (.*?)<\/script>/',
                (string)$response->getBody(), $response);
            $response = json_decode(substr($response[1], 0, -1));

            // Grabs the media list (slurp)
            $media = $response->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges;

            // Sends new media to interested users
            foreach ($media as $medium) {
                if (Carbon::createFromTimestamp($medium->node->taken_at_timestamp)->gt($instagram_profile->last_check)) {
                    $instagram_profile->followers->each(function (User $user) use ($medium) {
                        Telegram::sendMessage([
                            'chat_id' => $user->telegram_id,
                            'text'    => 'https://instagram.com/p/' . $medium->node->shortcode,
                        ]);
                    });
                }
            }

            // Updates last check
            $instagram_profile->update(['last_check' => $request_time->format('Y-m-d H:i:s')]);
        });
    }
}
