<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * 新規登録フォームを表示
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * ユーザー登録処理
     */
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

        // バリデーション失敗時のリダイレクト
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 画像保存処理
        $imagePath = null; // デフォルトはnull
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('images', 'public'); // images/に保存
            //Log::info('画像パスが保存されました: ' . $imagePath); // ログにパスを記録
        } else {
            //Log::info('画像はアップロードされませんでした'); // アップロードがない場合
        }

        // ユーザー作成
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'introduction' => $request->introduction,
            'image' => $imagePath, // 画像パスを保存
            'point' => 0, // 初期ポイント
        ]);

        // 作成されたユーザーをログに記録（デバッグ用）
        //Log::info('ユーザーが作成されました: ', $user->toArray());

        // ログイン後のリダイレクト
        //auth()->login($user);

        return redirect('/')->with('success', 'アカウントが作成されました');
    }
}
