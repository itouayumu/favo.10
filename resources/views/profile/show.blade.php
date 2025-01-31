@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
<div class="card">
    <div class="btn">
        <button class="e_btn" onclick="location.href='{{ route('profile.edit') }}'">編集</button>
    </div>
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
                @foreach ($tags as $tag)
                    <x-tag :tagId="$tag->id" :tagName="$tag->name" :tagCount="$tag->pivot->count" />
                @endforeach
            @else
                <p>タグはありません。</p>
            @endif
        </div>

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

            <!-- Edit and Logout Buttons -->
            <div class="d-flex justify-content-between mt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-btn">ログアウト</button>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/user_tags_click.js') }}"></script>
    <script src="{{ asset('js/favorite_tags_click.js') }}"></script>
@endsection
