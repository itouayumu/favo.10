@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/detail.css') }}">
@endsection

@section('content')
    <div class="card">
        <!-- 推し詳細 -->
        <div class="card-header text-center">
            <img src="{{ $favorite->image_1 && Storage::exists('public/' . $favorite->image_1) 
                        ? Storage::url($favorite->image_1) 
                        : asset('img/default.png') }}" 
                 alt="{{ $favorite->name }}" class="detail-img">
            <h2>{{ $favorite->name }}</h2>
        </div>
        
        <div class="card-body">
            <!-- 推しの紹介 -->
            <div class="introduction mb-3">
                <p>{{ $favorite->description ?? '詳細情報はありません。' }}</p>
            </div>

            <!-- 編集ボタン -->
            <div class="text-center">
                <a href="{{ route('oshi.edit', $favorite->id) }}" class="btn btn-primary">編集</a>
            </div>
        </div>
    </div>
@endsection
