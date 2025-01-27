<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>おすすめの推し</title>
    <link rel="stylesheet" href="{{ asset('css/recommend.css') }}">
    <link rel="stylesheet" href="{{asset('css/scal.css')}}">
</head>
<body>
    <header>
        <img src="{{ asset('img/rogo.png') }}" alt="ロゴ" class="rogo">
    </header>

    <div class="content">
        <h1 class="heading">おすすめの推し</h1>

        @if ($recommended)
        <div class="favos">
            <div class="favo_img">
                @foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $image)
                    @if($recommended->$image)
                        <!-- storage フォルダの画像を表示 -->
                         <div class="slide">
                             <img src="{{ asset('storage/' . $recommended->$image) }}" alt="{{ $recommended->name }}" class="image">
                         </div>
                    @endif
                @endforeach
            </div>
            <h2 class="f_name">{{ $recommended->name }}</h2>
            <p class="f_intro">{{ $recommended->introduction }}</p>
            <p class="f_count">お気に入り数: {{ $recommended->favorite_count }}</p>
        </div>
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>

        <div class="btn">
            <!-- お気に入り登録ボタン -->
            <div class="reg">
                <form action="{{ route('addFavorite', $recommended->id) }}" method="POST">
                    @csrf
                    <button type="submit">推</button>
                </form>
            </div>

            <!-- 次のおすすめボタン -->
            <div class="nex">
                <a href="{{ route('nextRecommended') }}">
                    <button>次</button>
                </a>
            </div>
        </div>
        @else
            <p>まだ登録された推しがありません。</p>
        @endif

        <!-- メッセージ表示 -->
        @if (session('message'))
            <p>{{ session('message') }}</p>
        @endif
    </div>

    <footer>
        <ul>
            <li class="profile"><p>プロフィール</p><a href="/profile"><img src="{{asset('img/profile.png')}}" alt="プロフィール"></a></li>
            <li class="timeline"><p>タイムライン</p><a href="/timeline"><img src="{{asset('img/timeline.png')}}" alt="タイムライン"></a></li>
            <li class="schedule"><p>予定表</p><a href="/home"><img src="{{asset('img/schedule.png')}}" alt="予定表"></a></li>
            <li class="search"><p>検索</p><a href="/recommend"><img src="{{asset('img/search.png')}}" alt="検索"></a></li>
            <li class="shop"><p>ショップ</p><a href=""><img src="{{asset('img/shop.png')}}" alt="ショップ"></a></li>
        </ul>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/recommend.js') }}"></script>
</body>
</html>
