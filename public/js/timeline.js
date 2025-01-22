$(document).ready(function () {
    const postForm = $('#postForm');
    const postList = $('#post-list');
    const favoriteSearchInput = $('#favorite-search');
    const favoriteList = $('#favorite-list');
    const favoriteIdInput = $('#favorite_id');
    const favoriteError = $('#favorite-error');
    let currentIndex = -1;
    let debounceTimeout;

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

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
                showError('検索中に問題が発生しました');
            }
        } catch (error) {
            console.error('エラー:', error);
            showError('通信エラーが発生しました');
        }
    }

    function renderFavoriteList(favorites) {
        favoriteList.empty();
        if (favorites.length === 0) {
            favoriteList.append('<li class="list-group-item text-muted">結果が見つかりません</li>');
        } else {
            favorites.forEach(favorite => {
                favoriteList.append(
                    `<li class="list-group-item list-group-item-action" role="option" aria-selected="false" data-favorite-id="${favorite.id}">${favorite.name}</li>`
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
        const selectedId = $(this).data('favoriteId');
        favoriteSearchInput.val(selectedName);
        favoriteIdInput.val(selectedId);
        favoriteError.hide();
        favoriteList.hide().empty();
    });

    favoriteSearchInput.on('keydown', function (event) {
        const items = favoriteList.find('li');
        if (items.length === 0) return;

        if (event.key === 'ArrowDown') {
            currentIndex = (currentIndex + 1) % items.length;
            highlightItem(items, currentIndex);
        } else if (event.key === 'ArrowUp') {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            highlightItem(items, currentIndex);
        } else if (event.key === 'Enter') {
            event.preventDefault();
            if (currentIndex >= 0) items.eq(currentIndex).click();
        }
    });

    function highlightItem(items, index) {
        items.removeClass('active').attr('aria-selected', 'false');
        items.eq(index).addClass('active').attr('aria-selected', 'true');
    }

    postForm.on('submit', async function (e) {
        e.preventDefault();
        if (!favoriteIdInput.val()) {
            showError('推しの名前を選択してください。');
            return;
        }
        favoriteError.hide();

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
                alert('投稿に失敗しました。詳細はコンソールを確認してください。');
            }
        } catch (error) {
            console.error('エラー:', error);
            alert('通信エラーが発生しました。詳細はコンソールを確認してください。');
        }
    });

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
        postList.prepend(postItem);
    }

    $(document).on('click', '.reply-toggle', function () {
        const postId = $(this).data('post-id');
        $(`#reply-form-${postId}`).toggleClass('d-none');
    });

    $(document).on('click', '.reply-show', function () {
        const postId = $(this).data('post-id');
        $(`#reply-list-${postId}`).toggleClass('d-none');
    });

    $(document).on('click', '.send-reply', async function () {
        const postId = $(this).data('post-id');
        const replyForm = $(`#reply-form-${postId}`);
        const comment = replyForm.find('.reply-comment').val();
        const imageInput = replyForm.find('.reply-image')[0]?.files[0];
        const errorDiv = replyForm.find('.reply-error');

        if (!comment) {
            errorDiv.text('返信内容を入力してください。');
            return;
        }
        errorDiv.text('');

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
                const data = await response.json();
                if (data.reply) {
                    addReplyToList(postId, data.reply);
                    replyForm[0].reset();
                }
            } else {
                const errorText = await response.text();
                errorDiv.text(`返信の送信に失敗しました: ${errorText}`);
            }
        } catch (error) {
            console.error('エラー:', error);
            errorDiv.text('通信エラーが発生しました。');
        }
    });

    function addReplyToList(postId, reply) {
        const replyList = $(`#reply-list-${postId}`);
        const replyItem = `
            <div class="reply-item border rounded p-2 mb-2">
                <div class="d-flex align-items-center">
                    <img src="${reply.user.icon_url}" alt="${reply.user.name}" class="rounded-circle me-2" style="width: 30px; height: 30px;">
                    <strong>${reply.user.name}</strong>
                    <small class="text-muted ms-2">${new Date(reply.created_at).toLocaleString()}</small>
                </div>
                <p class="mt-2">${reply.comment}</p>
                ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid mt-2">` : ''}
            </div>
        `;
        replyList.append(replyItem).removeClass('d-none');
    }

    $(document).on('click', function (event) {
        if (
            !favoriteSearchInput.is(event.target) &&
            !favoriteList.is(event.target) &&
            !favoriteList.has(event.target).length
        ) {
            favoriteList.hide();
        }
    });
});
