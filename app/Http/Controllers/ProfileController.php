<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
{
    $user = Auth::user();
    $tags = $user->tags;  // ユーザーに関連するタグを取得
    $favorites = $user->favorites;  // ユーザーのお気に入りを取得

    return view('profile.edit', compact('user', 'favorites', 'tags'));  // 'favorites' をビューに渡す
}


    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'introduction' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // プロフィール画像の処理
        if ($request->hasFile('image')) {
            if ($user->image) {
                Storage::delete('public/' . $user->image);
            }
            $path = $request->file('image')->store('profile_images', 'public');
            $user->image = $path;
        }

        // その他のデータを更新
        $user->name = $request->name;
        $user->introduction = $request->introduction;
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'プロフィールが更新されました。');
    }

     // ログイン中のユーザープロフィール表示
     public function show()
     {
         $user = auth()->user();
         return view('profile.show', compact('user'));
     }
 
     // 他ユーザーのプロフィール表示
     public function showUser($id)
     {
         $user = User::findOrFail($id); // 指定されたIDのユーザーを取得
         return view('profile.show', compact('user'));
     }
}
