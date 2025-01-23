$(document).ready(function () {
    // DOMが準備完了した時に実行される処理

    const postForm = $('#postForm'); // 投稿フォームの要素
    const postList = $('#post-list'); // 投稿リストの要素
    const favoriteSearchInput = $('#favorite-search'); // 推しの名前検索入力フィールド
    const favoriteList = $('#favorite-list'); // 推しリストの要素
    const favoriteIdInput = $('#favorite_id'); // 推しIDを保持する隠しフィールド
    const favoriteError = $('#favorite-error'); // エラーメッセージ表示エリア
    let currentIndex = -1; // 現在選択しているリスト項目のインデックス
    let debounceTimeout; // 検索のデバウンス用タイマー

    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // CSRFトークンを取得

    /**
     * 推しの名前検索処理（デバウンス対応）
     */
    favoriteSearchInput.on('input', function () {
        clearTimeout(debounceTimeout); // 直前のタイマーをクリア
        debounceTimeout = setTimeout(() => performFavoriteSearch(this.value.trim()), 300); // 入力後300msで検索を実行
    });

    // 推しの名前を検索して結果を表示する関数
    async function performFavoriteSearch(query) {
        if (query.length === 0) {
            favoriteList.hide().empty(); // クエリが空ならリストを非表示
            return;
        }

        try {
            // サーバーに検索リクエストを送る
            const response = await fetch(`/favorites/search?query=${encodeURIComponent(query)}`);
            if (response.ok) {
                const favorites = await response.json(); // 結果をJSON形式で取得
                renderFavoriteList(favorites); // リストに結果をレンダリング
            } else {
                showError('検索中に問題が発生しました'); // エラーメッセージ表示
            }
        } catch (error) {
            console.error('エラー:', error);
            showError('通信エラーが発生しました'); // 通信エラー時の処理
        }
    }

    // 検索結果をリストに表示する関数
    function renderFavoriteList(favorites) {
        favoriteList.empty(); // リストを空にする
        if (favorites.length === 0) {
            favoriteList.append('<li class="list-group-item text-muted">結果が見つかりません</li>'); // 結果がない場合
        } else {
            // 結果がある場合、リストに追加
            favorites.forEach(favorite => {
                favoriteList.append(
                    `<li class="list-group-item list-group-item-action" role="option" aria-selected="false" data-favorite-id="${favorite.id}">${favorite.name}</li>`
                );
            });
        }
        favoriteList.show(); // リストを表示
    }

    // エラーメッセージを表示する関数
    function showError(message) {
        favoriteError.text(message).show(); // エラーメッセージを設定して表示
    }

    // リストアイテムをクリックした時の処理
    favoriteList.on('click', 'li', function () {
        const selectedName = $(this).text(); // 選択された名前を取得
        const selectedId = $(this).data('favoriteId'); // 選択されたIDを取得
        favoriteSearchInput.val(selectedName); // 名前を入力フィールドにセット
        favoriteIdInput.val(selectedId); // IDを隠しフィールドにセット
        favoriteError.hide(); // エラーメッセージを非表示
        favoriteList.hide().empty(); // リストを非表示にして空にする
    });

    // キーボードの矢印キーやEnterキーによるリスト操作
    favoriteSearchInput.on('keydown', function (event) {
        const items = favoriteList.find('li'); // リストアイテムを取得
        if (items.length === 0) return; // アイテムがない場合は何もしない

        if (event.key === 'ArrowDown') {
            currentIndex = (currentIndex + 1) % items.length; // 下キー：インデックスを更新
            highlightItem(items, currentIndex); // アイテムをハイライト
        } else if (event.key === 'ArrowUp') {
            currentIndex = (currentIndex - 1 + items.length) % items.length; // 上キー：インデックスを更新
            highlightItem(items, currentIndex); // アイテムをハイライト
        } else if (event.key === 'Enter') {
            event.preventDefault(); // Enterキー押下時にデフォルトの送信を防ぐ
            if (currentIndex >= 0) items.eq(currentIndex).click(); // 選択されたアイテムをクリック
        }
    });

    // アイテムをハイライトする関数
    function highlightItem(items, index) {
        items.removeClass('active').attr('aria-selected', 'false'); // すべてのアイテムからアクティブを外す
        items.eq(index).addClass('active').attr('aria-selected', 'true'); // 選択されたアイテムをハイライト
    }

    // 投稿フォームの送信処理
    postForm.on('submit', async function (e) {
        e.preventDefault(); // デフォルトの送信を防ぐ
        if (!favoriteIdInput.val()) {
            showError('推しの名前を選択してください。'); // 推しを選択しないと投稿できない
            return;
        }
        favoriteError.hide(); // エラーメッセージを非表示

        const formData = new FormData(postForm[0]); // フォームデータを作成
        try {
            const response = await fetch('/timeline/store', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken }, // CSRFトークンを送信
            });

            if (response.ok) {
                const { post } = await response.json(); // 投稿データを取得
                addPostToList(post); // 新しい投稿をリストに追加
                postForm[0].reset(); // フォームをリセット
            } else {
                alert('投稿に失敗しました。詳細はコンソールを確認してください。'); // エラー時
            }
        } catch (error) {
            console.error('エラー:', error);
            alert('通信エラーが発生しました。詳細はコンソールを確認してください。'); // 通信エラー時
        }
    });

    // 投稿をリストに追加する関数
    function addPostToList(post) {
        const postItem = `
            <div class="post-item border rounded p-3 mb-3">
                <div class="d-flex align-items-center mb-2">
                    <img src="${post.user.icon_url}" alt="${post.user.name}のアイコン" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                    <strong>${post.user.name}</strong>
                </div>
                <p>${post.post}</p>
                ${post.image ? `<img src="/storage/${post.image}" alt="投稿画像" class="img-fluid mt-2">` : ''}
                <p class="text-muted"><small>${new Date(post.created_at).toLocaleString()}</small></p>
            </div>
        `;
        postList.prepend(postItem); // 新しい投稿を先頭に追加
    }

    // リプライの送信処理
    $(document).on('click', '.send-reply', async function () {
        const postId = $(this).data('post-id'); // ボタンから投稿IDを取得
        const replyForm = $(`#reply-form-${postId}`); // フォームを特定
        const comment = replyForm.find('.reply-comment').val(); // コメント取得
        const imageInput = replyForm.find('.reply-image')[0]?.files[0]; // 画像取得
        const errorDiv = replyForm.find('.reply-error'); // エラーメッセージ表示エリア

        // 入力チェック
        if (!comment) {
            errorDiv.text('返信内容を入力してください。');
            return;
        }
        errorDiv.text(''); // エラーメッセージをクリア

        // フォームデータ作成
        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('comment', comment);
        if (imageInput) formData.append('image', imageInput);

        // サーバーにリクエスト送信
        try {
            const response = await fetch('/replies/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            });

            // レスポンス処理
            if (!response.ok) {
                const errorText = await response.text();
                console.error('エラー内容:', errorText); // HTMLを確認
                errorDiv.text('送信エラー: サーバーの応答が正しくありません。');
                return;
            }

            const data = await response.json();
            if (data.reply) {
                addReplyToList(postId, data.reply); // 成功時にリプライを表示
                replyForm[0].reset(); // フォームをリセット
            } else if (data.errors) {
                errorDiv.text('入力エラー: ' + Object.values(data.errors).flat().join(' '));
            }
        } catch (error) {
            console.error('通信エラー:', error);
            errorDiv.text('通信エラーが発生しました。');
        }
    });

    // リプライをリストに追加する関数
    function addReplyToList(postId, reply) {
        const replyList = $(`#reply-list-${postId}`); // リストを取得
        const newReply = `
            <li>
                <p>${reply.comment}</p>
                ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" />` : ''}
            </li>
        `;
        replyList.append(newReply); // 新しいリプライを追加
    }
});
$(document).on('click', '.reply-toggle', function () {
    const postId = $(this).data('post-id'); // 投稿IDを取得
    $(`#reply-form-${postId}`).toggleClass('d-none'); // 返信フォームの表示/非表示を切り替え
});

$(document).on('click', '.reply-show', function () {
    const postId = $(this).data('post-id'); // 投稿IDを取得
    $(`#reply-list-${postId}`).toggleClass('d-none'); // 返信リストの表示/非表示を切り替え
});
async function loadReplies(postId) {
    try {
        const response = await fetch(`/timeline/replies/${postId}`);
        if (response.ok) {
            const replies = await response.json();
            renderReplies(postId, replies);
        } else {
            console.error('返信の取得に失敗しました');
        }
    } catch (error) {
        console.error('エラー:', error);
    }
}

function renderReplies(postId, replies) {
    const replyList = $(`#reply-list-${postId}`);
    replyList.empty(); // 既存の返信をクリア
    replies.forEach(reply => {
        const replyItem = `
            <div class="reply p-2 border rounded mb-2" id="reply-${reply.id}">
            <div class="d-flex align-items-center">
                <img src="${reply.user ? reply.user.icon_url : '/storage/default-icon.png'}" 
                     alt="${reply.user ? reply.user.name : '匿名ユーザー'}" 
                     class="rounded-circle me-2" 
                     style="width: 30px; height: 30px;">
                <strong>${reply.user ? reply.user.name : '匿名ユーザー'}</strong>
                <small class="text-muted ms-2">${new Date(reply.created_at).toLocaleString()}</small>
            </div>
            <p class="mt-2">${reply.comment}</p>
            ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid">` : ''}
        </div>
    `;
        replyList.append(replyItem);
    });
}

// 新しい投稿が追加された時にその投稿の返信を取得
function addPostToList(post) {
    const postItem = `
        <div class="post-item border rounded p-3 mb-3" id="post-${post.id}">
            <div class="d-flex align-items-center mb-2">
                <img src="${post.user.icon_url}" alt="${post.user.name}のアイコン" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                <strong>${post.user.name}</strong>
            </div>
            <p>${post.post}</p>
            ${post.image ? `<img src="/storage/${post.image}" alt="投稿画像" class="img-fluid mt-2">` : ''}
            <p class="text-muted"><small>${new Date(post.created_at).toLocaleString()}</small></p>

            <div id="reply-list-${post.id}"></div> <!-- 返信リストをここに追加 -->
            <div class="d-flex mt-3">
                <textarea id="reply-comment-${post.id}" class="form-control" placeholder="返信を入力"></textarea>
                <button data-post-id="${post.id}" class="send-reply btn btn-primary ms-2">返信</button>
            </div>
        </div>
    `;
    postList.prepend(postItem); // 新しい投稿を先頭に追加
    loadReplies(post.id);  // 新しい投稿のリプライを取得
}
