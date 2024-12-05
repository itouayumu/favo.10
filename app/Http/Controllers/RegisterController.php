<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // バリデーション
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'introduction' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:2048', // 画像は任意
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 画像保存処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        // ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'introduction' => $request->introduction,
            'image' => $imagePath,
            'point' => 0, // 初期ポイント
        ]);

        // ログイン後のリダイレクト
        auth()->login($user);

        return redirect()->route('home')->with('success', 'アカウントが作成されました');
    }
}

