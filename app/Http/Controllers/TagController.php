<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tag;

class TagController extends Controller
{
    // 公開タグと非公開タグの表示
    public function publicTags()
    {
        $user = auth()->user();
        
        // 削除されていないすべてのタグを取得
        $tags = $user->tags()
                     ->wherePivot('delete_flag', 0) // 削除されていないタグ
                     ->get();

        return view('tags.public_tags', compact('user', 'tags'));
    }

    // クリック数を増加
    public function incrementClickCount($tagId)
    {
        $userId = auth()->id();
        $user = User::findOrFail($userId);

        // タグが存在するか確認
        $tag = $user->tags()->where('tags.id', $tagId)->first();
        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'タグが見つかりません。']);
        }

        // クリック数を1増加
        $user->tags()->updateExistingPivot($tagId, [
            'count' => \DB::raw('count + 1'),
        ]);

        // 最新のクリック数を取得
        $clickCount = $user->tags()->find($tagId)->pivot->count;

        return response()->json(['success' => true, 'click_count' => $clickCount]);
    }

    // タグの作成
    public function create(Request $request)
    {
        // バリデーション
        $request->validate([
            'tag_name' => 'required|string|max:255',
            'visibility' => 'required|in:public,private',
        ]);

        $user = auth()->user();

        // タグの作成
        $tag = Tag::create([
            'name' => $request->input('tag_name'),
            'create_user' => $user->name,
        ]);

        // 公開/非公開フラグを設定
        $visibility = $request->input('visibility') === 'public' ? 0 : 1;

        // ユーザーとタグの関連付け
        $user->tags()->attach($tag->id, [
            'hidden_flag' => $visibility, 
            'count' => 0, 
            'delete_flag' => 0,
        ]);

        return redirect()->route('tags.publicTags')->with('success', 'タグが作成されました');
    }

    // 公開/非公開の切り替え
    public function toggleVisibility(Request $request, $tagId)
{
    $user = auth()->user();
    $userTag = $user->tags()->where('tags.id', $tagId)->first();

    if (!$userTag) {
        return response()->json(['success' => false, 'message' => 'タグが見つかりません。']);
    }

    // hidden_flagを切り替える
    $newVisibility = $request->input('hidden_flag'); // 0: 公開, 1: 非公開

    $user->tags()->updateExistingPivot($tagId, [
        'hidden_flag' => $newVisibility,
    ]);

    return response()->json(['success' => true, 'newVisibility' => $newVisibility]);
}


    // タグの削除
    public function delete($tagId)
    {
        try {
            $user = auth()->user();
            $user->tags()->detach($tagId); // 関連付けを解除

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'タグの削除に失敗しました']);
        }
    }
}
