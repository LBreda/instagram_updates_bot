<?php

namespace App\Http\Controllers;

use App\Models\InstagramProfiles;
use App\Traits\InstagramProfileHelper;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;

class AccountController extends Controller
{
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = \Auth::user();
        return view('account.index', compact('user'));
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function download()
    {
        $user = \Auth::user()->only(['first_name', 'last_name', 'username', 'telegram_id']);
        $followed_profiles = \Auth::user()->followedProfiles->map(function (InstagramProfiles $profile) {
            return $profile->only('name', 'full_name', 'instagram_id', 'profile_pic');
        });

        $out = collect(compact('user', 'followed_profiles'));

        $filename = 'IGUD_' . \Auth::user()->telegram_id . '_' . date('Ymd_His') . '.json';

        return response()->json($out, 200, ['Content-Disposition' => "attachment; filename={$filename}"]);
    }

    public function destroy()
    {
        $user = \Auth::user();
        $user->followedProfiles->each(function (InstagramProfiles $profile) use ($user) {
            $profile->followers()->detach($user);
            if($profile->followers->count() == 0) {
                $profile->delete();
            }
        });
        try {
            \Auth::logout();
            $user->forceDelete();
        }
        catch (\Exception $e) {
            return view('auth.deleted', ['success' => 'false']);
        }

        return view('auth.deleted', ['success' => 'true']);
    }
}
