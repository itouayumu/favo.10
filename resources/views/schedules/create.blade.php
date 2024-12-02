<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予定作成</title>
</head>
<body>
    <h1>予定を作成</h1>
    <form action="/schedules" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="oshiname">推しの名前:</label><br>
        <input type="text" id="oshiname" name="oshiname" required><br><br>

        <label for="title">タイトル:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="thumbnail">配信サムネイル:</label><br>
        <img id="preview" class="img_datareg"width="auto" height="200px">
        <label>
            <span class="filelabel" title="ファイルを選択">
            <input type="file" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(event)"><br><br>
        <img id="image_preview" src="" alt="画像プレビュー" style="display:none; max-width: 200px; max-height: 200px;" onchange="previewFile(this)">
<br><br>
        </label>

        <label for="start_date">開始日:</label><br>
        <input type="date" id="start_date" name="start_date" required><br><br>

        <label for="start_time">開始時間:</label><br>
        <input type="time" id="start_time" name="start_time" required><br><br>

        <label for="end_date">終了日:</label><br>
        <input type="date" id="end_date" name="end_date" required><br><br>

        <label for="end_time">終了時間:</label><br>
        <input type="time" id="end_time" name="end_time" required><br><br>

        <button type="submit">予定を作成</button>
        <button type="button" onclick="resetPreview()">リセット</button>
<script>
  function resetPreview() {
    document.getElementById('thumbnail').value = "";
    document.getElementById('preview').src = "";
  }
</script>

    </form>
    <script src="{{ asset('/js/image.js') }}"></script>

    <script>
        function previewImage(event) {
            var file = event.target.files[0];
            var reader = new FileReader();
            
            reader.onload = function() {
                var imagePreview = document.getElementById("image_preview");
                imagePreview.src = reader.result;
                imagePreview.style.display = "block"; // Show the image
            }

            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
