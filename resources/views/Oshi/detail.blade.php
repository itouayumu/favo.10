@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="card">
            <div class="btn">
                <button class="e_btn" onclick="location.href='{{ route('oshi.edit', $favorite->id) }}'">編集</button>
            </div>
        <!-- 推し詳細 -->
        <div class="card-header">
            <img src="{{ $favorite->image_1 && Storage::exists('public/' . $favorite->image_1) 
                        ? Storage::url($favorite->image_1) 
                        : asset('img/default.png') }}" 
                 alt="{{ $favorite->name }}" class="icon">
            <h2 class="username">{{ $favorite->name }}</h2>
        </div>
        
        <div class="card-body">
            <!-- 推しの紹介 -->
            <div class="introduction">
                <p class="description">{{ $favorite->description ?? '詳細情報はありません。' }}</p>
            </div>
    <div class="tag">
            @php
                $tags = $favorite->tags()
                    ->wherePivot('hidden_flag', 0)
                    ->wherePivot('delete_flag', 0)
                    ->get();
            @endphp

            @if ($tags->isNotEmpty())
                @foreach ($tags as $tag)
                <ul>
                    <li>
                        <x-oshi-tag :tagId="$tag->id" :tagName="$tag->name" :tagCount="$tag->pivot->count" :favoriteId="$favorite->id" />
                    </li>
                </ul>
                @endforeach
            @else
                <p>タグはありません。</p>
            @endif
    </div>


<!-- 推しタグ作成フォーム -->
<form action="{{ route('oshi.createTag', ['favorite_id' => $favorite->id]) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="tag-name-{{ $favorite->favorite_id }}">タグ名</label><br>
                                <input type="text" id="tag-name-{{ $favorite->favorite_id }}" name="tag_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="visibility-{{ $favorite->favorite_id }}">公開設定</label>
                                <select id="visibility-{{ $favorite->favorite_id }}" name="visibility" class="form-control">
                                    <option value="public">公開</option>
                                    <option value="private">非公開</option>
                                </select>
                            </div>
                            <div class="create">
                                <button type="submit" class="c_btn">推しタグを作成</button>
                            </div>
                        </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- JavaScriptファイルの読み込み -->
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/favorite_tags_click.js') }}"></script>
@endsection
