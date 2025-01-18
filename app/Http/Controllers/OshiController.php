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

    public function editProfile()
    {
        $user = Auth::user();

        // ログインユーザーのお気に入り一覧を取得
        $favorites = ToFavorite::where('user_id', $user->id)
            ->where('favorite_flag', 1)
            ->with('favorite') // モデルのリレーションをロード
            ->get();

        return view('profile.edit', compact('user', 'favorites'));
    }

    public function removeFavorite($id)
{
    $user = Auth::user();

    // お気に入りの推しを取得
    $toFavorite = ToFavorite::where('user_id', $user->id)
        ->where('favorite_id', $id)
        ->where('favorite_flag', 1) // お気に入りとして登録されているものを取得
        ->first();

    if ($toFavorite) {
        // お気に入りフラグを解除
        $toFavorite->update(['favorite_flag' => 0]);

        // 推しのカウントを減らす
        if ($toFavorite->favorite) {
            // 推しのfavorite_countを1減少させる
            $toFavorite->favorite->decrement('favorite_count');
        }
    }

    // フォロー解除後、再度同じ推しを追加した際に重複エラーを防ぐ
    // 推しのレコードがすでに存在しないか確認
    $existingFavorite = ToFavorite::where('user_id', $user->id)
        ->where('favorite_id', $id)
        ->where('favorite_flag', 1)
        ->first();

    if ($existingFavorite) {
        return redirect()->route('profile.edit')->with('message', 'この推しはすでにお気に入りに登録されています!');
    }

    return redirect()->route('profile.edit')->with('message', '推しのフォローを解除しました。');
}
}
