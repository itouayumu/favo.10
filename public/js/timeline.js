document.addEventListener('DOMContentLoaded', () => {
    let lastFetchedReply = new Date().toISOString(); // 最後に取得した返信の時刻
    const favoriteSearchInput = document.getElementById('favorite-search'); // 推し検索の入力ボックス
    const favoriteList = document.getElementById('favorite-list'); // 推し候補リスト
    const oshiNameInput = document.getElementById('oshiname'); // 選択された推しの名前を保持する隠しフィールド

    // 投稿フォームの送信処理
    const postForm = document.getElementById('postForm');
    postForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(postForm);
        try {
            const response = await fetch(postForm.action, {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const newPost = await response.json();
                console.log('新しい投稿:', newPost);
                // タイムラインに新しい投稿を追加するロジックを実装
            } else {
                console.error('投稿の送信に失敗しました');
            }
        } catch (error) {
            console.error('エラー:', error);
        }
    });

    // 推しの名前検索処理
    favoriteSearchInput.addEventListener('input', async function () {
        const query = this.value.trim();

        if (query.length === 0) {
            favoriteList.style.display = 'none';
            favoriteList.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`/favorites/search?query=${encodeURIComponent(query)}`);
            if (response.ok) {
                const favorites = await response.json();

                favoriteList.innerHTML = ''; // リストをクリア
                favorites.forEach(favorite => {
                    const listItem = document.createElement('li');
                    listItem.textContent = favorite.name;
                    listItem.dataset.favoriteId = favorite.id; // 推しのIDを保持
                    listItem.classList.add('list-group-item', 'list-group-item-action');
                    favoriteList.appendChild(listItem);
                });

                favoriteList.style.display = 'block';
            } else {
                console.error('推し検索に失敗しました');
            }
        } catch (error) {
            console.error('エラー:', error);
        }
    });

    // 推しの名前を選択
    favoriteList.addEventListener('click', function (event) {
        if (event.target.tagName === 'LI') {
            const selectedName = event.target.textContent;
            const selectedId = event.target.dataset.favoriteId;

            favoriteSearchInput.value = selectedName; // 検索ボックスに選択した名前を表示
            oshiNameInput.value = selectedId; // 隠しフィールドにIDをセット

            favoriteList.style.display = 'none'; // リストを隠す
            favoriteList.innerHTML = ''; // リストをクリア
        }
    });

    // 検索ボックス外をクリックしたら候補リストを隠す
    document.addEventListener('click', (event) => {
        if (!favoriteSearchInput.contains(event.target) && !favoriteList.contains(event.target)) {
            favoriteList.style.display = 'none';
        }
    });

    // 返信フォームの送信処理
    document.querySelectorAll('.replyForm').forEach(form => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const postId = form.dataset.postId;
            const formData = new FormData(form);

            try {
                const response = await fetch(`/reply/store/${postId}`, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    const newReply = await response.json();
                    console.log('新しい返信:', newReply);
                    // 新しい返信を表示するロジックを実装
                } else {
                    console.error('返信の送信に失敗しました');
                }
            } catch (error) {
                console.error('エラー:', error);
            }
        });
    });

    // 新規返信を取得して表示する関数
    function fetchNewReplies() {
        $.ajax({
            url: '/reply/fetch-new-replies', // 新規返信を取得するAPIエンドポイント
            type: 'GET',
            data: { last_fetched: lastFetchedReply },
            success: function (replies) {
                console.log('新しい返信:', replies);
                if (replies.length > 0) {
                    replies.forEach(reply => {
                        const postId = reply.post_id;
                        const repliesContainer = $(`#replies-${postId}`);

                        // 新規返信のみ追加
                        if ($(`#reply-${reply.id}`).length === 0) { // 重複チェック
                            const replyElement = `
                                <div class="reply mb-2 p-2 border rounded" id="reply-${reply.id}">
                                    <p>${reply.comment}</p>
                                    <small class="text-muted">${new Date(reply.created_at).toLocaleString()}</small>
                                    ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid">` : ''}
                                </div>
                            `;
                            repliesContainer.append(replyElement); // 返信リストの一番下に追加
                        }
                    });
                    lastFetchedReply = new Date().toISOString(); // 最後の取得時刻を更新
                }
            },
            error: function (xhr) {
                console.error('新しい返信の取得に失敗:', xhr.responseText);
            }
        });
    }

    // 5秒ごとに新しい返信をチェック
    setInterval(fetchNewReplies, 5000);
});
