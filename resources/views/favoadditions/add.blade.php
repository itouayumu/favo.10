@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<img src="{{ asset('img/fs11.png') }}" alt="husen">

<div class="form-container">
    <form action="#" method="post">
            <label for="pic">写真</label>
            <input type="file" id="pic" name="pic" required>

            <label for="name">名前<label>
            <input type="text" id="name" name="name" required>

            <button type="submit">タグ編集</button>

            <label for="pro">プロフィール・詳細情報</label>
            <input type="text" id="pro" name="pro" required>

            <label for="picadd">その他画像</label>
            <input type="file" id="picadd" name="picadd" required>

            <button type="submit">編集確定</button>
            <button type="submit">戻る</button>
        </form>
    </div>
@endsection