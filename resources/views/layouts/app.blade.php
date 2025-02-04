<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FanFolio</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{asset('css/scal.css')}}">
        <!-- ページごとのCSS -->
        @yield('css')
</head>
<body>
    <header>
        <img src="{{ asset('img/rogo.png') }}" alt="ロゴ" class="rogo">
        <a href="/profile"><img src="{{ auth()->user()->icon_url }}" alt="ユーザーアイコン" class="usericon1"></a>

    </header>


    <div class="container">
        <!-- 個別ページのコンテンツをここに挿入 -->
        @yield('content')
    </div>

<div class="footer">
    <footer>
        <ul>
            <li class="profile"><p>プロフィール</p><a href="/profile"><img src="{{asset('img/profile.png')}}" alt="プロフィール"></a></li>
            <li class="timeline"><p>タイムライン</p><a href="/timeline"><img src="{{asset('img/timeline.png')}}" alt="タイムライン"></a></li>
            <li class="schedule"><p>予定表</p><a href="/home"><img src="{{asset('img/schedule.png')}}" alt="予定表"></a></li>
            <li class="search"><p>検索</p><a href="/recommend"><img src="{{asset('img/search.png')}}" alt="検索"></a></li>
            <li class="shop"><p>ショップ</p><a href=""><img src="{{asset('img/shop.png')}}" alt="ショップ"></a></li>
        </ul>
    </footer>
</div>

    <!-- JavaScriptのリンクなど -->
    <script src="{{ asset('js/app.js') }}"></script>
        @yield('scripts')
</body>
</html>