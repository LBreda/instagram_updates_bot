<?php

namespace App\Http\Controllers;

use App\Models\InstagramProfiles;
use \App\Traits\InstagramProfileHelper;
use Illuminate\Http\Request;

class InstagramProfilesController extends Controller
{
    use InstagramProfileHelper;

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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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

        $result = $this::addProfile(\Auth::user(), $profile);

        return response()->json($result, $result['code']);
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
