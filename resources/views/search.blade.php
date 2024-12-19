@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center">
        <h1>推し一覧</h1>
        <a href="{{ route('favorites.create') }}" class="btn btn-success">新規登録</a>
    </div>

    <!-- 検索フォーム -->
    <div class="mb-3">
        <input type="text" id="search-box" class="form-control" placeholder="推し名で検索">
    </div>

    <!-- 一覧表示 -->
    <div id="favorites-list">
        <table class="table">
            <thead>
                <tr>
                    <th>アイコン</th>
                    <th>ID</th>
                    <th>名前</th>
                    <th>ジャンル</th>
                    <th>説明</th>
                </tr>
            </thead>
            <tbody>
                @foreach($favorites as $favorite)
                    <tr>
                        <td>
                            @if($favorite->icon)
                                <img src="{{ asset('storage/' . $favorite->icon) }}" alt="Icon" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    N/A
                                </div>
                            @endif
                        </td>
                        <td>{{ $favorite->id }}</td>
                        <td>{{ $favorite->name }}</td>
                        <td>{{ $favorite->genre->genre_name ?? 'なし' }}</td>
                        <td>{{ $favorite->introduction }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const searchBox = document.getElementById('search-box');
    const favoritesList = document.getElementById('favorites-list');

    searchBox.addEventListener('input', function () {
        const query = this.value;

        fetch(`/favorites/search?search=${query}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            let html = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>アイコン</th>
                            <th>ID</th>
                            <th>名前</th>
                            <th>ジャンル</th>
                            <th>説明</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (data.length > 0) {
                data.forEach(favorite => {
                    html += `
                        <tr>
                            <td>
                                ${favorite.icon ? `<img src="/storage/${favorite.icon}" alt="Icon" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">` : `<div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">N/A</div>`}
                            </td>
                            <td>${favorite.id}</td>
                            <td>${favorite.name}</td>
                            <td>${favorite.genre ? favorite.genre.genre_name : 'なし'}</td>
                            <td>${favorite.introduction ?? ''}</td>
                        </tr>
                    `;
                });
            } else {
                html += `
                    <tr>
                        <td colspan="5">該当する推しが見つかりませんでした。</td>
                    </tr>
                `;
            }

            html += `
                    </tbody>
                </table>
            `;

            favoritesList.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
    });
});

</script>
@endsection
