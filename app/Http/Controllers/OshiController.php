<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite; // モデルを使用
use App\Models\ToFavorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        DB::beginTransaction();

        try {
            // 推しを取得
            $oshi = Favorite::findOrFail($oshiId);

            // すでにお気に入りに登録されているか確認
            $existingFavorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $oshiId)
                ->first();

            if ($existingFavorite) {
                // もし `favorite_flag` が 0 であれば、そのレコードを更新してカウントを増やす
                if ($existingFavorite->favorite_flag == 0) {
                    $existingFavorite->update([
                        'favorite_flag' => 1,  // 再度お気に入りとしてフラグを立てる
                    ]);

                    // お気に入りカウントを増やす
                    $oshi->increment('favorite_count');

                    DB::commit();
                    return redirect()->route('recommend')->with('message', '推しを再びお気に入りに登録しました!');
                }

                // すでにお気に入りに登録されている場合
                return redirect()->route('recommend')->with('message', 'この推しはすでにお気に入りに登録されています!');
            } else {
                // 新規にレコードを作成
                ToFavorite::create([
                    'user_id' => $user->id,
                    'favorite_id' => $oshiId,
                    'favorite_flag' => 1,  // お気に入りとしてフラグを立てる
                ]);

                // お気に入りカウントを増やす
                $oshi->increment('favorite_count');
            }

            DB::commit();
            return redirect()->route('recommend')->with('message', '推しをお気に入りに登録しました!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('エラー発生: ' . $e->getMessage());
            return redirect()->route('recommend')->with('error', 'エラーが発生しました。');
        }
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
        DB::beginTransaction();

        try {
            // お気に入りの推しを取得
            $toFavorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $id)
                ->where('favorite_flag', 1) // お気に入りとして登録されているものを取得
                ->first();

            if ($toFavorite) {
                // 推しのfavorite_countを減らす
                if ($toFavorite->favorite) {
                    $toFavorite->favorite->decrement('favorite_count');
                }

                // `favorite_flag` を 0 にして非公開にする
                $toFavorite->update(['favorite_flag' => 0]);
            }

            DB::commit();
            return redirect()->route('profile.edit')->with('message', '推しのフォローを解除しました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('エラー発生: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'エラーが発生しました。');
        }
    }

    public function toggleVisibility($favoriteId)
    {
        $user = Auth::user();
        DB::beginTransaction();
    
        try {
            // 'ToFavorite'リレーションシップを取得
            $favorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $favoriteId)
                ->first();
    
            if ($favorite) {
                // 公開/非公開の切り替え (ToFavoriteのhidden_flag)
                $newHiddenFlag = $favorite->hidden_flag == 0 ? 1 : 0;
                $favorite->update(['hidden_flag' => $newHiddenFlag]);
    
                DB::commit();
                return redirect()->route('profile.edit')->with('message', '公開設定を変更しました!');
            } else {
                DB::rollback();
                return redirect()->route('profile.edit')->with('error', '推しはお気に入りに登録されていません。');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('エラー発生: ' . $e->getMessage());
            return redirect()->route('profile.edit')->with('error', 'エラーが発生しました。');
        }
    }
    
    public function show($id)
    {
        // favoriteテーブルからデータを取得
        $favorite = Favorite::with('tags')->findOrFail($id);

        // 詳細ページにデータを渡す
        return view('Oshi.detail', compact('favorite'));
    }

    // 編集ページを表示
    public function edit($id)
    {
        $favorite = Favorite::findOrFail($id); // 指定されたIDのデータを取得
        return view('Oshi.edit', compact('favorite'));
    }

    // 更新処理
    public function update(Request $request, $id)
    {
        $favorite = Favorite::findOrFail($id);

        // 入力データのバリデーション
        $request->validate([
            'name' => 'required|string|max:255',
            'introduction' => 'nullable|string',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // フィールドを更新
        $favorite->name = $request->name;
        $favorite->introduction = $request->introduction;

        // 画像アップロード処理
        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $path = $request->file($imageField)->store('public/favorites');
                $favorite->$imageField = basename($path); // パスを保存
            }
        }

        $favorite->save(); // 更新を保存

        return redirect()->route('oshi.show', $favorite->id)->with('success', '推しの情報が更新されました。');
    }
}
