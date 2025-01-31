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

        // 未登録のお気に入りをランダムに取得 (ジャンル情報を含める)
        $recommended = Favorite::with('genre')
            ->whereNotIn('id', $favoriteIds)
            ->inRandomOrder()
            ->first();

        return view('recommends.recommend', compact('recommended', 'user'));
    }

    public function addFavorite($oshiId)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $oshi = Favorite::findOrFail($oshiId);

            $existingFavorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $oshiId)
                ->first();

            if ($existingFavorite) {
                if ($existingFavorite->favorite_flag == 0) {
                    $existingFavorite->update(['favorite_flag' => 1]);
                    $oshi->increment('favorite_count');
                    DB::commit();
                    return redirect()->route('recommend')->with('message', '推しを再びお気に入りに登録しました!');
                }
                return redirect()->route('recommend')->with('message', 'この推しはすでにお気に入りに登録されています!');
            } else {
                ToFavorite::create([
                    'user_id' => $user->id,
                    'favorite_id' => $oshiId,
                    'favorite_flag' => 1,
                ]);
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

        // 次のおすすめを取得 (ジャンル情報を含める)
        $favoriteIds = ToFavorite::where('user_id', $user->id)
            ->where('favorite_flag', 1)
            ->pluck('favorite_id');

        $recommended = Favorite::with('genre')
            ->whereNotIn('id', $favoriteIds)
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
            ->with('favorite')
            ->get();

        return view('profile.edit', compact('user', 'favorites'));
    }

    public function removeFavorite($id)
    {
        $user = Auth::user();
        DB::beginTransaction();

        try {
            $toFavorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $id)
                ->where('favorite_flag', 1)
                ->first();

            if ($toFavorite) {
                if ($toFavorite->favorite) {
                    $toFavorite->favorite->decrement('favorite_count');
                }
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
            $favorite = ToFavorite::where('user_id', $user->id)
                ->where('favorite_id', $favoriteId)
                ->first();

            if ($favorite) {
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
        $favorite = Favorite::with('tags', 'genre')->findOrFail($id);
        return view('Oshi.detail', compact('favorite'));
    }

    public function edit($id)
    {
        $favorite = Favorite::findOrFail($id);
        return view('Oshi.edit', compact('favorite'));
    }

    public function update(Request $request, $id)
    {
        $favorite = Favorite::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'introduction' => 'nullable|string',
            'image_1' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_2' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_3' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'image_4' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $favorite->name = $request->name;
        $favorite->introduction = $request->introduction;

        foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $imageField) {
            if ($request->hasFile($imageField)) {
                $path = $request->file($imageField)->store('public/favorites');
                $favorite->$imageField = basename($path);
            }
        }

        $favorite->save();

        return redirect()->route('oshi.show', $favorite->id)->with('success', '推しの情報が更新されました。');
    }
}
