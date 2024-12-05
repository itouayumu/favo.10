<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>
</head>
<body>
    <h1>ログイン</h1>

    @if (session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @endif

    @if ($errors->any())
        <ul style="color: red;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div>
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <div>
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit">ログイン</button>
    </form>

    <p>アカウントをお持ちでない方は <a href="{{ route('register') }}">新規登録</a></p>
</body>
</html>
