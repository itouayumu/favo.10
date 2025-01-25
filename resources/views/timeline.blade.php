@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/timeline.css') }}">
@endsection

@section('content')
<div class="container mt-5">
    <h1 class="heading">タイムライン</h1>

    <!-- 投稿検索 -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="投稿を検索...">
        <div id="searchResults" class="mt-3"></div>
    </div>

    <form id="postForm" action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label for="post" class="form-label">投稿内容</label>
        <textarea id="post" name="post" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
        <label for="favorite-search" class="form-label">推しの名前を検索</label>
        <input type="text" id="favorite-search" class="form-control" placeholder="推しの名前を入力">
        <ul id="favorite-list" class="list-group mt-2" style="display: none;"></ul>
        <input type="hidden" id="favorite_id" name="favorite_id">
    </div>
    <div class="mb-3">
        <label for="image" class="form-label">画像 (任意)</label>
        <input type="file" id="image" name="image" class="form-control">
    </div>
    <input type="hidden" id="schedule_id" name="schedule_id"> <!-- hidden input -->
    <div class="mb-3">
        <button type="button" id="show-schedules" class="btn btn-outline-secondary">予定を投稿する</button>
    </div>

    <button type="submit" class="btn btn-primary">投稿する</button>
</form>

<!-- 予定リストモーダル -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleModalLabel">登録済みの予定</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="schedule-list">
                    <!-- Ajaxで取得した予定がここに表示されます -->
                </div>
            </div>
        </div>
    </div>
</div>

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
<div id="timeline" class="mt-4">
    @foreach ($posts as $post)
        <div class="post border rounded p-3 mb-4" id="post-{{ $post->id }}">
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('profile.showUser', ['id' => $post->user->id]) }}">
                    <img src="{{ $post->user->icon_url }}" alt="{{ $post->user->name }}のアイコン" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                </a>
                <strong>{{ $post->user->name }}</strong>
            </div>
            <p>{{ $post->post }}</p>
            <small class="text-muted">{{ $post->created_at }}</small>

            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="投稿画像" class="img-fluid mt-2">
            @endif

            <!-- ボタン -->
            <div class="mt-3">
                <button type="button" class="btn btn-sm btn-outline-primary reply-toggle" data-post-id="{{ $post->id }}">
                    返信する
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary reply-show" data-post-id="{{ $post->id }}">
                    返信を見る
                </button>
            </div>

            <!-- 返信フォーム -->
            <form id="reply-form-{{ $post->id }}" class="d-none mt-3">
                <textarea class="form-control reply-comment" rows="2" placeholder="返信を入力"></textarea>
                <input type="file" class="form-control mt-2 reply-image" accept="image/*">
                <button type="button" class="btn btn-secondary btn-sm mt-2 send-reply" data-post-id="{{ $post->id }}">返信する</button>
                <div class="reply-error text-danger mt-2" style="display: none;"></div>
            </form>

            <!-- 返信リスト -->
            <div id="reply-list-{{ $post->id }}" class="reply-list mt-3 d-none">
                @foreach ($post->replies as $reply)
                    <div class="reply p-2 border rounded mb-2">
                        <div class="d-flex align-items-center">
                        <a href="{{ route('profile.showUser', ['id' => $reply->user->id]) }}">
                            <img src="{{ $reply->user ? $reply->user->icon_url : asset('default-icon.png') }}" 
                                 alt="{{ $reply->user ? $reply->user->name : '匿名ユーザー' }}" 
                                 class="rounded-circle me-2" 
                                 style="width: 30px; height: 30px;">
                        </a>
                            <strong>{{ $reply->user ? $reply->user->name : '匿名ユーザー' }}</strong>
                            <small class="text-muted ms-2">{{ $reply->created_at->format('Y-m-d H:i') }}</small>
                        </div>
                        <p class="mt-2">{{ $reply->comment }}</p>
                        @if ($reply->image)
                            <img src="{{ asset('storage/' . $reply->image) }}" alt="返信画像" class="img-fluid mt-2">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

</div>
@endsection

@section('scripts')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('js/timeline.js') }}"></script>
<script src="{{ asset('js/serch_favorite.js') }}"></script>
@endsection
