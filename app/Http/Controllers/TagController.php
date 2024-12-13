<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tag;

class TagController extends Controller
{
    // ユーザーにタグを紐づける
    public function attachTag(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // タグIDリストを取得
        $tagIds = $request->input('tags'); // フォームから送信されたタグIDの配列
        $user->tags()->sync($tagIds); // タグを同期

        return redirect()->back()->with('success', 'タグを更新しました。');
    }

    // タグの一覧を表示
    public function index()
    {
        $tags = Tag::where('delete_flag', 0)->get();
        return view('tags.tag', compact('tags')); // ビュー名を変更
    }

    // 新しいタグを作成
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:tags',
            'create_user' => 'required|string',
        ]);

        Tag::create([
            'name' => $request->input('name'),
            'create_user' => $request->input('create_user'),
            'delete_flag' => 0,
        ]);

        return redirect()->back()->with('success', '新しいタグを作成しました。');
    }

    // プロフィール編集（タグ管理）
    public function profileEdit($userId)
    {
        $user = User::findOrFail($userId); // ユーザー情報を取得
        $tags = Tag::where('delete_flag', 0)->get(); // 有効なタグ一覧を取得

        return view('users.profile_edit', compact('user', 'tags')); // ビュー名を指定
    }
}
