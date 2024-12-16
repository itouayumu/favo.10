<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class TimelineController extends Controller
{
    // タイムライン表示
    public function index()
    {
        $posts = Post::where('delete_flag', false)
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
            'user_id' => auth()->id(), // 認証ユーザーのID
            'post' => $request->post,
            'image' => $imagePath,
            'delete_flag' => false,
        ]);
        

        return response()->json([
            'message' => '投稿が保存されました',
            'post' => $post,
        ]);
    }
    public function fetch()
{
    $posts = Post::where('delete_flag', false)
                 ->orderBy('created_at', 'desc')
                 ->get();

    return response()->json($posts);
}
  // タイムラインデータ取得 (非同期対応)
  public function fetchTimeline(Request $request)
  {
      $lastFetched = $request->input('last_fetched'); // 最後に取得した投稿の時刻
  
      $query = Post::where('delete_flag', false);
    
      if ($lastFetched) {
          $query->where('created_at', '>', $lastFetched); // 新しい投稿のみ取得
      }
    
      $posts = $query->orderBy('created_at', 'desc')->get();
    
      return response()->json($posts);
  }
  

}
