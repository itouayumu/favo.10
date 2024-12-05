<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新規登録</title>
</head>
<body>
    <h1>アカウント新規登録</h1>
    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">名前</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" required>
            @error('password')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation">パスワード（確認）</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>

        <div>
            <label for="introduction">自己紹介</label>
            <textarea name="introduction" id="introduction">{{ old('introduction') }}</textarea>
            @error('introduction')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="image">プロフィール画像</label>
            <input type="file" name="image" id="image">
            @error('image')<p>{{ $message }}</p>@enderror
        </div>

        <button type="submit">登録</button>
    </form>
</body>
</html>
