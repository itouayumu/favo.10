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

        <h3>使われているタグ</h3>
        <ul id="public-tags">
            @foreach ($user->tags()->wherePivot('hidden_flag', 0)->wherePivot('delete_flag', 0)->get() as $tag)
                <li id="tag-{{ $tag->id }}">
                    <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                    (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                    <button class="btn btn-secondary" onclick="toggleVisibility({{ $tag->id }}, 1)">非公開にする</button>
                    <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
                </li>
            @endforeach
        </ul>

        <div>
            <!-- 自己紹介のフォーム -->
            <div class="form-group">
                <label for="introduction">自己紹介</label>
                <textarea name="introduction" id="introduction" class="form-control">{{ old('introduction', $user->introduction) }}</textarea>
                @error('introduction')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <!-- プロフィール画像のアップロード -->
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
        </div>
    </form>

    <!-- 新しいタグ作成フォーム -->
    <h3>新しいタグを作成</h3>
    <form action="{{ route('tags.create') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="tag-name">タグ名</label>
            <input type="text" id="tag-name" name="tag_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="visibility">公開設定</label>
            <select id="visibility" name="visibility" class="form-control">
                <option value="public">公開</option>
                <option value="private">非公開</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">タグを作成</button>
    </form>

    <hr>

    <!-- 非公開タグ -->
    <h3>使われてないタグ</h3>
    <ul id="private-tags">
        @foreach ($user->tags()->wherePivot('hidden_flag', 1)->wherePivot('delete_flag', 0)->get() as $tag)
            <li id="tag-{{ $tag->id }}">
                <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                <button class="btn btn-warning" onclick="toggleVisibility({{ $tag->id }}, 0)">公開にする</button>
                <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
            </li>
        @endforeach
    </ul>

</div>

<script>
    // 公開/非公開切り替え
    function toggleVisibility(tagId, hiddenFlag) {
        fetch(`/tags/${tagId}/visibility`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ hidden_flag: hiddenFlag })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tagElement = document.getElementById(`tag-${tagId}`);
                const targetList = hiddenFlag === 0 ? 'public-tags' : 'private-tags';
                document.getElementById(targetList).appendChild(tagElement);

                const button = tagElement.querySelector('button');
                button.textContent = hiddenFlag === 0 ? '非公開にする' : '公開にする';
                button.setAttribute('onclick', `toggleVisibility(${tagId}, ${hiddenFlag === 0 ? 1 : 0})`);
                button.classList.toggle('btn-secondary', hiddenFlag === 0);
                button.classList.toggle('btn-warning', hiddenFlag !== 0);
            } else {
                alert('公開/非公開の切り替えに失敗しました。');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。');
        });
    }

    // タグ削除
    function deleteTag(tagId) {
        fetch(`/tags/${tagId}/delete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`tag-${tagId}`).remove();
            } else {
                alert(data.message || 'タグの削除に失敗しました。');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。');
        });
    }
</script>
@endsection
