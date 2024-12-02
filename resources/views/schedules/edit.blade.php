<!DOCTYPE html>
<html>
<head>
    <title>予定編集</title>
</head>
<body>
    <h1>予定編集</h1>

    <form method="post" action="/schedules/{{ $schedule->id }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>タイトル: <input type="text" name="title" value="{{ $schedule->title }}" required></label><br>
        <label>画像: <input type="file" name="image"></label><br>
        <label>開始日: <input type="date" name="start_date" value="{{ $schedule->start_date }}" required></label><br>
        <label>開始時間: <input type="time" name="start_time" value="{{ $schedule->start_time }}"></label><br>
        <label>終了日: <input type="date" name="end_date" value="{{ $schedule->end_date }}"></label><br>
        <label>終了時間: <input type="time" name="end_time" value="{{ $schedule->end_time }}"></label><br>
        <label>URL: <input type="url" name="url" value="{{ $schedule->url }}"></label><br>

        <button type="submit">更新</button>
    </form>

    <form method="post" action="/schedules/{{ $schedule->id }}">
        @csrf
        @method('DELETE')
        <button type="submit">削除</button>
    </form>
</body>
</html>
