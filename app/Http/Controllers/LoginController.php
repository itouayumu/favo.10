<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // ログイン画面を表示
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // ログイン処理
    public function login(Request $request)
    {
        // 入力値をバリデーション
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 認証を試行
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate(); // セッション固定攻撃を防止

            return redirect()->intended('home')->with('success', 'ログインしました');
        }

        // 認証失敗時
        return back()->withErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ])->withInput($request->only('email'));
    }

    // ログアウト処理
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'ログアウトしました');
    }
}

