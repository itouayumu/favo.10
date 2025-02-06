<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

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

        return view('profile.edit', compact('user', 'tags'));
    }

    // クリック数を増加
    public function incrementClickCount($tagId, $userId)
    {
        // ユーザーが存在するか確認
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'ユーザーが見つかりません。']);
        }
    
        // タグがそのユーザーに関連しているか確認
        $tag = $user->tags()->where('tags.id', $tagId)->first();
        if (!$tag) {
            return response()->json(['success' => false, 'message' => 'タグが見つかりません。']);
        }
    
        // クリック数をインクリメント
        $user->tags()->updateExistingPivot($tagId, [
            'count' => DB::raw('count + 1'),
        ]);
    
        // 最新のクリック数を取得
        $clickCount = $user->tags()->where('tags.id', $tagId)->first()->pivot->count;
    
        return response()->json(['success' => true, 'click_count' => $clickCount]);
    }
    
    public function create(Request $request)
    {
        // バリデーション
        $request->validate([
            'tag_name' => 'required|string|max:255',
            'visibility' => 'required|in:public,private',
            'user_id' => 'required|exists:users,id', // user_id の存在チェック
        ]);
    
        // ユーザーの取得
        $user = User::findOrFail($request->input('user_id'));
    
        // タグの取得または作成
        $tag = Tag::firstOrCreate(
            ['name' => $request->input('tag_name')], // 検索条件
            ['create_user' => $user->name]           // 作成時のみ適用
        );
    
        // 公開/非公開フラグ
        $visibility = $request->input('visibility') === 'public' ? 0 : 1;
    
        // ✅ 修正: `tags_id` を正しく指定
        if (!$user->tags()->wherePivot('tags_id', $tag->id)->exists()) {
            // ✅ ユーザーとタグを関連付け
            $user->tags()->attach($tag->id, [
                'hidden_flag' => $visibility, 
                'count' => 0, 
                'delete_flag' => 0,
            ]);
        }
    
        return redirect()->back()->with('success', 'タグが作成され、ユーザーに追加されました');
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
            $tag = $user->tags()->where('tags.id', $tagId)->first();

            if (!$tag) {
                return response()->json(['success' => false, 'message' => 'タグが見つかりません。']);
            }

            // 削除フラグを1に更新
            $user->tags()->updateExistingPivot($tagId, [
                'delete_flag' => 1,
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'タグの削除に失敗しました']);
        }
    }
}
