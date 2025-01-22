<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>おすすめの推し</title>
</head>
<body>
    <h1>おすすめの推し</h1>

    @if ($recommended)
        <h2>{{ $recommended->name }}</h2>
        <p>{{ $recommended->introduction }}</p>
        <div>
            @foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $image)
                @if($recommended->$image)
                    <!-- storage フォルダの画像を表示 -->
                    <img src="{{ asset('storage/' . $recommended->$image) }}" alt="{{ $recommended->name }}" style="width: 300px;">
                @endif
            @endforeach
        </div>
        <p>お気に入り数: {{ $recommended->favorite_count }}</p>

        <!-- お気に入り登録ボタン -->
        <form action="{{ route('addFavorite', $recommended->id) }}" method="POST">
            @csrf
            <button type="submit">この推しをお気に入りに登録</button>
        </form>

        <!-- 次のおすすめボタン -->
        <a href="{{ route('nextRecommended') }}">
            <button>次のおすすめを表示</button>
        </a>
    @else
        <p>まだ登録された推しがありません。</p>
    @endif

    <!-- メッセージ表示 -->
    @if (session('message'))
        <p>{{ session('message') }}</p>
    @endif
</body>
</html>
