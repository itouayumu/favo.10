@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
    <h1 class="heading">新規登録</h1>

    <div class="form-container">
        <img src="{{ asset('img/osipin.png') }}" alt="押しピン" class="osipin">
        <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div>
            <label for="name">名前</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
            @error('name')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email">メールアドレス</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
            @error('email')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" required>
            @error('password')<p>{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation">パスワード（確認）</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required>
        </div>

        <div>
            <label for="introduction">自己紹介</label>
            <textarea name="introduction" id="introduction">{{ old('introduction') }}</textarea>
            @error('introduction')<p>{{ $message }}</p>@enderror
        </div>

        <div>
        <label for="fileInput">プロフィール画像</label>
            <div class="preview">
                <label for="fileInput" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ asset('img/kkrn_icon_user.png') }}">
                <input type="file" name="image" class="file-input" id="fileInput" accept="image/*">
                @error('image')<p>{{ $message }}</p>@enderror
            </div>
        </div>

        <button type="submit">登録</button>
        <button onclick="window.location.href='/'">戻る</button>
    </form>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/register.js') }}"></script>
@endsection