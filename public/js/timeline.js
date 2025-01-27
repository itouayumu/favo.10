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
        <!-- 投稿者情報 -->
        <div class="d-flex align-items-center">
            <img src="${post.user.icon_url}" alt="${post.user.name}" class="rounded-circle me-2" style="width: 40px;">
            <strong>${post.user.name}</strong>
        </div>
        
        <!-- 投稿内容 -->
        <p>${post.post}</p>
        
        <!-- 投稿画像 -->
        ${post.image ? `<img src="storage/${post.image}" alt="投稿画像" class="img-fluid mt-2">` : ''}

        <!-- スケジュール情報 -->
        ${post.schedule ? `
            <div class="schedule-info mt-3 border rounded p-3">
                <h5 class="mb-2">予定情報</h5>
                <div class="d-flex align-items-center">
                    ${post.schedule.favorite && post.schedule.favorite.icon_url ? `
                        <img src="${post.schedule.favorite.icon_url}" alt="${post.schedule.favorite.name}" class="rounded-circle me-2" style="width: 30px;">
                    ` : ''}
                    <strong>${post.favorite ? post.favorite.name : '未設定'}</strong>
                </div>
                <div class="mt-2">
                    <strong>タイトル:</strong> ${post.schedule.title || 'タイトルなし'}<br>
                    <strong>内容:</strong> ${post.schedule.content || '内容なし'}<br>
                    <strong>開始日時:</strong> ${post.schedule.start_date || '未設定'} ${post.schedule.start_time || ''}<br>
                    <strong>終了日時:</strong> ${post.schedule.end_date || '未設定'} ${post.schedule.end_time || ''}<br>
                    ${post.schedule.url ? `<a href="${post.schedule.url}" target="_blank">リンクはこちら</a>` : ''}
                </div>
                ${post.schedule.image ? `<img src="storage/${post.schedule.image}" alt="スケジュール画像" class="img-fluid mt-2">` : ''}
            </div>
        ` : ''}
        
        <!-- ボタン -->
        <div class="mt-3">
            <button type="button" class="btn btn-sm btn-outline-primary reply-toggle" data-post-id="${post.id}">返信する</button>
            <button type="button" class="btn btn-sm btn-outline-secondary reply-show" data-post-id="${post.id}">返信を見る</button>
        </div>
        
        <!-- 返信フォーム -->
        <form id="reply-form-${post.id}" class="d-none mt-3">
            <textarea class="form-control reply-comment" rows="2" placeholder="返信を入力"></textarea>
            <input type="file" class="form-control mt-2 reply-image" accept="image/*">
            <button type="button" class="btn btn-secondary btn-sm mt-2 send-reply" data-post-id="${post.id}">返信する</button>
            <div class="reply-error text-danger mt-2" style="display: none;"></div>
        </form>
        
        <!-- 返信リスト -->
        <div id="reply-list-${post.id}" class="reply-list mt-3 d-none"></div>
    </div>
`;

        postList.prepend(postHtml);
              // 10秒ごとに投稿を取得（ミリ秒単位で指定）
      setInterval(addPostToList, 1000);
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
    // モーダル表示時に予定を取得
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
                            <button type="button" class="btn btn-sm btn-primary mt-2 share-schedule" data-schedule-id="${schedule.id}">
                                この予定を共有する
                            </button>
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

    // 「この予定を共有する」ボタンのクリック処理
    $(document).on('click', '.share-schedule', function () {
        const scheduleId = $(this).data('schedule-id');
        $('#schedule_id').val(scheduleId); // hidden input にセット
        $('#scheduleModal').modal('hide'); // モーダルを閉じる
    });
});
document.querySelectorAll('.register-schedule').forEach(button => {
    button.addEventListener('click', async () => {
        const scheduleId = button.dataset.scheduleId;

        try {
            const response = await fetch('/register-schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ schedule_id: scheduleId }),
            });

            if (!response.ok) {
                throw new Error('スケジュール登録に失敗しました。');
            }

            const data = await response.json();
            alert(data.message);

            // 登録完了後の処理（例: ボタンを無効化）
            button.disabled = true;
            button.textContent = '登録済み';
        } catch (error) {
            console.error(error);
            alert('エラーが発生しました。もう一度お試しください。');
        }
    });
});
$(document).ready(function() {
    // 右下のボタンがクリックされた時にモーダルを開く
    $('.btn-floating').click(function() {
        $('#postModal').modal('show');
    });

    // モーダル外をクリックした場合もモーダルを閉じる
    $('#postModal').on('click', function(e) {
        if ($(e.target).hasClass('modal')) {
            $('#postModal').modal('hide');
        }
    });

    // モーダルが閉じられた時に、フォームをリセット
    $('#postModal').on('hidden.bs.modal', function() {
        $('#postForm')[0].reset();
        $('#favorite-list').hide();
        $('#favorite_id').val('');
    });

    // 投稿するボタンがクリックされたときの処理
    $('#postForm').submit(function(e) {
        e.preventDefault(); // フォームのデフォルトの送信をキャンセル

        // ここで必要に応じて入力内容を確認
        const postContent = $('#post').val();
        if (!postContent.trim()) {
            alert('投稿内容を入力してください');
            return;
        }

        // 投稿するボタンを無効化して、送信中であることを示す
        $('#postModal button[type="submit"]').prop('disabled', true);
        $('#postModal button[type="submit"]').text('送信中...');


        });
    });

      

      
