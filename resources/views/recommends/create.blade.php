@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/r_create.css') }}">
@endsection

@section('content')
<h1 class="heading">推しの新規登録</h1>
    <div class="c_form">

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

        <!-- 成功メッセージの表示 -->
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- フォーム -->
        <form action="{{ route('recommends.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">名前</label><br>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="genre_id">ジャンル</label><br>
                <select name="genre_id" id="genre_id" class="custom-select" required>
                    <option value="" disabled selected>ジャンルを選択してください</option>
                    @foreach ($genres as $genre)
                        <option value="{{ $genre->id }}">{{ $genre->genre_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="introduction">紹介</label><br>
                <textarea name="introduction" id="introduction" class="form-control" rows="4"></textarea>
            </div>

            <label for="image_1">アイコン画像</label><br>
            <div class="preview">
                <label for="image_1" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ asset('img/kkrn_icon_user.png') }}">
                <input type="file" id="image_1" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="image_2">紹介画像1</label><br>
            <div class="preview">
                <label for="image_2" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ asset('img/kkrn_icon_user.png') }}">
                <input type="file" id="image_2" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="image_3">紹介画像2</label><br>
            <div class="preview">
                <label for="image_3" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ asset('img/kkrn_icon_user.png') }}">
                <input type="file" id="image_3" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="image_4">紹介画像3</label><br>
            <div class="preview">
                <label for="image_4" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ asset('img/kkrn_icon_user.png') }}">
                <input type="file" id="image_4" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>
            <div class="submit-btn">
                <button type="submit" class="btn btn-primary">登録</button>
            </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/r_create.js') }}"></script>
@endsection