<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Genre;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // 新規登録フォームの表示
    public function create()
    {
        $genres = Genre::where('delete_flag', false)->get(); // ジャンル一覧を取得
        return view('create', compact('genres'));
    }

    // 新規登録処理
    public function store(Request $request)
    {

        // データ登録
        $favorite = new Favorite();
        $favorite->user_id = Auth::id();
        $favorite->name = $request->name;
        $favorite->genre_id = $request->genre_id;
        $favorite->introduction = $request->introduction;

        // 画像の保存
        if ($request->hasFile('image_1')) {
            $favorite->image_1 = $request->file('image_1')->store('images', 'public');
        }

        $favorite->save();

        return redirect()->route('favorites.index')->with('success', '推しを登録しました！');
    }
}
