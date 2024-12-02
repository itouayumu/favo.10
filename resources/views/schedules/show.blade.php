<!DOCTYPE html>
<html>
<head>
    <title>{{ $schedule->title }} の詳細</title>
</head>
<body>
    <h1>{{ $schedule->title }}</h1>
    <p>開始日: {{ $schedule->start_date }} {{ $schedule->start_time }}</p>
    <p>終了日: {{ $schedule->end_date }} {{ $schedule->end_time }}</p>
    <p><a href="{{ $schedule->url }}">{{ $schedule->url }}</a></p>

    <!-- 推しのサムネイルがあれば表示 -->
    @if ($schedule->thumbnail)
        <p>推しのサムネイル:</p>
        <img src="{{ Storage::url($schedule->thumbnail) }}" alt="推しのサムネイル" width="200">
    @endif

    <!-- 画像があれば表示 -->
    @if ($schedule->image)
        <p>画像:</p>
        <img src="{{ Storage::url($schedule->image) }}" alt="{{ $schedule->title }}">
    @endif

    <a href="/schedules/{{ $schedule->id }}/edit">編集</a>

    <form method="post" action="/schedules/{{ $schedule->id }}">
        @csrf
        @method('DELETE')
        <button type="submit">削除</button>
    </form>

    <a href="/">戻る</a>
</body>
</html>
