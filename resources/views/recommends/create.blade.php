<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>推しの新規登録</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- スタイルを適用する場合 -->
</head>
<body>
    <div class="container">
        <h1>推しの新規登録</h1>

        <!-- エラーメッセージの表示 -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- 成功メッセージの表示 -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- フォーム -->
        <form action="{{ route('recommends.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">名前:</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="genre_id">ジャンル:</label>
                <select name="genre_id" id="genre_id" class="form-control" required>
                    <option value="" disabled selected>ジャンルを選択してください</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="introduction">紹介:</label>
                <textarea name="introduction" id="introduction" class="form-control" rows="4"></textarea>
            </div>

            <div class="form-group">
                <label for="image_1">アイコン画像:</label>
                <input type="file" name="image_1" id="image_1" class="form-control">
            </div>

            <div class="form-group">
                <label for="image_2">紹介画像1:</label>
                <input type="file" name="image_2" id="image_2" class="form-control">
            </div>

            <div class="form-group">
                <label for="image_3">紹介画像2:</label>
                <input type="file" name="image_3" id="image_3" class="form-control">
            </div>

            <div class="form-group">
                <label for="image_4">紹介画像3:</label>
                <input type="file" name="image_4" id="image_4" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">登録</button>
        </form>
    </div>
</body>
</html>
