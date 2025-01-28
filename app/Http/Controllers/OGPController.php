<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OGPController extends Controller
{
    public function fetchOGP(Request $request)
    {
        $url = $request->input('url');

        // URLのバリデーション
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => '無効なURLです'], 400);
        }

        try {
            $response = Http::get($url);
            $html = $response->body();

            // OGP情報を解析
            $ogTitle = $this->getMetaTag($html, 'og:title');
            $ogDescription = $this->getMetaTag($html, 'og:description');
            $ogImage = $this->getMetaTag($html, 'og:image');

            return response()->json([
                'title' => $ogTitle ?? 'タイトル不明',
                'description' => $ogDescription ?? '説明がありません',
                'image' => $ogImage ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'OGP情報の取得に失敗しました'], 500);
        }
    }

    private function getMetaTag($html, $property)
    {
        preg_match('/<meta property="' . preg_quote($property, '/') . '" content="([^"]+)"/', $html, $matches);
        return $matches[1] ?? null;
    }
}
