<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Default Title')</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
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

    </footer>

    <!-- JavaScriptのリンクなど -->
        @yield('scripts')
</body>
</html>