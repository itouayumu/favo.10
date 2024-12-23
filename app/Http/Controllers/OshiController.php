<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recommend;

class OshiController extends Controller
{
    /**
     * ランダムな推しを取得して表示する
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recommend()
    {
        // Recommendモデルからランダムに1件取得
        $recommended = Recommend::where('hidden_flag', false)->inRandomOrder()->first();

        // 推しが登録されていない場合の対応
        if (!$recommended) {
            return response()->json([
                'message' => 'まだ推しが登録されていません。',
            ], 404);
        }

        // 推しの情報を返却
        return response()->json([
            'message' => 'おすすめの推しはこちらです！',
            'oshi' => [
                'name' => $recommended->name,
                'introduction' => $recommended->introduction,
                'images' => [
                    $recommended->image_1,
                    $recommended->image_2,
                    $recommended->image_3,
                    $recommended->image_4,
                ],
                'favorite_count' => $recommended->favorite_count,
            ],
        ]);
    }
}
