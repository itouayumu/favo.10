<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>推し検索</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{asset('css/scal.css')}}">
    <style>
        .favorite-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }
        .favorite-item img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .favorite-item .name {
            flex-grow: 1;
        }
        .favorite-item button {
            margin-left: 10px;
        }
        .favorite-item {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    padding: 10px;
    margin-bottom: 10px;
    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
}

.favorite-item img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
    border: 2px solid #ddd;
}

.favorite-item .name {
    flex-grow: 1;
    font-size: 18px;
    font-weight: bold;
    color: #333;
}

.favorite-item .actions {
    display: flex;
    align-items: center;
}

.favorite-item button {
    background-color: #fff;
    border: 2px solid #007bff;
    color: #007bff;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 10px;
}

.favorite-item button:hover {
    background-color: #007bff;
    color: #fff;
}

.favorite-item a {
    color: #007bff;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
}

.favorite-item a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
<header>
        <img src="{{ asset('img/rogo.png') }}" alt="ロゴ" class="rogo">
        <a href="/profile"><img src="{{ auth()->user()->icon_url }}" alt="ユーザーアイコン" class="usericon1"></a>
    </header>
    <div class="container">
    <h1>推しの検索</h1>
    <input type="text" id="search" placeholder="推しの名前を入力">
    <div id="result"></div>
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

        <script>
$(document).ready(function () {
    let timer = null;

    function fetchFavorites(query = '') {
        $.ajax({
            url: "{{ route('favorites.search') }}",
            type: "GET",
            data: { query: query },
            success: function (response) {
                $('#result').html('');
                if (response.data.length === 0) {
                    $('#result').html('<p>' + response.message + '</p><button id="register">新規登録</button>');
                } else {
                    let resultHtml = '<div>';
                    response.data.forEach(favorite => {
                        let imageUrl = favorite.image_1 ? `/storage/${favorite.image_1}` : 'https://via.placeholder.com/50';
                        let followText = favorite.is_followed ? "フォロー解除" : "フォローする";
                        resultHtml += `
                            <div class="favorite-item">
                                <img src="${imageUrl}" alt="推しアイコン">
                                <span class="name">${favorite.name}</span>
                                <div class="actions">
                                    <button class="follow-btn" data-oshi-id="${favorite.id}">
                                        ${followText}
                                    </button>
                                    <a href="/oshi/${favorite.id}" class="detail-btn">詳細</a>
                                </div>
                            </div>
                        `;
                    });
                    resultHtml += '</div>';
                    $('#result').html(resultHtml);
                }
            }
        });
    }

    // フォローボタンをクリック
    $(document).on('click', '.follow-btn', function () {
        let oshiId = $(this).data('oshi-id');
        let button = $(this);

        $.ajax({
            url: `/follow/toggle/${oshiId}`,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.status === "followed") {
                    button.text("フォロー解除").css("background-color", "#fff").css("border-color", "#007bff");
                } else if (response.status === "unfollowed") {
                    button.text("フォローする").css("background-color", "#fff").css("border-color", "#007bff");
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("フォロー処理に失敗しました。");
            }
        });
    });

    // ページロード時に一覧取得
    fetchFavorites();

    // 入力時に検索（デバウンス処理）
    $('#search').on('keyup', function () {
        clearTimeout(timer);
        let query = $(this).val().trim();
        timer = setTimeout(function () {
            fetchFavorites(query);
        }, 300);
    });

    // 新規登録ボタンで登録ページへ
    $(document).on('click', '#register', function () {
        window.location.href = "{{ route('favorites.create') }}";
    });
});

</script>

</body>
</html>
