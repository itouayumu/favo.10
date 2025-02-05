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
   
    function extractUrls(text) {
        const urlPattern = /(https?:\/\/[^\s]+)/g;
        return text.match(urlPattern);
    }
    
    async function fetchOGP(url) {
        try {
            const response = await fetch(`/get-ogp?url=${encodeURIComponent(url)}`);
            if (response.ok) {
                return await response.json(); // OGPデータを返す
            }
        } catch (error) {
            console.error('OGPデータの取得に失敗しました:', error);
        }
        return null;
    }
    // OGPデータを取得する非同期関数
async function fetchOGP(url) {
    try {
        const response = await fetch(`/get-ogp?url=${encodeURIComponent(url)}`);
        if (response.ok) {
            return await response.json(); // OGPデータを返す
        } else {
            console.error('OGPデータの取得に失敗しました:', response.status);
        }
    } catch (error) {
        console.error('OGPデータの取得に失敗しました:', error);
    }
    return null;
}

// 投稿をリストに追加する非同期関数
async function addPostToList(post) {
    const ogpData = await fetchOGP(post.url);  // 非同期でOGPデータを取得
    if (ogpData) {
        // OGPデータを使って投稿をリストに追加する処理
        console.log('OGPデータ:', ogpData);
    } else {
        console.error('OGPデータが取得できませんでした');
    }
}
    
    async function addPostToList(post) {
        const postList = $('#timeline');
        let postContent = post.post;
        let linkPreviewHtml = '';
    
        // 投稿内容内のリンクを抽出
        const urls = extractUrls(postContent);
    
        // リンクが見つかった場合、OGPデータを取得
        if (urls && urls.length > 0) {
            // 非同期処理内でOGPデータを取得
            const ogpData = await fetchOGP(urls[0]); // 最初のURLだけ取得（複数URLにも対応可能）
    
            if (ogpData) {
                linkPreviewHtml = `
                    <div class="link-preview mt-3">
                        <strong>リンクプレビュー</strong>
                        <div class="border p-2">
                            <a href="${ogpData.url}" target="_blank">
                                <h5>${ogpData.title}</h5>
                                <p>${ogpData.description}</p>
                                <img src="${ogpData.image}" alt="Preview image" class="img-fluid">
                            </a>
                        </div>
                    </div>
                `;
            } else {
                console.log('OGPデータが取得できませんでした');
            }
    
            // 投稿内容のURLをリンクに変換
            postContent = postContent.replace(urls[0], `<a href="${urls[0]}" target="_blank">${urls[0]}</a>`);
        }
    
        // 投稿HTMLを生成
        const postHtml = `
            <div class="post border rounded p-3 mb-4" id="post-${post.id}">
                <!-- 投稿者情報 -->
                <div class="d-flex align-items-center">
                    <img src="${post.user.icon_url}" alt="${post.user.name}" class="rounded-circle me-2" style="width: 40px;">
                    <strong>${post.user.name}</strong>
                </div>
    
                <!-- 投稿内容 -->
                <p>${postContent}</p>
                ${linkPreviewHtml}  <!-- リンクプレビューを表示 -->
    
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
    
        postList.prepend(postHtml);  // 新しい投稿をリストに追加
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

            // 返信が完了したことを知らせるメッセージを表示
            const successMessage = $('<div class="alert alert-success mt-2">返信しました。</div>');
            replyForm.append(successMessage);

            // 5秒後にメッセージを非表示にする
            setTimeout(() => {
                successMessage.fadeOut();
            }, 5000);
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
    const postForm = $('#postForm');
    const submitButton = $('#postModal button[type="submit"]');
    const favoriteIdInput = $('#favorite_id');

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
        postForm[0].reset();
        $('#favorite-list').hide();
        favoriteIdInput.val('');
    });

    // 投稿フォームの送信処理
    postForm.on('submit', async function(e) {
        e.preventDefault();

        const postContent = $('#post').val().trim();
        if (!postContent) {
            alert('投稿内容を入力してください');
            return;
        }

        if (!favoriteIdInput.val()) {
            alert('推しの名前を選択してください。');
            return;
        }

        // 投稿ボタンを無効化して送信中の状態にする
        submitButton.prop('disabled', true).text('送信中...');

        const formData = new FormData(postForm[0]);

        try {
            const response = await fetch(postForm.attr('action'), {
                method: 'POST',
                body: formData,
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            });

            if (response.ok) {
                const { post } = await response.json();
                postForm[0].reset();
                $('#postModal').modal('hide'); // 投稿後モーダルを閉じる
            } else {
                alert('投稿に失敗しました。');
            }
        } catch (error) {
            console.error('通信エラー:', error);
            alert('通信エラーが発生しました。');
        } finally {
            submitButton.prop('disabled', false).text('投稿する'); // ボタンを元に戻す
        }
    });
});

    document.addEventListener('DOMContentLoaded', function () {
        const postContents = document.querySelectorAll('.post-content');
    
        postContents.forEach(content => {
            const originalText = content.textContent;
            const linkifiedText = originalText.replace(
                /(https?:\/\/[^\s]+)/g,
                '<a href="$1" class="external-link" data-url="$1" target="_blank" rel="noopener noreferrer">$1</a>'
            );
            content.innerHTML = linkifiedText;
        });
    
        // 確認ページを挟む処理
        const links = document.querySelectorAll('.external-link');
        links.forEach(link => {
            link.addEventListener('click', function (event) {
                event.preventDefault(); // デフォルトのリンク遷移を無効化
                const originalUrl = this.dataset.url; // 元のリンクURL
                const confirmPageUrl = `/confirm?url=${encodeURIComponent(originalUrl)}`; // 確認ページのURLを生成
                window.location.href = confirmPageUrl; // 確認ページにリダイレクト
            });
        });
    });

    document.addEventListener('click', function (event) {
    // クリックされた要素が .external-link かどうかをチェック
    const link = event.target.closest('.external-link');
    if (link) {
        event.preventDefault(); // デフォルトの遷移を防ぐ
        const originalUrl = link.dataset.url; // 元のURLを取得
        const confirmPageUrl = `/confirm?url=${encodeURIComponent(originalUrl)}`; // 確認ページのURLを生成
        window.location.href = confirmPageUrl; // 確認ページにリダイレクト
    }
});

    
    document.addEventListener('DOMContentLoaded', function () {
        // リンクプレビュー対象を取得
        const linkPreviews = document.querySelectorAll('.link-preview');
    
        linkPreviews.forEach(link => {
            const url = link.dataset.url;
    
            fetch(`/fetch-ogp?url=${encodeURIComponent(url)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('OGP取得エラー:', data.error);
                        return;
                    }
    
                    // プレビューHTMLを生成
                    const previewHTML = `
                        <div class="preview-box border rounded d-flex p-2 mt-2">
                            <img src="${data.image || '/path/to/default-image.jpg'}" 
                                 alt="${data.title}" 
                                 class="preview-image me-3" 
                                 style="width: 80px; height: 80px; object-fit: cover;">
                            <div>
                                <strong>${data.title}</strong>
                                <p class="text-muted">${data.description}</p>
                            </div>
                        </div>
                    `;
    
                    // プレビューを挿入
                    const previewContainer = link.closest('.post').querySelector('.ogp-preview-container');
                    previewContainer.insertAdjacentHTML('beforeend', previewHTML);
                })
                .catch(error => {
                    console.error('OGP情報の取得に失敗しました:', error);
                });
        });
    });
    function extractUrls(text) {
        // URL を抽出するための正規表現
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        return text.match(urlRegex) || [];
    }
    
    // OGPデータを取得する非同期関数
    async function fetchOGP(url) {
        try {
            const response = await fetch(`/get-ogp?url=${encodeURIComponent(url)}`);
            if (response.ok) {
                return await response.json(); // OGPデータを返す
            } else {
                console.error('OGPデータの取得に失敗しました:', response.status);
            }
        } catch (error) {
            console.error('OGPデータの取得に失敗しました:', error);
        }
        return null;
    }
    
    // 投稿をリストに追加する非同期関数
    async function addPostToList(post) {
        const postList = $('#timeline');
        let postContent = post.post;
        let linkPreviewHtml = '';
    
        // 投稿内容内のリンクを抽出
        const urls = extractUrls(postContent);
    
        // リンクが見つかった場合、OGPデータを取得
        if (urls && urls.length > 0) {
            // 非同期処理内でOGPデータを取得
            const ogpData = await fetchOGP(urls[0]); // 最初のURLだけ取得（複数URLにも対応可能）
    
            if (ogpData) {
                linkPreviewHtml = `
                    <div class="link-preview mt-3">
                        <strong>リンクプレビュー</strong>
                        <div class="border p-2">
                            <a href="${ogpData.url}" target="_blank">
                                <h5>${ogpData.title}</h5>
                                <p>${ogpData.description}</p>
                                <img src="${ogpData.image}" alt="Preview image" class="img-fluid">
                            </a>
                        </div>
                    </div>
                `;
            } else {
                console.log('OGPデータが取得できませんでした');
            }
    
            // 投稿内容のURLをリンクに変換
            postContent = postContent.replace(urls[0], `<a href="${urls[0]}" class="external-link" data-url="${urls[0]}" target="_blank">${urls[0]}</a>`);

        }
    
        // 投稿HTMLを生成
        const postHtml = `
            <div class="post border rounded p-3 mb-4" id="post-${post.id}">
                <!-- 投稿者情報 -->
                <div class="d-flex align-items-center">
                    <img src="${post.user.icon_url}" alt="${post.user.name}" class="rounded-circle me-2" style="width: 40px;">
                    <strong>${post.user.name}</strong>
                </div>
    
                <!-- 投稿内容 -->
                <p>${postContent}</p>
                ${linkPreviewHtml}  <!-- リンクプレビューを表示 -->
    
                <!-- 投稿画像 -->
                ${post.image ? `<img src="storage/${post.image}" alt="投稿画像" class="img-fluid mt-2">` : ''}
    
                <!-- スケジュール情報 -->
                ${post.schedule ? `
                    <div class="schedule-info mt-3 border rounded p-3">
                        <h5 class="mb-2">予定情報</h5>
                        <div class="d-flex align-items-center">
                            <strong>${post.schedule.favorite_name || '未設定'}</strong>
                        </div>
                        <div class="mt-2">
                            <strong>タイトル:</strong> ${post.schedule.title || 'タイトルなし'}<br>
                            <strong>内容:</strong> ${post.schedule.content || '内容なし'}<br>
                            <strong>開始日時:</strong> ${post.schedule.start_date || '未設定'} ${post.schedule.start_time || ''}<br>
                            ${post.schedule.url ? `<a href="${post.schedule.url}" target="_blank">リンクはこちら</a>` : ''}
                        </div>
                        ${post.schedule.image ? `<img src="${post.schedule.image}" alt="スケジュール画像" class="img-fluid mt-2">` : ''}
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
    
        postList.prepend(postHtml);  // 新しい投稿をリストに追加
    }
    
    $(document).ready(function () {
        // 定期的に新しい投稿をチェック（例：5秒ごとに確認）
        setInterval(async function () {
            try {
                const response = await fetch('/timeline/new-posts');  // 新規投稿を取得するAPIエンドポイント
                if (response.ok) {
                    const newPosts = await response.json();
                    if (newPosts.length > 0) {
                        newPosts.forEach(post => {
                            addPostToList(post);  // 新規投稿をリストに追加
                        });
                    }
                }
            } catch (error) {
                console.error('新規投稿の取得に失敗しました:', error);
            }
        }, 5000);  // 5秒ごとに新規投稿を確認
    });
    
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById("searchInput");
    const searchResults = document.getElementById("searchResults");

    searchInput.addEventListener("input", function() {
        let query = searchInput.value.trim();

        if (query.length === 0) {
            searchResults.innerHTML = ""; // 空ならリセット
            return;
        }

        fetch(`/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(posts => {
                searchResults.innerHTML = ""; // 結果をクリア
                
                if (posts.length === 0) {
                    searchResults.innerHTML = "<p>該当する投稿がありません。</p>";
                    return;
                }

                posts.forEach(post => {
                    let postContent = post.post ? post.post : '内容なし';
                    let linkPreviewHtml = post.link_preview ? `<div class="link-preview">${post.link_preview}</div>` : '';

                    let postElement = document.createElement("div");
                    postElement.innerHTML = `
                        <div class="post border rounded p-3 mb-4" id="post-${post.id}">
                            <!-- 投稿者情報 -->
                            <div class="d-flex align-items-center">
                                <img src="${post.user.icon_url}" alt="${post.user.name}" class="rounded-circle me-2" style="width: 40px;">
                                <strong>${post.user.name}</strong>
                            </div>

                            <!-- 投稿内容 -->
                            <p>${postContent}</p>
                            ${linkPreviewHtml}

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

                    searchResults.appendChild(postElement);
                });
            })
            .catch(error => console.error("検索エラー:", error));
    });
});