$(document).ready(function () {
    const csrfToken = $('meta[name="csrf-token"]').attr('content'); // CSRFトークンの取得

    // 推し検索関連要素
    const favoriteSearchInput = $('#favorite-search');
    const favoriteList = $('#favorite-list');
    const favoriteIdInput = $('#favorite_id');
    const favoriteError = $('#favorite-error');
    let debounceTimeout;

    // モーダル関連要素
    const replyFormModal = $('#reply-form-modal');
    const replyListModal = $('#reply-list-modal');
    const replyListBody = $('#reply-list-body');

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
                <div class="mt-3">
                    <button type="button" class="btn btn-sm btn-outline-primary reply-toggle" data-post-id="${post.id}">返信する</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary reply-show" data-post-id="${post.id}">返信を見る</button>
                </div>
                <form id="reply-form-${post.id}" class="d-none mt-3">
                    <textarea class="form-control reply-comment" rows="2" placeholder="返信を入力"></textarea>
                    <input type="file" class="form-control mt-2 reply-image" accept="image/*">
                    <button type="button" class="btn btn-secondary btn-sm mt-2 send-reply" data-post-id="${post.id}">返信する</button>
                    <div class="reply-error text-danger mt-2" style="display: none;"></div>
                </form>
                <div id="reply-list-${post.id}" class="reply-list mt-3 d-none"></div>
            </div>
        `;
        postList.prepend(postHtml);
    }

    /**
     * 返信フォームの表示・非表示
     */
    $(document).on('click', '.reply-toggle', function () {
        const postId = $(this).data('post-id');
        $(`#reply-form-${postId}`).toggleClass('d-none');
    });

    /**
     * 返信リストの表示・非表示
     */
    $(document).on('click', '.reply-show', async function () {
        const postId = $(this).data('post-id');
        const replyList = $(`#reply-list-${postId}`);

        if (replyList.hasClass('d-none')) {
            try {
                const response = await fetch(`/replies/${postId}`);
                if (response.ok) {
                    const replies = await response.json();
                    renderReplyList(postId, replies);
                    replyList.removeClass('d-none');
                } else {
                    alert('返信の取得に失敗しました。');
                }
            } catch (error) {
                console.error('通信エラー:', error);
                alert('通信エラーが発生しました。');
            }
        } else {
            replyList.addClass('d-none');
        }
    });

    function renderReplyList(postId, replies) {
        const replyList = $(`#reply-list-${postId}`);
        replyList.empty();

        replies.forEach(reply => {
            const replyHtml = `
                <div class="reply p-2 border rounded mb-2">
                    <div class="d-flex align-items-center">
                        <img src="${reply.user.icon_url}" alt="${reply.user.name}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                        <strong>${reply.user.name}</strong>
                        <small class="text-muted ms-2">${new Date(reply.created_at).toLocaleString()}</small>
                    </div>
                    <p class="mt-2">${reply.comment}</p>
                    ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid mt-2">` : ''}
                </div>
            `;
            replyList.append(replyHtml);
        });
    }

    /**
     * 返信フォームの送信処理
     */
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

    function addReplyToList(postId, reply) {
        const replyList = $(`#reply-list-${postId}`);
        const replyHtml = `
            <div class="reply p-2 border rounded mb-2">
                <div class="d-flex align-items-center">
                    <img src="${reply.user.icon_url}" alt="${reply.user.name}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                    <strong>${reply.user.name}</strong>
                    <small class="text-muted ms-2">${new Date(reply.created_at).toLocaleString()}</small>
                </div>
                <p class="mt-2">${reply.comment}</p>
                ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid mt-2">` : ''}
            </div>
        `;
        replyList.append(replyHtml);
    }
});
$(document).ready(function () {
    $('#show-schedules').on('click', function () {
        $.ajax({
            url: '/schedules', // 予定取得のルート
            method: 'GET',
            success: function (data) {
                const scheduleList = $('#schedule-list');
                scheduleList.empty();

                if (data.length === 0) {
                    scheduleList.append('<p>登録された予定がありません。</p>');
                    return;
                }

                data.forEach(schedule => {
                    const scheduleHtml = `
                        <div class="schedule-item border rounded p-3 mb-3">
                            <strong>${schedule.title}</strong>
                            <p>推しの名前: ${schedule.favorite_id}</p>
                            ${schedule.image ? `<img src="/storage/${schedule.image}" alt="${schedule.title}" class="img-fluid mt-2">` : ''}
                        </div>
                    `;
                    scheduleList.append(scheduleHtml);
                });

                // モーダルを表示
                $('#scheduleModal').modal('show');
            },
            error: function () {
                alert('予定の取得に失敗しました。');
            }
        });
    });
});
