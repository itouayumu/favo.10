@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://via.placeholder.com/150' }}"
                 alt="Profile Image" class="icon" width="150">
            <div class="prate">
                <div class="name">
                    <h3 class="username">{{ $user->name }}</h3>
                </div>
            </div>
        </div>

        <div class="tag">
            @php
                $tags = $user->tags()->wherePivot('hidden_flag', 0)->get();
            @endphp

            @if ($tags->isNotEmpty())
                @foreach ($tags as $tag)
                    <!-- コンポーネントにデータを渡す -->
                    <x-tag :tagId="$tag->id" :tagName="$tag->name" :tagCount="$tag->pivot->count" />
                @endforeach
            @else
                <p>タグはありません。</p>
            @endif
        </div>

        <div class="card-body">
            <div class="introduction">
                <div class="intro_content">
                    <p>{{ $user->introduction ?? '詳細情報はありません。' }}</p>
                </div>
            </div>
            
            <hr>
            <h4>公開されたお気に入りの推し</h4>
            <div class="favorites">
                @if ($user->favorites()->where('hidden_flag', 0)->exists())
                    @foreach ($user->favorites()->where('hidden_flag', 0)->get() as $favorite)
                        <div class="favorite-item">
                            <h5>{{ $favorite->name }}</h5>
                            <p>{{ $favorite->introduction }}</p>

                            <!-- 関連するタグを表示 -->
                            <div class="favorite-tags">
                                @php
                                    $favoriteTags = $favorite->tags()->wherePivot('hidden_flag', 0)->get();
                                @endphp

                                @if ($favoriteTags->isNotEmpty())
                                    @foreach ($favoriteTags as $favoriteTag)
                                        <span style="margin-right: 10px;">{{ $favoriteTag->name }}</span>
                                    @endforeach
                                @else
                                    <p>タグはありません。</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <p>公開されたお気に入りの推しはありません。</p>
                @endif
            </div>

            <hr>

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
@endsection
