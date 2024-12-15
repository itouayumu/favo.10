$(document).ready(function () {
    fetchRepliesForAllPosts();

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
                location.reload(); // 投稿後にリロード
            },
            error: function (xhr) {
                alert('投稿に失敗しました: ' + xhr.responseJSON.message);
            }
        });
    });

    // 返信フォーム非同期送信
    $(document).on('submit', '.replyForm', function (e) {
        e.preventDefault();

        const form = $(this);
        const formData = new FormData(this);
        const postId = form.data('post-id'); // データ属性からpost_idを取得

        // post_idをFormDataに追加
        formData.append('post_id', postId);

        $.ajax({
            url: '/reply/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                alert(data.message);
                form[0].reset();
                fetchReplies(postId); // 返信を取得
            },
            error: function (xhr) {
                alert('返信の保存に失敗しました: ' + xhr.responseJSON.message);
            }
        });
    });

    // 返信を取得
    function fetchReplies(postId) {
        $.ajax({
            url: `/reply/fetch/${postId}`,
            type: 'GET',
            success: function (replies) {
                const repliesContainer = $(`#replies-${postId}`);
                repliesContainer.empty();

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
                console.error('返信の取得に失敗しました:', xhr.responseText);
            }
        });
    }

    // 全ての投稿に対して返信一覧を取得
    function fetchRepliesForAllPosts() {
        $('.replies').each(function () {
            const postId = $(this).attr('id').split('-')[1];
            fetchReplies(postId);
        });
    }
});
