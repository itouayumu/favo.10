@extends('layouts.app')

@section('content')
<!-- スクロール可能なコンテナを適用 -->
<div class="container scrollable-container">
    <h1>プロフィール編集</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <h3>使われているタグ</h3>
        <ul id="public-tags">
            @foreach ($user->tags()->wherePivot('hidden_flag', 0)->wherePivot('delete_flag', 0)->get() as $tag)
                <li id="tag-{{ $tag->id }}">
                    <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                    (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                    <button class="btn btn-secondary" onclick="toggleVisibility({{ $tag->id }}, 1)">非公開にする</button>
                    <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
                </li>
            @endforeach
        </ul>

        <div class="form-group">
            <label for="introduction">自己紹介</label>
            <textarea name="introduction" id="introduction" class="form-control">{{ old('introduction', $user->introduction) }}</textarea>
            @error('introduction')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">プロフィール画像</label><br>
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="プロフィール画像" width="150"><br>
            @endif
            <input type="file" name="image" id="image" class="form-control-file">
            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">更新</button>
    </form>

    <h3>新しいタグを作成</h3>
    <form action="{{ route('tags.create') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="tag-name">タグ名</label>
            <input type="text" id="tag-name" name="tag_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="visibility">公開設定</label>
            <select id="visibility" name="visibility" class="form-control">
                <option value="public">公開</option>
                <option value="private">非公開</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">タグを作成</button>
    </form>

    <hr>

    <h3>使われてないタグ</h3>
    <ul id="private-tags">
        @foreach ($user->tags()->wherePivot('hidden_flag', 1)->wherePivot('delete_flag', 0)->get() as $tag)
            <li id="tag-{{ $tag->id }}">
                <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                <button class="btn btn-warning" onclick="toggleVisibility({{ $tag->id }}, 0)">公開にする</button>
                <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
            </li>
        @endforeach
    </ul>

    <hr>

    <h3>お気に入りの推し</h3>
<ul>
    @foreach ($favorites as $favorite)
        <li>
            <h4>{{ $favorite->favorite->name }}</h4>
            <p>{{ $favorite->favorite->introduction }}</p>
            <img src="{{ asset('storage/' . $favorite->favorite->image_1) }}" alt="{{ $favorite->favorite->name }}" width="100">

            <!-- Follow/unfollow button -->
            <form action="{{ route('favorite.remove', $favorite->favorite_id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-danger">フォローを解除</button>
            </form>

            <!-- Visibility toggle (公開/非公開) -->
            @if ($favorite->favorite->hidden_flag == 0)
                <form action="{{ route('oshi.toggleVisibility', $favorite->favorite_id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">非公開にする</button>
                </form>
            @else
                <form action="{{ route('oshi.toggleVisibility', $favorite->favorite_id) }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">公開する</button>
                </form>
            @endif
        </li>
    @endforeach
</ul>

</div>

<!-- CSSファイルのリンク -->
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">

<!-- JavaScriptファイルの読み込み -->
<script src="{{ asset('js/profile.js') }}"></script>
@endsection
