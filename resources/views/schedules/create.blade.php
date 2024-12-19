@extends('layouts.app')

@section('content')
<h1>予定を作成</h1>

<form action="/schedules" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- 推しの名前検索 -->
    <label for="favorite-search">推しの名前を検索:</label><br>
    <input type="text" id="favorite-search" placeholder="推しの名前を入力" autocomplete="off">
    <ul id="favorite-list" style="border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none;"></ul>

    <!-- 選択された推しのIDを格納する隠しフィールド -->
    <input type="hidden" id="oshiname" name="oshiname" value="">

    <br><br>

    <label for="title">タイトル:</label><br>
    <input type="text" id="title" name="title" required value="{{ old('title') }}"><br><br>

    <label for="start_date">開始日:</label><br>
    <input type="date" id="start_date" name="start_date" required value="{{ old('start_date') }}"><br><br>

    <label for="start_time">開始時間:</label><br>
    <input type="time" id="start_time" name="start_time" required value="{{ old('start_time') }}"><br><br>

    <label for="end_date">終了日:</label><br>
    <input type="date" id="end_date" name="end_date" required value="{{ old('end_date') }}"><br><br>

    <label for="end_time">終了時間:</label><br>
    <input type="time" id="end_time" name="end_time" required value="{{ old('end_time') }}"><br><br>

    <label for="thumbnail">サムネイル画像:</label><br>
    <input type="file" id="thumbnail" name="image" accept="image/*"><br><br>

    <label for="content">内容</label>
    <textarea id="content" name="content" required>{{ old('content') }}</textarea><br><br>

    <button type="submit">予定を作成</button>
</form>

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

<script src="{{ asset('js/serch_favorite.js') }}"></script>
@endsection
