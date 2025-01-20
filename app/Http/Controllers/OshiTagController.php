<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Tag;
use Illuminate\Http\Request;

class OshiTagController extends Controller
{
    public function createTag(Request $request, $favoriteId)
    {
        $request->validate([
            'tag_name' => 'required|string|max:255',
            'visibility' => 'required|in:public,private',
        ]);

        // タグが既に存在するか確認、なければ作成
        $tag = Tag::firstOrCreate(
            ['name' => $request->tag_name],
            ['create_user' => auth()->id()]  // create_userに現在のユーザーIDを設定
        );

        // 推しを取得
        $favorite = Favorite::findOrFail($favoriteId);

        // すでにそのタグが推しに関連付けられているか確認
        if ($favorite->tags->contains($tag->id)) {
            return redirect()->back()->with('info', 'このタグはすでに関連付けられています。');
        }

        // 推しタグを関連付け
        $favorite->tags()->attach($tag->id, [
            'sort_id' => 0,
            'count' => 0,
            'hidden_flag' => $request->visibility == 'private' ? 1 : 0,
            'delete_flag' => 0,
        ]);

        // 関連付けたタグを取得
        $tags = $favorite->tags;

        // タグ名をビューに渡す
        return redirect()->back()->with('success', '推しタグが作成されました。')->with('tags', $tags);
    }

    // タグをクリックした際にカウントを増加させるメソッド
    public function incrementTagCount($favoriteId, $tagId)
    {
        // 推しを取得
        $favorite = Favorite::findOrFail($favoriteId);

        // タグを取得（推しに関連付けられているタグを絞り込む）
        $tag = $favorite->tags()->where('tags_id', $tagId)->first();

        // タグが関連付けられていない場合はエラー
        if (!$tag) {
            return response()->json(['error' => 'タグが見つかりません'], 404);
        }

        // カウントをインクリメント
        $tag->pivot->count++;
        $tag->pivot->save();

        // 新しいカウントを返す
        return response()->json([
            'success' => true,
            'newCount' => $tag->pivot->count
        ]);
    }

    public function deleteTag($favoriteId, $tagId)
{
    // favorite_tagテーブルの該当行を取得
    $favoriteTag = \App\Models\FavoriteTag::where('favorite_id', $favoriteId)
                                            ->where('tags_id', $tagId)
                                            ->first();

    if ($favoriteTag) {
        // delete_flagを1に設定して非表示にする
        $favoriteTag->delete_flag = 1;
        $favoriteTag->save();
        
        return redirect()->back()->with('success', 'タグが削除されました。');
    }

    return redirect()->back()->with('error', 'タグが見つかりませんでした。');
}


}
