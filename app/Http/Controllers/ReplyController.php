<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reply;
use Illuminate\Support\Facades\Log;

class ReplyController extends Controller
{
    // 返信一覧を取得
    public function fetch($post_id)
    {
        try {
            $replies = Reply::where('post_id', $post_id)
                ->where('delete_flag', false)
                ->with('user:id,name,image') // 必要なユーザー情報をロード
                ->orderBy('created_at', 'asc')
                ->get();

            if ($replies->isEmpty()) {
                return response()->json([
                    'message' => '返信はまだありません。',
                ]);
            }

            return response()->json($replies);
        } catch (\Exception $e) {
            Log::error('返信データ取得エラー: ' . $e->getMessage());
            return response()->json(['error' => '返信データの取得に失敗しました。'], 500);
        }
    }

    // 返信を保存
    public function store(Request $request)
    {
        // バリデーション
        try {
            $validatedData = $request->validate([
                'post_id' => 'required', // 存在する投稿IDであることを確認
                'comment' => 'required|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    
        Log::debug('リクエストデータ: ', $request->all());
    
        // 画像保存
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('replies', 'public');
        }
    
        // 返信作成
        try {
            $reply = Reply::create([
                'user_id' => auth()->id(),
                'post_id' => $validatedData['post_id'],
                'comment' => $validatedData['comment'],
                'image' => $imagePath,
                'delete_flag' => false,
            ]);

            // ユーザー情報をロード
            $reply->load('user');

            return response()->json([
                'message' => '返信が保存されました。',
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('返信保存エラー: ' . $e->getMessage());
            return response()->json([
                'message' => '返信の保存に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
