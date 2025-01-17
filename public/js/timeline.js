document.addEventListener('DOMContentLoaded', () => {
    let lastFetchedReply = new Date().toISOString(); // 最後に取得した返信の時刻

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

    // 返信表示ボタンの処理
    document.querySelectorAll('.reply-show').forEach(button => {
        button.addEventListener('click', async (event) => {
            const postId = event.target.dataset.postId;
            const replyList = document.getElementById(`reply-list-${postId}`);

            if (replyList.classList.contains('d-none')) {
                try {
                    // サーバーから返信を取得
                    const response = await fetch(`/reply/fetch/${postId}`);
                    const replies = await response.json();

                    // レスポンスを描画
                    const replyHtml = replies.map(reply => `
                        <div class="reply" id="reply-${reply.id}">
                            <div class="reply-header">
                                <strong>${reply.user ? reply.user.name : '匿名ユーザー'}</strong>
                                <span class="text-muted">${new Date(reply.created_at).toLocaleString()}</span>
                            </div>
                            <div class="reply-body">
                                <p>${reply.comment}</p>
                                ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="reply-image">` : ''}
                            </div>
                        </div>
                    `).join('');
                    replyList.innerHTML = replyHtml;

                    // 返信リストを表示
                    replyList.classList.remove('d-none');
                } catch (error) {
                    console.error('エラー:', error);
                }
            } else {
                // 返信リストを非表示
                replyList.classList.add('d-none');
            }
        });
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

    // jQueryを使用した動的なイベント処理

    // 返信フォームの表示切り替え
    $(document).on('click', '.reply-toggle', function () {
        const postId = $(this).data('post-id');
        const form = $(`#reply-form-${postId}`);
        form.toggleClass('d-none');
    });

    // 返信フォームの非同期送信
    $(document).on('submit', '.replyForm', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const postId = form.data('post-id');

        $.ajax({
            url: '/reply/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRFトークン
            },
            success: function (data) {
                alert(data.message);
                form[0].reset();
                fetchReplies(postId); // 返信を再取得
            },
            error: function (xhr) {
                alert('返信の保存に失敗しました: ' + xhr.responseJSON.message);
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
