@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit_profile.css') }}">
@endsection

@section('content')
    <h1 class="heading">プロフィール編集</h1>

    <!-- @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif -->

    <!-- ここに関連付けられたタグの表示を追加 -->
    @if(session('tags'))
        <h3>関連付けられたタグ:</h3>
        <ul>
            @foreach(session('tags') as $tag)
                <li>{{ $tag->name }}</li>
            @endforeach
        </ul>
    @endif

    <div class="form-container">
        <img src="{{ asset('img/osipin.png') }}" alt="押しピン" class="osipin">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="e_icon">
                @if($user->image)
                    <label for="image"><img src="{{ asset('storage/' . $user->image) }}" alt="プロフィール画像" class="icon" id="imagePreview"></label>
                @endif
                <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
                <label for="image" class="camera"></label>
                @error('image')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <p><label for="name">名前</label></p>
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
                        (<span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                        <button class="btn btn-secondary" onclick="toggleVisibility({{ $tag->id }}, 1)">非公開にする</button>
                        <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
                    </li>
                @endforeach
            </ul>

            <div class="form-group">
                <label for="introduction">自己紹介</label>
                <textarea name="introduction" id="introduction" class="form-control" maxlength="200" oninput="updateCharacterCount()">{{ old('introduction', $user->introduction) }}</textarea>
                <p><span id="currentChars">0</span> / 200</p>
                @error('introduction')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary mt-3">更新</button>
        </form>

        <h3>新しいタグを作成</h3>
        <form action="{{ route('tags.create') }}" method="POST">
            @csrf
            <input type="hidden" name="user_id" value="{{ $user->id }}">
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
        @if (isset($favorites) && $favorites->count() > 0)
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

                        <!-- 推しタグの表示 -->
                        <h4>推しタグ</h4>
                        <ul>
                            @foreach ($favorite->favorite->tags as $tag)
                                @if ($tag->pivot->delete_flag == 0) <!-- ここでdelete_flagを確認 -->
                                    <li>{{ $tag->name }}

                                        <!-- Visibility toggle (公開/非公開) -->
                                        @if ($tag->pivot->hidden_flag == 0) <!-- 0 means public -->
                                            <form action="{{ route('oshi.toggleTagVisibility', [$favorite->favorite_id, $tag->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning">非公開にする</button>
                                            </form>
                                        @else <!-- 1 means private -->
                                            <form action="{{ route('oshi.toggleTagVisibility', [$favorite->favorite_id, $tag->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-success">公開する</button>
                                            </form>
                                        @endif

                                        <!-- 削除ボタン -->
                                        <form action="{{ route('oshi.deleteTag', [$favorite->favorite_id, $tag->id]) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger">削除</button>
                                        </form>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        <!-- 推しタグ作成フォーム -->
                        <form action="{{ route('oshi.createTag', $favorite->favorite_id) }}" method="POST" style="margin-top: 10px;">
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
                    </li>
                @endforeach
            </ul>
        @else
            <p>お気に入りの推しがありません。</p>
        @endif
    </div>
@endsection

@section('scripts')
    <!-- JavaScriptファイルの読み込み -->
    <script src="{{ asset('js/profile.js') }}"></script>
@endsection
