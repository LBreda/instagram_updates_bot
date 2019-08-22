<?php

namespace App\Console\Commands;

use App\Models\InstagramProfiles;
use App\Models\Locks;
use App\Models\Todos;
use App\Models\User;
use App\Traits\InstagramProfileHelper;
use Carbon\Carbon;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Console\Command;
use GuzzleHttp\Client as Guzzle;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckAndSend extends Command
{
    use InstagramProfileHelper;

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
        // Avoids double run
        if (self::is_locked()) {
            return;
        }
        self::lock();

        $client = new Guzzle();

        // Manages scheduled profile adds
        Todos::where('type_id', '=', \Config::get('const.todo_types.add_profile'))->each(function (Todos $todo) {
            $data = json_decode($todo->data);
            $response = $this->addProfile(User::find($data->user_id), $data->url);
            $todo->delete();

            $message = implode(', ', $response['messages']);
            $recipient = User::find($data->user_id);
            if ($response['status']) {
                Telegram::sendMessage([
                    'chat_id'    => $recipient->telegram_id,
                    'text'       => "🤖 `OK! {$message}.`",
                    'parse_mode' => 'Markdown',
                ]);
            } else {
                Telegram::sendMessage([
                    'chat_id'    => $recipient->telegram_id,
                    'text'       => "🤖 `ERROR! {$message}.`",
                    'parse_mode' => 'Markdown',
                ]);
            }
        });

        // Gets new posts
        InstagramProfiles::get()->shuffle()->each(function (InstagramProfiles $instagram_profile) use ($client) {
            // Gets the profile page
            $request_time = Carbon::now();
            try {
                $url = sprintf('https://www.instagram.com/%s/', $instagram_profile->name);
                $response = $client->request('GET', $url, [
                    'headers' => [
                        'User-Agent' => 'IGUD/' . \Config::get('app.version'),
                        'Accept'     => '*/*',
                    ],
                ]);
                if ($this->option('verbose')) {
                    $this->info("Retrieved media for {$instagram_profile->instagram_id} ({$instagram_profile->name})");
                }
            } catch (ClientException $e) {
                $response = null;
                if ($e->getCode() === 404) {
                    $instagram_profile->followers->each(function (User $user) use ($instagram_profile) {
                        try {
                            $message = [
                                'chat_id' => $user->telegram_id,
                                'text'    => "🤖 Removed the {$instagram_profile->name}'s profile. It was probably deleted from Instagram or renamed.",
                            ];
                            Telegram::sendMessage($message);
                        } catch (TelegramResponseException $e) {
                            $this->error($e->getMessage() . " - SendMessage to id {$message['chat_id']} - Message: '{$message['text']}'");
                        }
                    });
                    $instagram_profile->delete();
                    if ($this->option('verbose')) {
                        $this->error("Deleted {$instagram_profile->instagram_id} ({$instagram_profile->name}): not found");
                    }
                } elseif ($e->getCode() === 429) {
                    sleep(65);
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
                try {
                    $ig_user_data = $response->entry_data->ProfilePage[0]->graphql->user;
                } catch (\ErrorException $e) {
                    self::unlock();
                    if ($this->option('verbose')) {
                        $this->error("Not a Instagram Page JSON (1)");
                    }
                    return;
                }
                $instagram_profile->full_name = $ig_user_data->full_name;
                $instagram_profile->profile_pic = $ig_user_data->profile_pic_url;
                $instagram_profile->is_private = $ig_user_data->is_private;
                $instagram_profile->save();
                if ($this->option('verbose')) {
                    $this->info("Updated info for {$instagram_profile->instagram_id} ({$instagram_profile->name})");
                }

                if ($ig_user_data->is_private == false) {
                    // Grabs the media list (slurp)
                    try {
                        $media = $response->entry_data->ProfilePage[0]->graphql->user->edge_owner_to_timeline_media->edges;
                    } catch (\ErrorException $e) {
                        self::unlock();
                        if ($this->option('verbose')) {
                            $this->error("Not a Instagram Page JSON (2)");
                        }
                        return;
                    }

                    // Sends new media to interested users
                    foreach ($media as $medium) {
                        if (Carbon::createFromTimestamp($medium->node->taken_at_timestamp)->gt($instagram_profile->last_check)) {
                            $instagram_profile->followers->each(function (User $user) use (
                                $medium,
                                $instagram_profile
                            ) {
                                try {
                                    $message = [
                                        'chat_id' => $user->telegram_id,
                                        'text'    => 'https://instagram.com/p/' . $medium->node->shortcode,
                                    ];
                                    Telegram::sendMessage($message);
                                    if ($this->option('verbose')) {
                                        $this->info("Sent new media for {$instagram_profile->instagram_id} ({$instagram_profile->name}) to {$user->telegram_id}");
                                    }
                                } catch (TelegramResponseException $e) {
                                    $this->error($e->getMessage() . " - SendMessage to id {$message['chat_id']} - Message: '{$message['text']}'");
                                }
                            });
                        }
                    }
                }

                // Updates last check
                $instagram_profile->update(['last_check' => $request_time->format('Y-m-d H:i:s')]);
            }
        });

        self::unlock();
    }

    /**
     * @return bool
     */
    private static function is_locked()
    {
        $lock_name = 'App\Console\Commands\CheckAndSend';
        $lock = Locks::where('name', '=', $lock_name)->first();

        if ($lock) {
            return $lock->status;
        } else {
            return false;
        }
    }

    /**
     *
     */
    private static function unlock()
    {
        $lock_name = 'App\Console\Commands\CheckAndSend';
        $lock = Locks::where('name', '=', $lock_name)->first();

        if ($lock) {
            $lock->status = false;
            $lock->save();
        } else {
            $lock = new Locks([
                'name'   => $lock_name,
                'status' => false,
            ]);
            $lock->save();
        }
    }

    /**
     *
     */
    private static function lock()
    {
        $lock_name = 'App\Console\Commands\CheckAndSend';
        $lock = Locks::where('name', '=', $lock_name)->first();

        if ($lock) {
            $lock->status = true;
            $lock->save();
        } else {
            $lock = new Locks([
                'name'   => $lock_name,
                'status' => true,
            ]);
            $lock->save();
        }
    }
}
