<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\Request;

class PrivacyController extends Controller
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
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            $privacy_data = \Storage::disk('local')->get('privacy_policy.md');
        } catch (FileNotFoundException $e) {
            $privacy_data = "Create a `privacy_policy.md` file in /storage/app.";
        }
        return view('privacy.index', compact('privacy_data'));
    }
}
