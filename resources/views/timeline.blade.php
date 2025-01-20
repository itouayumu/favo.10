@extends('layouts.app')

@section('content')

    <h1 class="heading">タイムライン</h1>
    <input type="text" id="searchInput" class="form-control" placeholder="投稿を検索...">

<div id="searchResults"></div> <!-- 検索結果の表示領域 -->


        <!-- 投稿フォーム -->
        <form id="postForm" action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">

            @csrf


    <div class="container mt-5">
        <h1>タイムライン</h1>
        
        <!-- 検索機能 -->
        <div class="mb-4">
            <input type="text" id="searchInput" class="form-control" placeholder="投稿を検索...">
        </div>
        <div id="searchResults"></div>
   <!-- 推しの名前検索 -->
   <label for="favorite-search">推しの選択</label><br>
    <input type="text" id="favorite-search" placeholder="推しの名前を入力" autocomplete="off">
    <ul id="favorite-list" style="border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none;"></ul>

    <!-- 選択された推しのIDを格納する隠しフィールド -->
    <input type="hidden" id="oshiname" name="oshiname" value="">
        <!-- 投稿フォーム -->
        <form id="postForm" action="{{ route('timeline.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
            @csrf
            <div class="mb-3">
                <label for="post" class="form-label">投稿内容</label>
                <textarea id="post" name="post" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">画像 (任意)</label>
                <input type="file" id="image" name="image" accept="image/*" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">投稿する</button>
            <button type="button" class="btn btn-secondary" id="shareScheduleBtn" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                自分の予定を共有
            </button>
        </form>


        <!-- タイムライン表示部分 -->
<div id="timeline">
    @foreach ($posts as $post)
    <div class="post mb-4 p-3 border rounded" id="post-{{ $post->id }}">
        <div class="p_content">
            <!-- 投稿者のアイコンと名前 -->
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('profile.showUser', ['id' => $post->user->id]) }}">
                    <img src="{{ $post->user->icon_url }}" alt="{{ $post->user->name }}のアイコン" 
                        class="rounded-circle me-2" style="width: 40px; height: 40px;">
                </a>
                <strong>{{ $post->user->name }}</strong>
            </div>

            <!-- 投稿内容表示 -->
            <p>{{ $post->post }}</p>
            <p class="text-muted">
                <small>{{ $post->created_at }}</small>
            </p>

            @if ($post->image)
                <img src="{{ asset('storage/' . $post->image) }}" alt="投稿画像" class="img-fluid mb-2">
            @endif
        </div>
        <!-- 返信一覧 -->
        <div class="replies mt-3" id="replies-{{ $post->id }}">
            @foreach ($post->replies as $reply)
            <div class="reply mb-2 p-2 border rounded">
                <div class="d-flex align-items-center mb-2">
                    <a href="{{ route('profile.showUser', ['id' => $reply->user->id]) }}">
                        <img src="{{ $reply->user->icon_url }}" alt="{{ $reply->user->name }}のアイコン" 
                             class="rounded-circle me-2" style="width: 30px; height: 30px;">
                    </a>
                    <strong>{{ $reply->user->name }}</strong>

                </div>
            @endforeach
        </div>
    </div>

    <!-- モーダル: 自分の予定を共有 -->
    <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scheduleModalLabel">共有する予定を選択</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="scheduleList" class="list-group"></ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/timeline.js') }}"></script>
@endsection
