<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use App\Models\Genre;

class SearchController extends Controller
{
    public function index()
    {
        // 初期表示で全ての推しを取得
        $favorites = Favorite::with('genre')->get();
        return view('search', compact('favorites'));
    }

    public function searchAjax(Request $request)
    {
        // 検索条件を設定
        $query = Favorite::with('genre');
        
        // 入力があればあいまい検索、なければ全件取得
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        // データ取得（削除フラグがfalseのもの）
        $favorites = $query->where('delete_flag', false)->get();

        // JSONで結果を返す
        return response()->json($favorites);
    }
}
