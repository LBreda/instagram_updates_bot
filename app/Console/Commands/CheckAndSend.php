<?php

namespace App\Console\Commands;

use App\Models\InstagramProfiles;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
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
            $request_time = Carbon::now();
            try {
                $url = sprintf('https://www.instagram.com/%s/', $instagram_profile->name);
                $response = $client->request('GET', $url);
                if ($this->option('verbose')) {
                    $this->info("Retrieved media for {$instagram_profile->instagram_id} ({$instagram_profile->name})");
                }
            } catch (ClientException $e) {
                $response = null;
                if ($e->getCode() === 404) {
                    $instagram_profile->followers->each(function (User $user) use ($instagram_profile) {
                        Telegram::sendMessage([
                            'chat_id' => $user->telegram_id,
                            'text'    => "🤖 Removed the {$instagram_profile->name}'s profile. It was probably deleted from Instagram or renamed.",
                        ]);
                    });
                    $instagram_profile->delete();
                    if ($this->option('verbose')) {
                        $this->error("Deleted {$instagram_profile->instagram_id} ({$instagram_profile->name}): not found");
                    }
                } else {
                    $instagram_profile->last_error = json_encode([
                        'date'      => Carbon::now(),
                        'exception' => $e->getMessage(),
                    ]);
                    $instagram_profile->save();
                }
            } catch (ServerException $e) {
                $instagram_profile->last_error = json_encode([
                    'date'      => Carbon::now(),
                    'exception' => $e->getMessage(),
                ]);
                $instagram_profile->save();
                $response = null;
            } catch (RequestException $e) {
                $instagram_profile->last_error = json_encode([
                    'date'      => Carbon::now(),
                    'exception' => $e->getMessage(),
                ]);
                $instagram_profile->save();
                $response = null;
            }

            if ($response and $response->getStatusCode() === 200) {
                // Does magic parsing (sigh)
                preg_match('/<script type="text\/javascript">window\._sharedData = (.*?)<\/script>/',
                    (string)$response->getBody(), $response);
                $response = json_decode(substr($response[1], 0, -1));

                // Updates the profile data
                $ig_user_data = $response->entry_data->ProfilePage[0]->graphql->user;
                $instagram_profile->full_name = $ig_user_data->full_name;
                $instagram_profile->profile_pic = $ig_user_data->profile_pic_url;
                $instagram_profile->is_private = $ig_user_data->is_private;
                $instagram_profile->save();
                if ($this->option('verbose')) {
                    $this->info("Updated info for {$instagram_profile->instagram_id} ({$instagram_profile->name})");
                }

                if ($ig_user_data->is_private == false) {
                    // Grabs the media list (slurp)
                    $media = $response->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges;

                    // Sends new media to interested users
                    foreach ($media as $medium) {
                        if (Carbon::createFromTimestamp($medium->node->taken_at_timestamp)->gt($instagram_profile->last_check)) {
                            $instagram_profile->followers->each(function (User $user) use (
                                $medium,
                                $instagram_profile
                            ) {
                                Telegram::sendMessage([
                                    'chat_id' => $user->telegram_id,
                                    'text'    => 'https://instagram.com/p/' . $medium->node->shortcode,
                                ]);
                                if ($this->option('verbose')) {
                                    $this->info("Sent new media for {$instagram_profile->instagram_id} ({$instagram_profile->name}) to {$user->telegram_id}");
                                }
                            });
                        }
                    }
                }

                // Updates last check
                $instagram_profile->update(['last_check' => $request_time->format('Y-m-d H:i:s')]);
            }
        });
    }
}
