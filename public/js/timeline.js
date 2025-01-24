$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // CSRFトークンの取得

    // 推し検索関連要素
    const favoriteSearchInput = $('#favorite-search'); // 推しの名前検索入力
    const favoriteList = $('#favorite-list'); // 推しリスト表示
    const favoriteIdInput = $('#favorite_id'); // 選択された推しIDを保持
    const favoriteError = $('#favorite-error'); // エラーメッセージ表示
    let debounceTimeout;

    // モーダル関連要素
    const replyFormModal = $('#reply-form-modal'); // 返信フォームモーダル
    const replyListModal = $('#reply-list-modal'); // 返信リストモーダル
    const replyListBody = $('#reply-list-body'); // 返信リスト表示領域

    /**
     * 推しの名前検索処理（デバウンス対応）
     */
    favoriteSearchInput.on('input', function () {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => performFavoriteSearch(this.value.trim()), 300);
    });

    async function performFavoriteSearch(query) {
        if (query.length === 0) {
            favoriteList.hide().empty();
            return;
        }

        try {
            const response = await fetch(`/favorites/search?query=${encodeURIComponent(query)}`);
            if (response.ok) {
                const favorites = await response.json();
                renderFavoriteList(favorites);
            } else {
                showError('検索中に問題が発生しました。');
            }
        } catch (error) {
            console.error('通信エラー:', error);
            showError('通信エラーが発生しました。');
        }
    }

    function renderFavoriteList(favorites) {
        favoriteList.empty();
        if (favorites.length === 0) {
            favoriteList.append('<li class="list-group-item text-muted">結果が見つかりません。</li>');
        } else {
            favorites.forEach(favorite => {
                favoriteList.append(
                    `<li class="list-group-item list-group-item-action" data-favorite-id="${favorite.id}">${favorite.name}</li>`
                );
            });
        }
        favoriteList.show();
    }

    function showError(message) {
        favoriteError.text(message).show();
    }

    favoriteList.on('click', 'li', function () {
        const selectedName = $(this).text();
        const selectedId = $(this).data('favorite-id');
        favoriteSearchInput.val(selectedName);
        favoriteIdInput.val(selectedId);
        favoriteError.hide();
        favoriteList.hide().empty();
    });

    /**
     * 投稿フォームの送信処理
     */
    const postForm = $('#postForm');
    postForm.on('submit', async function (e) {
        e.preventDefault();

        if (!favoriteIdInput.val()) {
            showError('推しの名前を選択してください。');
            return;
        }

        const formData = new FormData(postForm[0]);
        try {
            const response = await fetch('/timeline/store', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken },
            });

            if (response.ok) {
                const { post } = await response.json();
                addPostToList(post);
                postForm[0].reset();
            } else {
                alert('投稿に失敗しました。');
            }
        } catch (error) {
            console.error('通信エラー:', error);
            alert('通信エラーが発生しました。');
        }
    });

    function addPostToList(post) {
        const postList = $('#timeline');
        const postHtml = `
            <div class="post border rounded p-3 mb-4" id="post-${post.id}">
                <div class="d-flex align-items-center">
                    <img src="${post.user.icon_url}" alt="${post.user.name}" class="rounded-circle me-2" style="width: 40px;">
                    <strong>${post.user.name}</strong>
                </div>
                <p>${post.post}</p>
                <button type="button" class="btn btn-sm btn-outline-primary reply-toggle" data-post-id="${ post.id }">返信する</button>
                <button type="button" class="btn btn-sm btn-outline-secondary-toggle" data-post-id="${post.id}">返信を見る</button>
            </div>
        `;
        postList.prepend(postHtml);
    }

    /**
     * モーダルの表示・非表示制御
     */
    $(document).on('click', '.reply-toggle', function () {
        const postId = $(this).data('post-id');
        replyFormModal.find('.send-reply').data('post-id', postId);
        replyFormModal.show();
    });

    $(document).on('click', '.reply-show', async function () {
        const postId = $(this).data('post-id');
        try {
            const response = await fetch(`/replies/${postId}`);
            if (response.ok) {
                const replies = await response.json();
                renderReplyList(replies);
                replyListModal.show();
            } else {
                alert('返信の取得に失敗しました。');
            }
        } catch (error) {
            console.error('通信エラー:', error);
            alert('通信エラーが発生しました。');
        }
    });

    $('.close-reply-modal').on('click', () => replyFormModal.hide());
    $('.close-reply-list-modal').on('click', () => replyListModal.hide());

    /**
     * 返信フォームの送信処理
     */
    $(document).on('click', '.send-reply', async function () {
        const postId = $(this).data('post-id');
        const replyForm = replyFormModal.find('.modal-body');
        const comment = replyForm.find('.reply-comment').val();
        const imageInput = replyForm.find('.reply-image')[0]?.files[0];
        const errorDiv = replyForm.find('.reply-error');

        if (!comment) {
            errorDiv.text('返信内容を入力してください。').show();
            return;
        }
        errorDiv.hide();

        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('comment', comment);
        if (imageInput) formData.append('image', imageInput);

        try {
            const response = await fetch('/replies/store', {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': csrfToken },
            });

            if (response.ok) {
                const { reply } = await response.json();
                addReplyToList(postId, reply);
                replyFormModal.hide();
                replyForm[0].reset();
            } else {
                const errorText = await response.text();
                console.error('エラー内容:', errorText);
                errorDiv.text('送信エラー: サーバーの応答が正しくありません。').show();
            }
        } catch (error) {
            console.error('通信エラー:', error);
            errorDiv.text('通信エラーが発生しました。').show();
        }
    });

    function addReplyToList(postId, reply) {
        const replyHtml = `
            <div class="reply p-2 border rounded mb-2">
                <strong>${reply.user.name}</strong>
                <p>${reply.comment}</p>
            </div>
        `;
        replyListBody.append(replyHtml).removeClass('d-none');
    }

    function renderReplyList(replies) {
        replyListBody.empty();
        replies.forEach(reply => {
            replyListBody.append(`
                <div class="reply p-2 border rounded mb-2">
                    <strong>${reply.user.name}</strong>
                    <p>${reply.comment}</p>
                </div>
            `);
        });
    }
});
$(document).ready(function () {
    // 返信フォームの表示・非表示
    $(".reply-toggle").on("click", function () {
        const postId = $(this).data("post-id");
        const replyForm = $(`#reply-form-${postId}`);
        replyForm.toggleClass("d-none");
    });

    // 返信リストの表示・非表示
    $(".reply-show").on("click", function () {
        const postId = $(this).data("post-id");
        const replyList = $(`#reply-list-${postId}`);
        replyList.toggleClass("d-none");
    });
});
$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // CSRFトークンの取得

    // 投稿リストの描画（例として）
    function addPostToList(post) {
        const postList = $('#timeline');
        const postHtml = `
            <div class="post border rounded p-3 mb-4" id="post-${post.id}">
                <div class="d-flex align-items-center">
                    <img src="${post.user.icon_url}" alt="${post.user.name}" class="rounded-circle me-2" style="width: 40px;">
                    <strong>${post.user.name}</strong>
                </div>
                <p>${post.post}</p>
                <button type="button" class="btn btn-link reply-toggle" data-post-id="${post.id}">返信する</button>
                <button type="button" class="btn btn-link reply-show" data-post-id="${post.id}">返信を見る</button>
            </div>
        `;
        postList.prepend(postHtml);
    }

    // モーダルの表示・非表示制御
    $(document).on('click', '.reply-toggle', function () {
        const postId = $(this).data('post-id');
        $('#reply-form-modal').find('.send-reply').data('post-id', postId);
        $('#reply-form-modal').show();
    });

    $(document).on('click', '.reply-show', async function () {
        const postId = $(this).data('post-id');
        try {
            const response = await fetch(`/replies/${postId}`);
            if (response.ok) {
                const replies = await response.json();
                renderReplyList(replies);
                $('#reply-list-modal').show();
            } else {
                alert('返信の取得に失敗しました。');
            }
        } catch (error) {
            console.error('通信エラー:', error);
            alert('通信エラーが発生しました。');
        }
    });

    $('.close-reply-modal').on('click', () => $('#reply-form-modal').hide());
    $('.close-reply-list-modal').on('click', () => $('#reply-list-modal').hide());

    // 返信リストを描画
    function renderReplyList(replies) {
        const replyListBody = $('#reply-list-body');
        replyListBody.empty();
        replies.forEach(reply => {
            replyListBody.append(`
                <div class="reply p-2 border rounded mb-2">
                    <strong>${reply.user.name}</strong>
                    <p>${reply.comment}</p>
                </div>
            `);
        });
    }

    // 返信フォームの送信処理

   $(document).on('click', '.send-reply', async function () {
       const postId = $(this).data('post-id');
       const replyForm = $(`#reply-form-${postId}`);
       const comment = replyForm.find('.reply-comment').val();
       const imageInput = replyForm.find('.reply-image')[0]?.files[0];
       const errorDiv = replyForm.find('.reply-error');

       if (!comment) {
           errorDiv.text('返信内容を入力してください。').show();
           return;
       }
       errorDiv.hide();

       const formData = new FormData();
       formData.append('post_id', postId);
       formData.append('comment', comment);
       if (imageInput) formData.append('image', imageInput);

       try {
           const response = await fetch('/replies/store', {
               method: 'POST',
               body: formData,
               headers: { 'X-CSRF-TOKEN': csrfToken },
           });

           if (response.ok) {
               const { reply } = await response.json();
               addReplyToList(postId, reply);
               replyForm[0].reset();
           } else {
               const errorText = await response.text();
               console.error('エラー内容:', errorText);
               errorDiv.text('送信エラー: サーバーの応答が正しくありません。').show();
           }
       } catch (error) {
           console.error('通信エラー:', error);
           errorDiv.text('通信エラーが発生しました。').show();
       }
   });
    // 返信をリストに追加
    function addReplyToList(postId, reply) {
        const replyList = $(`#reply-list-${postId}`); // 返信リストを特定
        const replyHtml = `
            <div class="reply p-2 border rounded mb-2">
                <div class="d-flex align-items-center">
                    <img src="${reply.user?.icon_url || '/default-icon.png'}" 
                         alt="${reply.user?.name || '匿名ユーザー'}" 
                         class="rounded-circle me-2" 
                         style="width: 30px; height: 30px;">
                    <strong>${reply.user?.name || '匿名ユーザー'}</strong>
                    <small class="text-muted ms-2">${new Date(reply.created_at).toLocaleString()}</small>
                </div>
                <p class="mt-2">${reply.comment}</p>
                ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid mt-2">` : ''}
            </div>
        `;
        replyList.append(replyHtml); // 返信リストに追加
    }
});    