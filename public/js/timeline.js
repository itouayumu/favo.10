$(document).ready(function () {
    let lastFetchedPost = new Date().toISOString(); // 最後に取得した投稿時刻を初期化
    let lastFetchedReply = new Date().toISOString(); // 最後に取得した返信時刻を初期化
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

    // 投稿フォーム非同期送信
    $('#postForm').on('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(this);

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                alert(data.message);
                $('#postForm')[0].reset();
                fetchNewPosts(); // 投稿後すぐに新しい投稿を取得
            },
            error: function (xhr) {
                alert('投稿に失敗しました: ' + xhr.responseJSON.message);
            }
        });
    });

    // 新しい投稿を取得
    function fetchNewPosts() {
        $.ajax({
            url: '/timeline/fetch-timeline',
            type: 'GET',
            data: { last_fetched: lastFetchedPost },
            success: function (posts) {
                console.log('新しい投稿:', posts);
                if (posts.length > 0) {
                    posts.forEach(post => {
                        if ($('#post-' + post.id).length === 0) { // 重複防止
                            const postElement = `
                                <div class="post mb-4 p-3 border rounded" id="post-${post.id}">
                                                                    <!-- 投稿者のアイコンと名前 -->
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="${post.user.icon_url}" alt="投稿者のアイコン" 
                                             class="rounded-circle me-2" 
                                             style="width: 40px; height: 40px;">
                                        <strong>${post.user.name}</strong>
                                    </div>
                                    <p>${$('<div>').text(post.post).html()}</p>
                                    <p class="text-muted">
                                        <small>${new Date(post.created_at).toLocaleString()}</small>
                                    </p>
                                    ${post.image ? `<img src="/storage/${post.image}" class="img-fluid mb-2" alt="投稿画像">` : ''}

                                    <!-- 返信フォーム -->
                                    <form class="replyForm" data-post-id="${post.id}">
                                        <input type="hidden" name="post_id" value="${post.id}">
                                        <div class="mb-2">
                                            <textarea name="comment" class="form-control" placeholder="返信を書く" required></textarea>
                                        </div>
                                        <div class="mb-2">
                                            <input type="file" name="image" accept="image/*" class="form-control">
                                        </div>
                                        <button type="submit" class="btn btn-sm btn-secondary">返信する</button>
                                    </form>

                                    <!-- 返信一覧 -->
                                    <div class="replies mt-3" id="replies-${post.id}"></div>
                                </div>
                            `;
                            $('#timeline').prepend(postElement);

                            // 返信を取得して表示
                            fetchReplies(post.id);
                        }
                    });
                    lastFetchedPost = new Date().toISOString(); // 取得時刻を更新
                }
            },
            error: function (xhr) {
                console.error('新しい投稿の取得に失敗:', xhr.responseText);
            }
        });
    }

    // 返信を取得
    function fetchReplies(postId) {
        $.ajax({
            url: `/reply/fetch/${postId}`,
            type: 'GET',
            success: function (replies) {
                const repliesContainer = $(`#replies-${postId}`);
                repliesContainer.empty(); // 既存の返信をクリア

                replies.forEach(reply => {
                    const replyElement = `
                        <div class="reply mb-2 p-2 border rounded">
                            <p>${reply.comment}</p>
                            <small class="text-muted">${new Date(reply.created_at).toLocaleString()}</small>
                            ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid">` : ''}
                        </div>
                    `;
                    repliesContainer.append(replyElement);
                });
            },
            error: function (xhr) {
                console.error('返信の取得に失敗:', xhr.responseText);
            }
        });
    }

    // 新規返信を取得
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

                        const replyElement = `
                            <div class="reply mb-2 p-2 border rounded">
                                <p>${reply.comment}</p>
                                <small class="text-muted">${new Date(reply.created_at).toLocaleString()}</small>
                                ${reply.image ? `<img src="/storage/${reply.image}" alt="返信画像" class="img-fluid">` : ''}
                            </div>
                        `;
                        repliesContainer.append(replyElement);
                    });
                    lastFetchedReply = new Date().toISOString(); // 取得時刻を更新
                }
            },
            error: function (xhr) {
                console.error('新しい返信の取得に失敗:', xhr.responseText);
            }
        });
    }

    // 3秒ごとに新しい投稿をチェック
    setInterval(fetchNewPosts, 3000);
    // 3秒ごとに新しい返信をチェック
    setInterval(fetchNewReplies, 3000);

    // 返信フォームの非同期送信
    $(document).on('submit', '.replyForm', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const postId = form.data('post-id'); // データ属性からpost_idを取得

        formData.append('post_id', postId);
        const token = $('meta[name="csrf-token"]').attr('content'); // CSRFトークンをmetaタグから取得

        $.ajax({
            url: '/reply/store', // 返信保存のルート
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': token // ヘッダーにCSRFトークンを追加
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
});
$(document).ready(function () {
    let searchTimeout;

    // 🔍 検索機能
    $('#searchInput').on('input', function () {
        clearTimeout(searchTimeout); // 入力のたびにタイマーをクリア
        const query = $(this).val();

        if (query.length > 0) {
            searchTimeout = setTimeout(() => {
                searchPosts(query);
            }, 500); // 0.5秒後に検索を実行
        } else {
            $('#searchResults').empty(); // フォームが空なら検索結果をクリア
        }
    });

    function searchPosts(query) {
        $.ajax({
            url: '/posts/search',
            type: 'GET',
            data: { query: query },
            success: function (posts) {
                $('#searchResults').empty(); // 結果をクリア

                if (posts.length > 0) {
                    posts.forEach(post => {
                        const postElement = `
                            <div class="post mb-4 p-3 border rounded" id="post-${post.id}">
                                <p>${$('<div>').text(post.post).html()}</p>
                                <p class="text-muted">
                                    <small>${new Date(post.created_at).toLocaleString()}</small>
                                </p>
                                ${post.image ? `<img src="/storage/${post.image}" class="img-fluid mb-2" alt="投稿画像">` : ''}

                                <!-- 返信フォーム -->
                                <form class="replyForm" data-post-id="${post.id}">
                                    <input type="hidden" name="post_id" value="${post.id}">
                                    <div class="mb-2">
                                        <textarea name="comment" class="form-control" placeholder="返信を書く" required></textarea>
                                    </div>
                                    <div class="mb-2">
                                        <input type="file" name="image" accept="image/*" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-sm btn-secondary">返信する</button>
                                </form>

                                <!-- 返信一覧 -->
                                <div class="replies mt-3" id="replies-${post.id}"></div>
                            </div>
                        `;
                        $('#searchResults').append(postElement);
                    });
                } else {
                    $('#searchResults').html('<p>該当する投稿はありません。</p>');
                }
            },
            error: function (xhr) {
                console.error('検索に失敗:', xhr.responseText);
            }
        });
    }
});
