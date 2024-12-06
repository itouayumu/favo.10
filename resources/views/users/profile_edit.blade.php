@extends('layouts.app')

@section('content')
<div class="container">
    <h1>プロフィール編集 - タグ管理</h1>
    <form method="POST" action="{{ route('users.tags.attach', ['user' => $user->id]) }}">
        @csrf
        <div class="mb-3">
            <label for="tags" class="form-label">タグを選択</label>
            <select name="tags[]" id="tags" class="form-control" multiple>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ $user->tags->contains($tag->id) ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">更新</button>
    </form>
</div>
@endsection
