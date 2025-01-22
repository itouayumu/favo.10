<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reply;

class ReplyController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'comment' => 'required|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $imagePath = $request->file('image') 
            ? $request->file('image')->store('reply_images', 'public') 
            : null;
    
        $reply = Reply::create([
            'post_id' => $request->post_id,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
            'image' => $imagePath,
            'delete_flag' => false,
        ]);
    
        $reply->load('user');
    
        return response()->json(['reply' => $reply], 200);

    }
    

    public function fetch($post_id)
    {
        try {
            $replies = Reply::where('post_id', $post_id)
                ->where('delete_flag', false)
                ->with('user:id,name')
                ->orderBy('created_at', 'asc')
                ->get();
    
            if ($replies->isEmpty()) {
                return response()->json([
                    'message' => '返信はまだありません。'
                ]);
            }
    
            return response()->json($replies);
    
        } catch (\Exception $e) {
            return response()->json(['error' => '返信データの取得に失敗しました。']);
        }
    }
    
}
