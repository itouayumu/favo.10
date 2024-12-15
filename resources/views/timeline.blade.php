<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タイムライン</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>タイムライン</h1>

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
        </form>

        <!-- タイムライン表示部分 -->
        <div id="timeline">
            @foreach ($posts as $post)
                <div class="post mb-4 p-3 border rounded" id="post-{{ $post->id }}">
                    <!-- 投稿内容表示 -->
                    <p>{{ $post->post }}</p>
                    <p class="text-muted">
                        <small>{{ $post->created_at }}</small>
                    </p>
                    @if ($post->image)
                        <img src="{{ asset('storage/' . $post->image) }}" alt="投稿画像" class="img-fluid mb-2">
                    @endif

                    <!-- 返信フォーム -->
                    <form class="replyForm" data-post-id="{{ $post->id }}">
                        @csrf
                        <input type="hidden" name="post_id" value="{{ $post->id }}"> <!-- post_id -->
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" placeholder="返信を書く" required></textarea>
                        </div>
                        <div class="mb-2">
                            <input type="file" name="image" accept="image/*" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-sm btn-secondary">返信する</button>
                    </form>

                    <!-- 返信一覧 -->
                    <div class="replies mt-3" id="replies-{{ $post->id }}"></div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- timeline.js の読み込み -->
    <script src="{{ asset('js/timeline.js') }}"></script>
</body>
</html>