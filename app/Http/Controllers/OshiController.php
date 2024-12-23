<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Recommend; // 推しモデルをインポート
use Illuminate\Support\Facades\Auth;

class OshiController extends Controller
{
    public function recommend()
    {
        // ログインユーザーを取得
        $user = Auth::user();

        // おすすめの推しを取得
        $recommended = Recommend::where('hidden_flag', false)->inRandomOrder()->first();

        return view('recommends.recommend', compact('recommended', 'user'));
    }

    public function addFavorite($oshiId)
    {
        // ログインユーザーを取得
        $user = Auth::user();

        // 推しを取得（推しIDから）
        $oshi = Recommend::findOrFail($oshiId);

        // ユーザーのお気に入りに同じ名前の推しがすでに登録されているか確認
        $existingFavorite = Favorite::where('user_id', $user->id)
                                    ->where('name', $oshi->name) // 同じ名前の推しがすでに登録されていないか確認
                                    ->first();

        // もし既に登録されていたらメッセージを返す
        if ($existingFavorite) {
            return redirect()->route('recommend')->with('message', 'この推しはすでにお気に入りに登録されています!');
        }

        // お気に入りテーブルに新しい推しを登録
        Favorite::create([
            'user_id' => $user->id,
            'genre_id' => $oshi->genre_id,  // 推しのジャンルID
            'name' => $oshi->name,
            'introduction' => $oshi->introduction,
            'image_1' => $oshi->image_1,
            'image_2' => $oshi->image_2,
            'image_3' => $oshi->image_3,
            'image_4' => $oshi->image_4,
            'favorite_count' => 0,  // 初期値として0
        ]);

        // 推しのお気に入りカウントを更新
        $oshi->increment('favorite_count');  // お気に入り数を1増加

        return redirect()->route('recommend')->with('message', '推しをお気に入りに登録しました!');
    }

    public function nextRecommended()
    {
        // ログインユーザーを取得
        $user = Auth::user();

        // 次のおすすめをランダムに取得
        $recommended = Recommend::where('hidden_flag', false)->inRandomOrder()->first();

        return view('recommends.recommend', compact('recommended', 'user'));
    }
}
