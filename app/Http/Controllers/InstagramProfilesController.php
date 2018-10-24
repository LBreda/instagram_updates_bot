<?php

namespace App\Http\Controllers;

use App\Models\InstagramProfiles;
use App\Models\User;
use Carbon\Carbon;
use Egulias\EmailValidator\Exception\ExpectingCTEXT;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Client as Guzzle;
use \GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class InstagramProfilesController extends Controller
{
    private static $rules = [
        'profile' => [
            'required',
            'url',
            'regex:/https?:\/\/(www\.)?instagram\.com\/([A-Za-z0-9_](?:(?:[A-Za-z0-9_]|(?:\.(?!\.))){0,28}(?:[A-Za-z0-9_]))?)/',
        ],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->get('list_only') == 1) {
            return view('instagram_profiles.list', compact('request'));
        } else {
            return view('instagram_profiles.index', compact('request'));
        }
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), static::$rules);

        if ($validator->fails()) {
            $response = [
                'status'   => false,
                'messages' => $validator->errors()->get('profile'),
            ];
            return response()->json($response, 400);
        }

        $profile = $request->input('profile');

        // Trying to retrieve profile information
        $client = new Guzzle();
        try {
            $response = $client->request('GET', $profile);
        } catch (ClientException $e) {
            $res = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($res, $e->getCode());
        } catch (RequestException $e) {
            $res = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($res, $e->getCode());
        } catch (GuzzleException $e) {
            $res = [
                'status'  => false,
                'message' => $e->getMessage(),
            ];
            return response()->json($res, $e->getCode());
        }

        if ($response and $response->getStatusCode() == 200) {
            // Does magic parsing (sigh)
            preg_match('/<script type="text\/javascript">window\._sharedData = (.*?)<\/script>/',
                (string)$response->getBody(), $response);
            $profile_data = json_decode(substr($response[1], 0, -1));

            $graphql = $profile_data->entry_data->ProfilePage[0]->graphql ?? null;

            if(!$graphql) {
                $res = [
                    'status'   => false,
                    'messages' => ["The URL is not an Instagram profile page"],
                ];
                return response()->json($res, 400);
            }

            // Check for the user in the database, updates it if present or creates it if not present
            $db_profile = InstagramProfiles::where('instagram_id', '=', $graphql->user->id)
                ->withTrashed()->first();
            if ($db_profile) {
                $db_profile->update([
                    'name'         => $graphql->user->username,
                    'full_name'    => $graphql->user->full_name,
                    'instagram_id' => $graphql->user->id,
                    'profile_pic'  => $graphql->user->profile_pic_url,
                    'is_private'   => $graphql->user->is_private,
                    'deleted_at'   => null,
                ]);
                if($db_profile->followers->count() == 1) {
                    $db_profile->update(['last_check' => Carbon::now()]);
                }
            } else {
                $db_profile = new InstagramProfiles([
                    'name'         => $graphql->user->username,
                    'full_name'    => $graphql->user->full_name,
                    'instagram_id' => $graphql->user->id,
                    'profile_pic'  => $graphql->user->profile_pic_url,
                    'is_private'   => $graphql->user->is_private,
                    'last_check'   => Carbon::now(),
                ]);
                $db_profile->save();
            }

            // Adds the profile to the followed ones
            if (\Auth::user()->followedProfiles->contains($db_profile->id)) {
                $res = [
                    'status'   => true,
                    'messages' => ['You already added this profile'],
                ];
                return response()->json($res, 200);
            } else {
                \Auth::user()->followedProfiles()->attach($db_profile);
                $res = [
                    'status'   => true,
                    'messages' => ['Instagram profile added'],
                    'profile'  => $db_profile->toArray(),
                ];
                return response()->json($res, 201);
            }

        } else {
            $res = [
                'status'   => false,
                'messages' => ['Error retrieving profile'],
            ];
            return response()->json($res, $response->getStatusCode());
        }
    }

    public function destroy(InstagramProfiles $instagramProfile) {
        \Auth::user()->followedProfiles()->detach($instagramProfile);
        if($instagramProfile->followers->count() == 0) {
            try {
                $instagramProfile->delete();
            }
            catch (\Exception $e) {
                $res = [
                    'status' => false,
                    'messages' => [ $e->getMessage() ],
                ];
                return response()->json($res, 500);
            }
            $res = [
                'status' => true,
                'messages' => ['Instagram profile deleted'],
            ];
            return response()->json($res);
        }
    }
}
