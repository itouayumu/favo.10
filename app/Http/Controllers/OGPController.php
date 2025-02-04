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
            $ogTitle = $this->getMetaTag($html, 'og:title') ?? $this->getTitle($html);
            $ogDescription = $this->getMetaTag($html, 'og:description') ?? $this->getMetaTag($html, 'description');
            $ogImage = $this->getMetaTag($html, 'og:image');

            // 画像URLをフルパスに変換（絶対パスではない場合）
            $ogImage = $this->resolveImageUrl($ogImage, $url);

            return response()->json([
                'title' => $ogTitle ?? 'タイトル不明',
                'description' => $ogDescription ?? '説明がありません',
                'image' => $ogImage ?? null,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'OGP情報の取得に失敗しました'], 500);
        }
    }

    /**
     * 指定されたメタタグの値を取得
     */
    private function getMetaTag($html, $property)
    {
        preg_match('/<meta (?:property|name)="'.preg_quote($property, '/').'" content="([^"]+)"/i', $html, $matches);
        return $matches[1] ?? null;
    }

    /**
     * <title>タグの値を取得
     */
    private function getTitle($html)
    {
        preg_match('/<title>(.*?)<\/title>/i', $html, $matches);
        return $matches[1] ?? null;
    }

    /**
     * 画像URLをフルパスに変換（相対パスを補正）
     */
    private function resolveImageUrl($imageUrl, $pageUrl)
    {
        if (!$imageUrl) {
            return null;
        }

        // すでに完全なURLならそのまま返す
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }

        // ベースURLを取得
        $parsedUrl = parse_url($pageUrl);
        $baseUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];

        // 画像URLが '/' で始まる場合はドメインを補完
        if (strpos($imageUrl, '/') === 0) {
            return $baseUrl . $imageUrl;
        }

        // それ以外の場合は元のページURLに結合
        return rtrim($baseUrl, '/') . '/' . ltrim($imageUrl, '/');
    }
}
