<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite; // モデルを使用
use App\Models\ToFavorite;
use Illuminate\Support\Facades\Auth;

class OshiController extends Controller
{
    public function recommend()
    {
        $user = Auth::user();

        // ログインユーザーのお気に入り一覧を取得
        $favoriteIds = ToFavorite::where('user_id', $user->id)
            ->where('favorite_flag', 1)
            ->pluck('favorite_id');

        // 未登録のお気に入りをランダムに取得
        $recommended = Favorite::whereNotIn('id', $favoriteIds)
            ->inRandomOrder()
            ->first();

        return view('recommends.recommend', compact('recommended', 'user'));
    }

    public function addFavorite($oshiId)
    {
        $user = Auth::user();

        // 推しを取得
        $oshi = Favorite::findOrFail($oshiId);

        // 中間テーブルで重複登録を防止
        $existingFavorite = ToFavorite::where('user_id', $user->id)
            ->where('favorite_id', $oshiId)
            ->where('favorite_flag', 1)
            ->first();

        if ($existingFavorite) {
            return redirect()->route('recommend')->with('message', 'この推しはすでにお気に入りに登録されています!');
        }

        // 中間テーブルに登録
        ToFavorite::create([
            'user_id' => $user->id,
            'favorite_id' => $oshiId,
            'favorite_flag' => 1,
        ]);

        // お気に入りカウントを増やす
        $oshi->increment('favorite_count');

        return redirect()->route('recommend')->with('message', '推しをお気に入りに登録しました!');
    }

    public function nextRecommended()
    {
        $user = Auth::user();

        // 次のおすすめを取得
        $favoriteIds = ToFavorite::where('user_id', $user->id)
            ->where('favorite_flag', 1)
            ->pluck('favorite_id');

        $recommended = Favorite::whereNotIn('id', $favoriteIds)
            ->inRandomOrder()
            ->first();

        return view('recommends.recommend', compact('recommended', 'user'));
    }
}
