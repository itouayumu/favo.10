@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://via.placeholder.com/150' }}" 
                 alt="Profile Image" class="img-thumbnail rounded-circle" width="150">
            <h3>{{ $user->name }}</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <strong>プロフィール:</strong>
                <p>{{ $user->introduction ?? '詳細情報はありません。' }}</p>
            </div>
            <hr>
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-primary">編集</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">戻る</a>
            </div>
        </div>
    </div>
</div>
@endsection
