<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Default Title')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{asset('css/scal.css')}}">
        <!-- ページごとのCSS -->
        @yield('css')
</head>
<body>
    <header>
        <img src="{{ asset('img/rogo.png') }}" alt="ロゴ" class="rogo">
    </header>


    <div class="container">
        <!-- 個別ページのコンテンツをここに挿入 -->
        @yield('content')
    </div>

    <footer>
        <ul>
            <li class="profile"><p>プロフィール</p><a href=""><img src="{{asset('img/profile.png')}}" alt="プロフィール"></a></li>
            <li class="timeline"><p>タイムライン</p><a href="/timeline"><img src="{{asset('img/timeline.png')}}" alt="タイムライン"></a></li>
            <li class="schedule"><p>予定表</p><a href=""><img src="{{asset('img/schedule.png')}}" alt="予定表"></a></li>
            <li class="search"><p>検索</p><a href="/search"><img src="{{asset('img/search.png')}}" alt="検索"></a></li>
            <li class="shop"><p>ショップ</p><a href=""><img src="{{asset('img/shop.png')}}" alt="ショップ"></a></li>
        </ul>
    </footer>

    <!-- JavaScriptのリンクなど -->
    <script src="{{ asset('js/app.js') }}"></script>
        @yield('scripts')
</body>
</html>