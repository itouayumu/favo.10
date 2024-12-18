@extends('layouts.app')

@section('content')
<div class="container">
    <h1>プロフィール編集</h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="introduction">自己紹介</label>
            <textarea name="introduction" id="introduction" class="form-control">{{ old('introduction', $user->introduction) }}</textarea>
            @error('introduction')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group">
            <label for="image">プロフィール画像</label><br>
            @if($user->image)
                <img src="{{ asset('storage/' . $user->image) }}" alt="プロフィール画像" width="150"><br>
            @endif
            <input type="file" name="image" id="image" class="form-control-file">
            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-3">更新</button>
    </form>
</div>
@endsection
