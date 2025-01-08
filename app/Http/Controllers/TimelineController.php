<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Carbon\Carbon;

class TimelineController extends Controller
{
    // タイムライン表示
    public function index()
    {
        $posts = Post::with('user') // ユーザー情報をロード
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

        $query = Post::where('delete_flag', false);

        if ($lastFetched) {
            $lastFetchedTime = Carbon::parse($lastFetched); // 入力をCarbonインスタンスに変換
            $query->where('created_at', '>', $lastFetchedTime);
        }

        $posts = $query->orderBy('created_at', 'desc')->with('user')->get();

        return response()->json($posts);
    }

    // 投稿検索
    public function search(Request $request)
    {
        $query = $request->input('query');
        $posts = Post::where('post', 'LIKE', '%' . $query . '%')
                     ->where('delete_flag', false)
                     ->orderBy('created_at', 'desc')
                     ->get();

        return response()->json($posts);
    }
}
