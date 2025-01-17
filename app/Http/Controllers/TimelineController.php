<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Reply;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class TimelineController extends Controller
{
    // タイムライン表示
    public function index()
    {
        $posts = Post::with(['user', 'replies.user']) // replies.userをロード
                     ->where('delete_flag', false)
                     ->orderBy('created_at', 'desc')
                     ->get();
    
        return view('timeline', ['posts' => $posts]);
    }

    // 投稿保存 (非同期対応)
    public function store(Request $request)
    {
        $request->validate([
            'post' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'favorite_id' => $request->input('oshiname'),
            'post' => $request->post,
            'image' => $imagePath,
            'delete_flag' => false,
        ]);

        return response()->json([
            'message' => '投稿が保存されました',
            'post' => $post,
        ]);
    }

    // タイムラインデータ取得 (非同期対応)
    public function fetchTimeline(Request $request)
    {
        $lastFetched = $request->input('last_fetched'); // 最後に取得した投稿の時刻

        $query = Post::with('user', 'replies.user') // ユーザー情報と返信情報をロード
                     ->where('delete_flag', false);

        if ($lastFetched) {
            $lastFetchedTime = Carbon::parse($lastFetched); // 入力をCarbonインスタンスに変換
            $query->where('created_at', '>', $lastFetchedTime);
        }

        $posts = $query->orderBy('created_at', 'desc')->get();

        return response()->json($posts);
    }

    // 投稿検索
    public function search(Request $request)
    {
        $query = $request->input('query');
        $posts = Post::with('user', 'replies.user') // ユーザー情報と返信情報をロード
                     ->where('post', 'LIKE', '%' . $query . '%')
                     ->where('delete_flag', false)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json($posts);
    }

    // 返信保存
    public function storeReply(Request $request)
    {
        $request->validate([
            'post_id' => 'required|exists:post,id',
            'comment' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('replies', 'public');
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

    // 特定投稿の返信取得
  // 特定投稿の返信取得
  public function fetchReplies($postId)
  {
      $replies = Reply::with('user') // ユーザー情報を一緒に取得
                      ->where('post_id', $postId)
                      ->where('delete_flag', false)
                      ->orderBy('created_at', 'asc')
                      ->get();
  
      // 各返信のuser_idを表示（デバッグ用）
      foreach ($replies as $reply) {
          Log::error('Reply ID: ' . $reply->id . ' - User ID: ' . $reply->user_id); // ログに出力
      }
  
      // 返信のリストを返す
      return response()->json($replies);
  }
  

    
    
}
