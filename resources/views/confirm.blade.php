@extends('layouts.app')

@section('content')
<div class="container2">
    <h1>リンク遷移確認</h1>
    <p>以下のリンク先に移動します。<br>よろしいですか？</p>
    <div class="alert alert-warning">
        <strong>リンク先:</strong> {{ $url }}
    </div>
    <div class="d-flex">
        <a href="{{ $url }}" class="btn btn-primary me-2" target="_blank" rel="noopener noreferrer">移動する</a>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">戻る</a>
    </div>
</div>
@endsection
