@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
    <img src="{{ asset('img/fs11.png') }}" alt="husen">

    <div class="form-container">
        <form action="#" method="post">
                <label for="name">ユーザーID</label>
                <input type="text" id="name" name="name" required>

                <label for="email">パスワード</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">ログイン</button>
                <button onclick="window.location.href='/register'">新規登録</button>
                <button type="submit">パスワードを忘れた方はこちら</button>
        </form>
    </div>
@endsection
