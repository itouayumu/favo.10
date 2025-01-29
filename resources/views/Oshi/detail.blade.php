@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="card">
        <!-- 推し詳細 -->
        <div class="card-header text-center">
            <img src="{{ $favorite->image_1 && Storage::exists('public/' . $favorite->image_1) 
                        ? Storage::url($favorite->image_1) 
                        : asset('img/default.png') }}" 
                 alt="{{ $favorite->name }}" class="detail-img">
            <h2>{{ $favorite->name }}</h2>
        </div>
        
        <div class="card-body">
            <!-- 推しの紹介 -->
            <div class="introduction mb-3">
                <p>{{ $favorite->description ?? '詳細情報はありません。' }}</p>
            </div>
<h4>推しタグ</h4>
<ul>
<div class="tag">
    @php
        $tags = $favorite->tags()
            ->wherePivot('hidden_flag', 0)
            ->wherePivot('delete_flag', 0)
            ->get();
    @endphp

    @if ($tags->isNotEmpty())
        @foreach ($tags as $tag)
        <x-oshi-tag :tagId="$tag->id" :tagName="$tag->name" :tagCount="$tag->pivot->count" :favoriteId="$favorite->id" />
        @endforeach
    @else
        <p>タグはありません。</p>
    @endif
</div>



<!-- 推しタグ作成フォーム -->
<form action="{{ route('oshi.createTag', ['favorite_id' => $favorite->id]) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="tag-name-{{ $favorite->favorite_id }}">タグ名</label>
                                <input type="text" id="tag-name-{{ $favorite->favorite_id }}" name="tag_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="visibility-{{ $favorite->favorite_id }}">公開設定</label>
                                <select id="visibility-{{ $favorite->favorite_id }}" name="visibility" class="form-control">
                                    <option value="public">公開</option>
                                    <option value="private">非公開</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">推しタグを作成</button>
                        </form>

            </ul>

            <!-- 編集ボタン -->
            <div class="text-center">
                <a href="{{ route('oshi.edit', $favorite->id) }}" class="btn btn-primary">編集</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- JavaScriptファイルの読み込み -->
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/favorite_tags_click.js') }}"></script>
@endsection
