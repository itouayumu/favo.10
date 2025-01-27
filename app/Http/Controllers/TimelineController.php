<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Reply;
use App\Models\Favorite;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Schedule;
use App\Models\ToSchedule; 
class TimelineController extends Controller
{
// コントローラ内
public function index()
{
    $user = auth()->user(); // 現在ログインしているユーザーを取得

    // 投稿データを取得
    $posts = Post::with(['user', 'schedule', 'replies']) // 必要なリレーションをロード
        ->where('delete_flag', false) // 論理削除されていない投稿のみ
        ->orderBy('created_at', 'desc')
        ->get();

    // 現在のユーザーが登録しているスケジュールIDを取得
    $registeredSchedules = ToSchedule::where('user_id', $user->id)
        ->where('delete_flag', false) // 論理削除されていないもの
        ->pluck('schedule_id') // スケジュールIDのコレクション
        ->toArray();

    // 投稿データに登録状態フラグを追加
    foreach ($posts as $post) {
        if ($post->schedule) {
            // 自分が作成した予定かどうか
            $post->schedule->is_own_schedule = $post->schedule->user_id === $user->id;

            // 他人が作成した予定で、登録済みかどうかを判定
            $post->schedule->is_registered = in_array($post->schedule->id, $registeredSchedules);
        }
    }

    return view('timeline', compact('posts'));
}






    public function store(Request $request)
    {
        Log::info('リクエストデータ:', $request->all());
    
        // バリデーションルールを修正して schedule_id を追加
        $validatedData = $request->validate([
            'post' => 'required|max:255',
            'favorite_id' => 'required|integer', // favorite_id は必須
            'schedule_id' => 'nullable|integer|exists:schedules,id', // schedule_id は任意
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [

        ]);
    
        Log::info('バリデーション後のデータ:', $validatedData);
    
        // 画像のアップロード処理
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images', 'public');
        }
    
        try {
            // 投稿を保存
            $post = Post::create([
                'user_id' => auth()->id(),
                'favorite_id' => $validatedData['favorite_id'],
                'schedule_id' => $validatedData['schedule_id'] ?? null, // schedule_id があれば設定
                'post' => $validatedData['post'],
                'image' => $imagePath,
                'delete_flag' => false,
            ]);
    
            // 必要な関連データをロード
            $post->load('user', 'favorite', 'schedule'); // schedule 関連をロード
    
            return response()->json([
                'message' => '投稿が保存されました。',
                'post' => $post,
            ]);
        } catch (\Exception $e) {
            Log::error('投稿保存エラー: ' . $e->getMessage());
            return response()->json([
                'message' => '投稿の保存に失敗しました。',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function fetchTimeline(Request $request)
    {
        $lastFetched = $request->input('last_fetched');
        $userId = auth()->id();
    
        // 投稿データのクエリを作成
        $query = Post::with([
            'user:id,name,icon_url',  // 投稿者の必要な情報のみ
            'replies.user:id,name,icon_url',
            'schedule:id,title,image,favorite_id',
            'schedule.favorite:id,name,image_1'
        ])
        ->where('delete_flag', false);
    
        // 最後に取得した時間が指定されていれば、その後の投稿を取得
        if ($lastFetched) {
            $lastFetchedTime = Carbon::parse($lastFetched);
            $query->where('created_at', '>', $lastFetchedTime);
        }
    
        // 投稿データを取得
        $posts = $query->orderBy('created_at', 'desc')->get();
    
        // ユーザーのスケジュールデータを取得
        $schedules = Schedule::with('favorite:id,name,image_1') // 必要なデータのみ取得
            ->where('user_id', $userId)
            ->where('start_date', '>=', Carbon::today())
            ->orderBy('start_date', 'asc')
            ->get();
    
        // 投稿データにスケジュール情報を追加
        $postsWithSchedules = $posts->map(function ($post) {
            if ($post->schedule && $post->schedule->favorite) {
                $post->schedule_info = [
                    'favorite_icon' => $post->schedule->favorite->image_1 ?? null,
                    'favorite_name' => $post->schedule->favorite->name ?? null,
                    'title' => $post->schedule->title,
                    'image' => $post->schedule->image ? asset('storage/' . $post->schedule->image) : null,
                ];
            }
            return $post;
        });
    
        // タイムラインデータとスケジュールデータを返却
        return response()->json([
            'posts' => $postsWithSchedules,
            'schedules' => $schedules,
        ]);
    }
    
    

    // 投稿検索
    public function search(Request $request)
    {
        $query = $request->input('query');

        $posts = Post::with('user', 'replies.user')
                     ->where('post', 'LIKE', '%' . $query . '%')
                     ->where('delete_flag', false)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json($posts);
    }

    // 返信保存
    public function storeReply(Request $request)
    {
        $validatedData = $request->validate([
            'post_id' => 'required|exists:posts,id', // 必須で存在する投稿ID
            'comment' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('replies', 'public');
        }

        try {
            $reply = Reply::create([
                'user_id' => auth()->id(),
                'post_id' => $validatedData['post_id'],
                'comment' => $validatedData['comment'],
                'image' => $imagePath,
                'delete_flag' => false,
            ]);

            return response()->json([
                'message' => '返信が保存されました',
                'reply' => $reply,
            ]);
        } catch (\Exception $e) {
            Log::error('返信保存エラー: ' . $e->getMessage());
            return response()->json(['message' => '返信の保存に失敗しました。'], 500);
        }
    }

    // 特定投稿の返信取得
    public function fetchReplies($postId)
    {
        $replies = Reply::with('user')
                        ->where('post_id', $postId)
                        ->where('delete_flag', false)
                        ->orderBy('created_at', 'asc')
                        ->get();

        return response()->json($replies);
    }
}
