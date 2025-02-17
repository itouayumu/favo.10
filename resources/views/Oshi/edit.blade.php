@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/o_edit.css') }}">
@endsection

@section('content')
<h2 class="heading">推し編集ページ</h2>
    <div class="c_form">

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

            <!-- 画像
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
            @endforeach -->

            <label for="image_1">アイコン画像</label><br>
            <div class="preview">
                <label for="image_1" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ Storage::url('public/' . $favorite->image_1) }}">
                <input type="file" id="image_1" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="image_2">紹介画像1</label><br>
            <div class="preview">
                <label for="image_2" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ Storage::url('public/' . $favorite->image_2) }}">
                <input type="file" id="image_2" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="image_3">紹介画像2</label><br>
            <div class="preview">
                <label for="image_3" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ Storage::url('public/' . $favorite->image_3) }}">
                <input type="file" id="image_3" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <label for="image_4">紹介画像3</label><br>
            <div class="preview">
                <label for="image_4" class="upload-icon"></label>
                <img id="imagePreview" class="image-preview" src="{{ Storage::url('public/' . $favorite->image_4) }}">
                <input type="file" id="image_4" class="file-input" name="image" accept="image/*"><br><br>
                @error('image')<p>{{ $message }}</p>@enderror
            </div>

            <div class="submit-btn">
                <button type="submit" class="btn">更新する</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
<script src="{{ asset('js/r_create') }}"></script>
@endsection