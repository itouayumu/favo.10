<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reply;

class ReplyController extends Controller
{
    // 返信の保存
    public function store(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:posts,id', // post テーブルの id を確認
            'comment' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reply_images', 'public');
        }

        $reply = Reply::create([
            'user_id' => auth()->id(),
            'post_id' => $request->post_id,
            'comment' => $request->comment,
            'image' => $imagePath,
            'delete_flag' => false,
        ]);

        $reply->load('user:id,name,image'); // ユーザー情報をロード

        return response()->json([
            'message' => '返信が保存されました',
            'reply' => $reply,
        ]);
    }

    // 特定の投稿の返信を取得
    public function fetch($post_id)
    {
        $replies = Reply::with('user:id,name,image') // アイコン情報を取得
                        ->where('post_id', $post_id)
                        ->where('delete_flag', false)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json($replies);
    }

    // 新しい返信を取得
    public function fetchNewReplies(Request $request)
    {
        $lastFetched = $request->input('last_fetched');
        $replies = Reply::with('user:id,name,image') // アイコン情報を取得
                        ->where('created_at', '>', $lastFetched)
                        ->where('delete_flag', false)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json($replies);
    }

    public function fetchReplies(Request $request)
    {
        $lastFetched = $request->input('last_fetched');
        $replies = Reply::with('user:id,name,image') // アイコン情報を取得
                        ->where('created_at', '>', $lastFetched)
                        ->where('delete_flag', false)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json($replies);
    }
}
