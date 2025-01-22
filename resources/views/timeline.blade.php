@extends('layouts.app')

@section('content')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>タイムライン</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <div class="container mt-5">
        <h1>タイムライン</h1>

        <!-- 検索機能 -->
        <div class="mb-4">
            <input type="text" id="searchInput" class="form-control" placeholder="投稿を検索...">
        </div>
        <div id="searchResults"></div>

        <!-- 投稿フォーム -->
        <form id="postForm" action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="post" class="form-label">投稿内容</label>
                <textarea id="post" name="post" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label for="favorite-search" class="form-label">推しの名前を検索</label>
                <input type="text" id="favorite-search" placeholder="推しの名前を入力" class="form-control" autocomplete="off">
                <ul id="favorite-list" class="list-group mt-2" style="display: none;"></ul>
                <input type="hidden" id="favorite_id" name="favorite_id" value="">
                <span id="favorite-error" class="text-danger" style="display: none;"></span>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">画像 (任意)</label>
                <input type="file" id="image" name="image" accept="image/*" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">投稿する</button>
        </form>

        <!-- エラーメッセージ -->
        @if ($errors->any())
            <div class="alert alert-danger mt-3">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- タイムライン表示 -->
        <div id="timeline">
            @foreach ($posts as $post)
                <div class="post mb-4 p-3 border rounded" id="post-{{ $post->id }}">
                    <div class="d-flex align-items-center mb-2">
                        <a href="{{ route('user.profile', ['id' => $post->user->id]) }}">
                            <img src="{{ $post->user->icon_url }}" alt="{{ $post->user->name }}のアイコン" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                        </a>
                        <strong><a href="{{ route('user.profile', ['id' => $post->user->id]) }}">{{ $post->user->name }}</a></strong>
                    </div>
                    <p>{{ $post->post }}</p>
                    <p class="text-muted"><small>{{ $post->created_at }}</small></p>
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="投稿画像" class="img-fluid mb-2">
                    @endif

                    <!-- 返信フォーム -->
                    <div class="reply-form d-none" id="reply-form-{{ $post->id }}">
                    <form action="{{ route('replies.store', ['postId' => $post->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}">
                        <textarea name="comment">aa</textarea>
                        <input type="file" name="image">
                        <button type="submit">送信</button>
                    </form>

                    </div>

                    <!-- 返信リスト -->
                    <div class="reply-list d-none" id="reply-list-{{ $post->id }}">
                        @foreach ($post->replies as $reply)
                            <div class="reply p-2 border rounded mb-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $reply->user ? $reply->user->icon_url : asset('default-icon.png') }}" alt="{{ $reply->user->name }}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                                    <strong>{{ $reply->user ? $reply->user->name : '匿名ユーザー' }}</strong>
                                    <small class="text-muted ms-2">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                                <p class="mt-2">{{ $reply->comment }}</p>
                                @if ($reply->image)
                                    <img src="{{ asset('storage/' . $reply->image) }}" alt="返信画像" class="img-fluid">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- 返信ボタン -->
                    <div class="post-footer">
                        <button class="btn btn-link btn-sm reply-toggle" data-post-id="{{ $post->id }}">返信する</button>
                        <button class="btn btn-link btn-sm reply-show" data-post-id="{{ $post->id }}">返信を表示</button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/timeline.js') }}"></script>
<script src="{{ asset('js/serch_favorite.js') }}"></script>
@endsection
