@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/s_create.css') }}">
@endsection

@section('content')
<h1 class="heading">予定を作成</h1>

<div class="form-container">
<img src="{{ asset('img/osipin.png') }}" alt="押しピン" class="osipin">
<form action="/schedules" method="POST" enctype="multipart/form-data">
    @csrf

    <!-- 推しの名前検索 -->
    <label for="favorite-search">推しの名前を検索</label><br>
    <input type="text" id="favorite-search" placeholder="推しの名前を入力" autocomplete="off">
    <ul id="favorite-list" style="border: 1px solid #ccc; max-height: 150px; overflow-y: auto; display: none;"></ul>

    <!-- 選択された推しのIDを格納する隠しフィールド -->
    <input type="hidden" id="oshiname" name="oshiname" value="">

    <br><br>

    <label for="title">タイトル</label><br> 
    <select name="title" id="title"required value="{{ old('title') }}">
  <option value="">選択してください</option>
  <option value="リアルライブ">リアルライブ</option>
  <option value="リアルイベント">リアルイベント</option>
  <option value="配信予定">配信予定</option>
  <option value="ライブ配信">ライブ配信</option>
  <option value="グッズ発売日">グッズ発売日</option>
</select>

    <label for="start_date">開始日</label><br>
    <input type="date" id="start_date" name="start_date" required value="{{ old('start_date') }}"><br><br>

    <label for="start_time">開始時間</label><br>
    <input type="time" id="start_time" name="start_time" required value="00:00" step="300"><br><br>

    <label for="end_date">終了日</label><br>
    <input type="date" id="end_date" name="end_date" required value="{{ old('end_date') }}"><br><br>

    <label for="end_time">終了時間</label><br>
    <input type="time" id="end_time" name="end_time" required value="00:00" step="300"><br><br>

    <label for="thumbnail">サムネイル画像</label><br>
    <div class="preview">
        <label for="thumbnail" class="upload-icon"></label>
        <img id="imagePreview" class="image-preview" src="">
        <input type="file" id="thumbnail" class="file-input" name="image" accept="image/*"><br><br>
        @error('image')<p>{{ $message }}</p>@enderror
    </div>

    <label for="content">内容</label>
    <textarea id="content" name="content" required>{{ old('content') }}</textarea><br><br>

    <button type="submit">予定を作成</button>
</form>
</div>

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

@endsection

@section("scripts")
<script src="{{ asset('js/serch_favorite.js') }}"></script>
<script src="{{ asset('js/schedule.js') }}"></script>
@endsection
