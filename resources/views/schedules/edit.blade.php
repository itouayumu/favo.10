@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit_schedule.css') }}">
@endsection

@section('content')
    <h1 class="heading">予定編集</h1>

    <div class="c_form">
        <!-- メッセージ表示 -->
        @if (session('success'))
            <p style="color: green;">{{ session('success') }}</p>
        @endif

        <!-- 更新フォーム -->
        <form method="post" action="{{ route('schedules.update', ['schedule' => $schedule->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <label for="title">タイトル</label><br> 
    <select name="title" id="title"required value="{{ old('title') }}" class="custom-select">
  <option value="">選択してください</option>
  <option value="リアルライブ">リアルライブ</option>
  <option value="リアルイベント">リアルイベント</option>
  <option value="配信予定">配信予定</option>
  <option value="ライブ配信">ライブ配信</option>
  <option value="グッズ発売日">グッズ発売日</option>
</select>

            <label>開始日</label><br>
            <input type="date" name="start_date" id="start_date" value="{{ $schedule->start_date }}" required><br>

            <label>開始時間</label><br>
            <input type="time" name="start_time" id="start_time" value="{{ $schedule->start_time }}"><br>

            <label>終了日</label><br>
            <input type="date" name="end_date" id="end_date" value="{{ $schedule->end_date }}"><br>

            <label>終了時間</label><br>
            <input type="time" name="end_time" id="end_time" value="{{ $schedule->end_time }}"><br>

            <label for="image_1">画像</label><br>
            <div class="preview">
                <label for="image_1" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ asset('storage/' . $schedule->image) }}">
                <input type="file" id="image_1" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="content">内容</label>
            <textarea id="content" name="content" required>{{ $schedule->content }}</textarea><br>

            <div class="update">
                <button type="submit">更新</button>
            </div>
        </form>

        <!-- 削除フォーム -->
        <form method="post" action="{{ route('schedules.destroy', ['schedule' => $schedule->id]) }}" class="delete">
            @csrf
            @method('DELETE')
            <button type="submit">削除</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/edit_schedule.js') }}"></script>
@endsection