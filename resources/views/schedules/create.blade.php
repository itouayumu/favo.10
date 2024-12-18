@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create_schedule.css') }}">
@endsection

@section('content')
<div class="container">
    <form action="/schedules" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="oshiname">推しの名前:</label>
        <input type="text" id="oshiname" name="oshiname" required>

        <label for="title">タイトル:</label>
        <input type="text" id="title" name="title" required>

        <label for="thumbnail">配信サムネイル:</label>
        <img id="preview" class="img_datareg" width="auto" height="200px">
        <label>
            <span class="filelabel" title="ファイルを選択">
                <input type="file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(event)">
            </span>
        </label>
        <img id="image_preview" src="" alt="画像プレビュー" style="display:none; max-width: 200px; max-height: 200px;">

        <label for="start_date">開始日:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="start_time">開始時間:</label>
        <input type="time" id="start_time" name="start_time" required>

        <label for="end_date">終了日:</label>
        <input type="date" id="end_date" name="end_date" required>

        <label for="end_time">終了時間:</label>
        <input type="time" id="end_time" name="end_time" required>

        <button type="submit">予定を作成</button>
        <button type="button" onclick="resetPreview()">リセット</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function resetPreview() {
        document.getElementById('thumbnail').value = "";
        document.getElementById('image_preview').src = "";
        document.getElementById('image_preview').style.display = "none";
    }

    function previewImage(event) {
        var file = event.target.files[0];
        var reader = new FileReader();

        reader.onload = function() {
            var imagePreview = document.getElementById("image_preview");
            imagePreview.src = reader.result;
            imagePreview.style.display = "block"; // Show the image preview
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
