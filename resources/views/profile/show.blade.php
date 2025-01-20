@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="card">
        <!-- Profile Header -->
        <div class="card-header">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://via.placeholder.com/150' }}"
                 alt="Profile Image" class="icon" width="150">
            <div class="prate">
                <div class="name">
                    <h3 class="username">{{ $user->name }}</h3>
                </div>
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
            <h4>公開されたお気に入りの推し</h4>
            <div class="favorites">
                @if ($user->favorites()->where('hidden_flag', 0)->exists())
                    @if ($user->toFavorites()->where('favorite_flag', 1)->exists())
                        @foreach ($user->favorites()->where('hidden_flag', 0)->get() as $favorite)
                            @foreach ($user->toFavorites()->where('favorite_flag', 1)->get() as $toFavorite)
                                <div class="favorite-item">
                                    <h5>{{ $favorite->name }}</h5>
                                    <p>{{ $favorite->introduction }}</p>

                                    <!-- 関連するタグを表示 -->
                                    <div class="favorite-tags">
                                        @php
                                            $favoriteTags = $favorite->tags()->wherePivot('hidden_flag', 0)->wherePivot('delete_flag', 0)->get();
                                        @endphp

                                        @if ($favoriteTags->isNotEmpty())
                                            @foreach ($favoriteTags as $favoriteTag)
                                                <a href="javascript:void(0);" 
                                                   class="tag-link" 
                                                   data-favorite-id="{{ $favorite->id }}" 
                                                   data-tag-id="{{ $favoriteTag->id }}"
                                                   style="margin-right: 10px;">
                                                    {{ $favoriteTag->name }} 
                                                    (<span class="tag-count">{{ $favoriteTag->pivot->count }}</span>)
                                                </a>
                                            @endforeach
                                        @else
                                            <p>タグはありません。</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <p>公開されたお気に入りの推しはありません。</p>
                    @endif
                @else
                    <p>公開されたお気に入りの推しはありません。</p>
                @endif
            </div>

            <hr>

            <!-- Edit and Logout Buttons -->
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-primary" onclick="location.href='{{ route('profile.edit') }}'">編集</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">戻る</a>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/user_tags_click.js') }}"></script>
    <script src="{{ asset('js/favorite_tags_click.js') }}"></script>
@endsection
