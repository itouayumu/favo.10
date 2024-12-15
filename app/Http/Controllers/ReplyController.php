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
            'post_id' => 'required|exists:post,id', // post テーブルの id を確認
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

        return response()->json([
            'message' => '返信が保存されました',
            'reply' => $reply,
        ]);
    }

    // 返信の取得
    public function fetch($post_id)
    {
        $replies = Reply::where('post_id', $post_id)
                        ->where('delete_flag', false)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json($replies);
    }
}
