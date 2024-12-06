@extends('layouts.app')

@section('content')
<div class="container">
    <h1>タグ管理</h1>

    {{-- タグ作成フォーム --}}
    <form method="POST" action="{{ route('tags.store') }}">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">タグ名</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="create_user" class="form-label">作成者</label>
            <input type="text" name="create_user" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">作成</button>
    </form>

    <hr>

    {{-- タグ一覧 --}}
    <h2>タグ一覧</h2>
    <ul>
        @foreach($tags as $tag)
            <li>{{ $tag->name }} (作成者: {{ $tag->create_user }})</li>
        @endforeach
    </ul>
</div>
@endsection
