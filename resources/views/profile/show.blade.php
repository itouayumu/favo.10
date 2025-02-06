@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="card">
    <!-- Display the "Edit" button only if the logged-in user is the profile user -->
    @if (auth()->user()->id === $user->id)
        <div class="btn">
            <button class="e_btn" onclick="location.href='{{ route('profile.edit') }}'">編集</button>
        </div>
    @endif

    <!-- Profile Header -->
    <div class="card-header">
        <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://via.placeholder.com/150' }}"
             alt="Profile Image" class="icon" width="150">
        <div class="username">
            <h3>{{ $user->name }}</h3>
        </div>
    </div>

    <!-- Tags Section -->
    <div class="tag">
        @php
            $tags = $user->tags()->wherePivot('hidden_flag', 0)->wherePivot('delete_flag', 0)->get();
        @endphp

        @if ($tags->isNotEmpty())
            <ul>
                @foreach ($tags as $tag)
                <li id="tag-{{ $tag->id }}">
                    <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}" data-user-id="{{ $user->id }}">
                        {{ $tag->name }}
                    </a>
                    <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count ?? 0 }}</span>
                </li>
                @endforeach
            </ul>
        @else
            <p>タグはありません。</p>
        @endif
    </div>
    <h3 class="heading">新しいタグを作成</h3>
        <form action="{{ route('tags.create') }}" method="POST" class="tag-form">
            @csrf
            <div class="form-group">
            <input type="hidden" name="user_id" value="{{ $user->id }}">
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
            <div class="create">
                <button type="submit" class="c_btn">タグを作成</button>
            </div>
        </form>

    <!-- Introduction Section -->
    <div class="card-body">
        <div class="introduction">
            <div class="intro_content">
                <p>{{ $user->introduction ?? '詳細情報はありません。' }}</p>
            </div>
        </div>

        <hr>

        <!-- Favorite Oshi Section -->
        <h4 class="heading">お気に入りの推し</h4>
        <div class="favorites">
            @if ($user->favorites()->wherePivot('hidden_flag', 0)->exists())
                @foreach ($user->favorites()->wherePivot('hidden_flag', 0)->get() as $favorite)
                    <div class="favorite-item">
                        <div class="img-name">
                            <!-- 推しの詳細ページへのリンク -->
                            <a href="{{ route('oshi.show', $favorite->id) }}">
                                <img
                                    src="{{ $favorite->image_1 && Storage::exists('public/' . $favorite->image_1) 
                                    ? Storage::url($favorite->image_1) 
                                    : asset('img/default.png') }}" 
                                    alt="{{ $favorite->name }}" class="favorite-img">
                            </a>
                            <img src="{{ asset('img/red.png') }}" alt="赤いテープ" class="redimg">
                        </div>

                        <!-- Visibility toggle button -->
                        @if ($favorite->pivot->hidden_flag == 1)
                            <form action="{{ route('oshi.toggleVisibility', $favorite->pivot->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-success">公開する</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            @else
                <p>公開されたお気に入りの推しはありません。</p>
            @endif
        </div>

        <hr>

        <!-- Display the "Logout" button only if the logged-in user is the profile user -->
        @if (auth()->user()->id === $user->id)
            <div class="d-flex justify-content-between mt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">ログアウト</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/user_tags_click.js') }}"></script>
    <script src="{{ asset('js/favorite_tags_click.js') }}"></script>
@endsection
