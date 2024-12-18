<div>
    <h3>{{ $user->name }} のタグ管理</h3>

    <!-- タグ作成フォーム -->
    <h4>新しいタグを作成</h4>
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
    <h4>公開タグ</h4>
    <ul id="public-tags">
        @foreach ($tags->where('pivot.hidden_flag', 0) as $tag)
            <li id="tag-{{ $tag->id }}">
                <a href="#" class="tag-click" data-tag-id="{{ $tag->id }}">{{ $tag->name }}</a>
                (クリック数: <span id="click-count-{{ $tag->id }}">{{ $tag->pivot->count }}</span>)
                <button class="btn btn-secondary" onclick="toggleVisibility({{ $tag->id }}, 1)">非公開にする</button>
                <button class="btn btn-danger" onclick="deleteTag({{ $tag->id }})">削除</button>
            </li>
        @endforeach
    </ul>

    <hr>

    <!-- 非公開タグ -->
    <h4>非公開タグ</h4>
    <ul id="private-tags">
        @foreach ($tags->where('pivot.hidden_flag', 1) as $tag)
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
    //クリック数
    document.querySelectorAll('.tag-click').forEach(element => {
        element.addEventListener('click', event => {
            event.preventDefault();
            const tagId = event.target.dataset.tagId;

            fetch(`/tags/${tagId}/count`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`click-count-${tagId}`).textContent = data.click_count;
                }
            });
        });
    });

    //公開・非公開
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

    //削除
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
