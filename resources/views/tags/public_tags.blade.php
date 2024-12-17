@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $user->name }} のタグ管理</h1>

    <!-- タグ作成フォーム -->
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

    <!-- 公開タグ -->
    <h3>公開タグ</h3>
    <ul id="public-tags">
        @foreach ($tags->where('pivot.hidden_flag', 0) as $tag)
            <li id="tag-{{ $tag->id }}">
                <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">
                    {{ $tag->name }}
                </a> (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                <button class="btn btn-secondary" onclick="toggleVisibility({{ $tag->id }}, 1)">非公開にする</button>
                <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
            </li>
        @endforeach
    </ul>

    <hr>

    <!-- 非公開タグ -->
    <h3>非公開タグ</h3>
    <ul id="private-tags">
        @foreach ($tags->where('pivot.hidden_flag', 1) as $tag)
            <li id="tag-{{ $tag->id }}">
                <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">
                    {{ $tag->name }}
                </a> (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                <button class="btn btn-warning" onclick="toggleVisibility({{ $tag->id }}, 0)">公開にする</button>
                <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
            </li>
        @endforeach
    </ul>
</div>

<script>
    document.querySelectorAll('.tag-click').forEach(function(element) {
        element.addEventListener('click', function(event) {
            event.preventDefault();
            const tagId = event.target.getAttribute('data-tag-id');

            fetch(`/tags/${tagId}/count`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`click-count-${tagId}`).textContent = data.click_count;
                }
            });
        });
    });

    function toggleVisibility(tagId, hiddenFlag) {
    fetch(`/tags/${tagId}/visibility`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ hidden_flag: hiddenFlag }) // 送信データを修正
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tagElement = document.getElementById(`tag-${tagId}`);
            if (hiddenFlag === 0) {
                // 非公開 -> 公開
                document.getElementById('public-tags').appendChild(tagElement);
                tagElement.querySelector('button').textContent = '非公開にする';
                tagElement.querySelector('button').setAttribute('onclick', `toggleVisibility(${tagId}, 1)`);
                tagElement.querySelector('button').classList.replace('btn-warning', 'btn-secondary');
            } else {
                // 公開 -> 非公開
                document.getElementById('private-tags').appendChild(tagElement);
                tagElement.querySelector('button').textContent = '公開にする';
                tagElement.querySelector('button').setAttribute('onclick', `toggleVisibility(${tagId}, 0)`);
                tagElement.querySelector('button').classList.replace('btn-secondary', 'btn-warning');
            }
        } else {
            alert('公開/非公開の切り替えに失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    });
}


    function deleteTag(tagId) {
        fetch(`/tags/${tagId}/delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`tag-${tagId}`).remove();
            }
        });
    }
</script>
@endsection
