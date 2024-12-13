<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    /**
     * Show the profile of the logged-in user.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // ログイン中のユーザーを取得
        $user = Auth::user();

        // ビューにユーザー情報を渡す
        return view('profile.show', compact('user'));
    }
}
