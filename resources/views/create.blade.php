@extends('layouts.app')

@section('content')
<div class="container">
    <h1>新規登録</h1>

    <form action="{{ route('favorites.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- 名前 -->
        <div class="mb-3">
            <label for="name" class="form-label">名前</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <!-- ジャンル -->
        <div class="mb-3">
            <label for="genre_id" class="form-label">ジャンル</label>
            <select name="genre_id" id="genre_id" class="form-select">
                <option value="">なし</option>
                @foreach($genres as $genre)
                    <option value="{{ $genre->id }}">{{ $genre->genre_name }}</option>
                @endforeach
            </select>
        </div>

        <!-- 説明 -->
        <div class="mb-3">
            <label for="introduction" class="form-label">説明</label>
            <textarea name="introduction" id="introduction" class="form-control" rows="4"></textarea>
        </div>

        <!-- 画像 -->
        <div class="mb-3">
            <label for="image_1" class="form-label">画像</label>
            <input type="file" name="image_1" id="image_1" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
        <a href="{{ route('favorites.index') }}" class="btn btn-secondary">戻る</a>
    </form>
</div>
@endsection
