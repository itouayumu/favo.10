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

    // タグを取得または作成
    $tag = Tag::firstOrCreate(
        ['name' => $request->tag_name],
        ['create_user' => auth()->id()]
    );

    // すでに同じタグが関連付けられているか確認
    $favoriteTag = \App\Models\FavoriteTag::where('favorite_id', $favoriteId)
                                          ->where('tags_id', $tag->id)
                                          ->first();

    if ($favoriteTag) {
        if ($favoriteTag->delete_flag === 0) {
            // 既に存在し、削除フラグが立っていない場合
            return redirect()->back()->with('info', 'このタグはすでに関連付けられています。');
        } else {
            // 削除フラグが立っている場合は再利用
            $favoriteTag->delete_flag = 0;
            $favoriteTag->hidden_flag = $request->visibility == 'private' ? 1 : 0;
            $favoriteTag->save();

            return redirect()->back()->with('success', '削除されたタグが再度関連付けられました。');
        }
    }

    // 推しタグを関連付け
    \App\Models\FavoriteTag::create([
        'favorite_id' => $favoriteId,
        'tags_id' => $tag->id,
        'sort_id' => 0,
        'count' => 0,
        'hidden_flag' => $request->visibility == 'private' ? 1 : 0,
        'delete_flag' => 0,
    ]);

    return redirect()->back()->with('success', '推しタグが作成されました。');
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
        // Fetch the pivot record with strict conditions
        $favoriteTag = \App\Models\FavoriteTag::where('favorite_id', $favoriteId)
                                              ->where('tags_id', $tagId)
                                              ->where('delete_flag', 0)
                                              ->first();
    
        if ($favoriteTag) {
            // Update the delete_flag
            $favoriteTag->delete_flag = 1;
            $favoriteTag->save();
            
            return redirect()->back()->with('success', 'タグが削除されました。');
        }
    
        return redirect()->back()->with('error', 'タグが見つかりませんでした。');
    }
    



public function toggleTagVisibility(Request $request, $favoriteId, $tagId)
{
    // 推しを取得
    $favorite = Favorite::findOrFail($favoriteId);

    // タグを取得（推しに関連付けられているタグを絞り込む）
    $tag = $favorite->tags()->where('tags.id', $tagId)->first();

    // タグが関連付けられていない場合はエラー
    if (!$tag) {
        return redirect()->back()->with('error', 'タグが見つかりませんでした。');
    }

    // タグのvisibilityをトグル（公開・非公開）
    $currentVisibility = $tag->pivot->hidden_flag;
    $newVisibility = $currentVisibility == 0 ? 1 : 0;

    // 更新
    $tag->pivot->hidden_flag = $newVisibility;
    $tag->pivot->save();

    // 新しいvisibilityに基づいたメッセージを設定
    $message = $newVisibility == 0 ? 'タグが公開されました。' : 'タグが非公開になりました。';

    // リダイレクト
    return redirect()->back()->with('success', $message);
}

}
