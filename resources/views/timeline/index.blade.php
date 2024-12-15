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
        <div class="mb-4">
            <form id="postForm" enctype="multipart/form-data">
                <textarea id="postContent" name="post" class="form-control" rows="3" placeholder="いまどうしてる？"></textarea>
                <input type="file" id="postImage" name="image" class="form-control mt-2">
                <button type="submit" class="btn btn-primary mt-2">投稿する</button>
            </form>
        </div>

        <!-- タイムライン -->
        <div id="timeline">
            @foreach ($posts as $post)
                <div class="card mb-3">
                    <div class="card-body">
                        <p>{{ $post->post }}</p>
                        @if ($post->image)
                            <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid">
                        @endif
                        <small class="text-muted">作成日時: {{ $post->created_at }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#postForm').on('submit', function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                $.ajax({
                    url: '/timeline',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert(response.message);
                        location.reload(); // ページをリロード
                    },
                    error: function () {
                        alert('投稿に失敗しました。');
                    }
                });
            });
        });
    </script>
</body>
</html>
