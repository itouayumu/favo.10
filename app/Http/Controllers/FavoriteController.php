<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\ToFavorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FavoriteController extends Controller
{
    // 新規登録フォームの表示
    public function create(Request $request)
    {
        $genres = Genre::where('delete_flag', false)->get(); // ジャンル一覧を取得

        // セッションに保存されたメッセージを取得（成功・失敗時の表示）
        $success = $request->session()->get('success');
        $error = $request->session()->get('error');

        return view('recommends.create', compact('genres', 'success', 'error'));
    }

    // 新規登録処理
    public function store(Request $request)
    {
        // 重複チェック: ユーザーが既に同じ名前の推しを登録していないか確認
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('name', $request->name)
            ->first();

        if ($existingFavorite) {
            return redirect()->route('recommends.create')->with('error', '同じ名前の推しは既に登録されています！');
        }

        // データ登録
        $favorite = new Favorite();
        $favorite->user_id = Auth::id();
        $favorite->name = $request->name;
        $favorite->genre_id = $request->genre_id;
        $favorite->introduction = $request->introduction;

        // 画像の保存
        if ($request->hasFile('image_1')) {
            $path = $request->file('image_1')->store('images', 'public');
            $favorite->image_1 = $path;
        }
        
        if ($request->hasFile('image_2')) {
            $path = $request->file('image_2')->store('images', 'public');
            $favorite->image_2 = $path;
        }
        if ($request->hasFile('image_3')) {
            $path = $request->file('image_3')->store('images', 'public');
            $favorite->image_3 = $path;
        }
        
        if ($request->hasFile('image_4')) {
            $path = $request->file('image_4')->store('images', 'public');
            $favorite->image_4 = $path;
        }
        $favorite->save();


        return redirect()->route('recommends.create')->with('success', '推しを登録しました！');
    }


    public function index()
    {
        // ログインユーザーIDを取得
        $userId = Auth::id();

        // デフォルトは五十音順（名前の昇順）で取得
        $favorites = Favorite::orderBy('name', 'asc')->get();

        // ログインユーザーがフォローしている推しを取得
        $followedFavorites = ToFavorite::where('user_id', $userId)
                                       ->where('delete_flag', 0) // フォロー解除されていない
                                       ->pluck('favorite_id')
                                       ->toArray();

        // ビューにデータを渡す
        return view('recommends.search', compact('favorites', 'followedFavorites'));
    }

    public function search(Request $request)
    {
        // クエリパラメータから検索条件を取得
        $query = $request->input('query', '');
    
        // 現在ログインしているユーザーID
        $userId = Auth::id();
    
        // 名前で検索し、ログインユーザーのフォロー情報も取得
        $favorites = Favorite::where('name', 'like', "%{$query}%")
                             ->orderBy('name', 'asc')
                             ->get();
    
        // ログインユーザーのフォロー済みの推しIDを取得
        $followedFavorites = ToFavorite::where('user_id', $userId)
                                       ->where('delete_flag', 0)
                                       ->pluck('favorite_id')
                                       ->toArray();
    
        // フォロー状態を含めてレスポンスを返す
        $favoritesWithFollowStatus = $favorites->map(function ($favorite) use ($followedFavorites) {
            $favorite->is_followed = in_array($favorite->id, $followedFavorites);
            return $favorite;
        });
    
        return response()->json([
            'data' => $favoritesWithFollowStatus,
            'message' => $favoritesWithFollowStatus->isEmpty() ? '推しが見つかりませんでした。' : '',
        ]);
    }
    

    public function toggleFollow($oshiId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'ログインしてください。'], 401);
        }

        DB::beginTransaction();
        try {
            $oshi = Favorite::findOrFail($oshiId);

            $existingFavorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $oshiId)
                ->first();

            if ($existingFavorite) {
                // すでにフォローしている場合は解除
                if ($existingFavorite->favorite_flag == 1) {
                    $existingFavorite->update(['favorite_flag' => 0]);
                    $oshi->decrement('favorite_count');
                    DB::commit();
                    return response()->json(['status' => 'unfollowed', 'message' => 'フォローを解除しました。'], 200);
                } else {
                    $existingFavorite->update(['favorite_flag' => 1]);
                    $oshi->increment('favorite_count');
                    DB::commit();
                    return response()->json(['status' => 'followed', 'message' => 'フォローしました。'], 200);
                }
            } else {
                // 初めてフォローする場合
                ToFavorite::create([
                    'user_id' => $user->id,
                    'favorite_id' => $oshiId,
                    'favorite_flag' => 1,
                ]);
                $oshi->increment('favorite_count');
                DB::commit();
                return response()->json(['status' => 'followed', 'message' => 'フォローしました。'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('エラー発生: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'エラーが発生しました。'], 500);
        }
    }
}


