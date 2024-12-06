@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <h1 class="heading">ログイン</h1>

    <div class="form-container">
        <img src="{{ asset('img/osipin.png') }}" alt="押しピン">
        <form action="{{ route('login') }}" method="post">
            @csrf
            <label for="email">メールアドレス</label>
            <input type="email" id="email" name="email" required>

            <label for="password">パスワード</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">ログイン</button>
            <button type="button" onclick="window.location.href='/register'">新規登録</button>
            <button type="submit">パスワードを忘れた方はこちら</button>
        </form>
    </div>
@endsection
