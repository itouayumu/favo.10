@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
    <div class="container">
        <h2>推し編集ページ</h2>

        <!-- フォーム -->
        <form action="{{ route('oshi.update', $favorite->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 名前 -->
            <div class="form-group">
                <label for="name">名前</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $favorite->name) }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- 紹介文 -->
            <div class="form-group">
                <label for="introduction">紹介文</label>
                <textarea name="introduction" id="introduction" class="form-control" rows="5">{{ old('introduction', $favorite->introduction) }}</textarea>
                @error('introduction')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- 画像 -->
            @foreach (['image_1', 'image_2', 'image_3', 'image_4'] as $imageField)
                <div class="form-group">
                    <label for="{{ $imageField }}">{{ strtoupper(str_replace('_', ' ', $imageField)) }}</label>
                    @if ($favorite->$imageField)
                        <div>
                            <img src="{{ Storage::url('public/' . $favorite->$imageField) }}" alt="Current Image" class="current-img">
                        </div>
                    @endif
                    <input type="file" name="{{ $imageField }}" id="{{ $imageField }}" class="form-control-file">
                    @error($imageField)
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
            @endforeach

            <!-- 更新ボタン -->
            <button type="submit" class="btn btn-primary mt-3">更新する</button>
        </form>
    </div>
@endsection
