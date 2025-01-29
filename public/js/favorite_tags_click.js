// タグのクリックイベント処理
document.querySelectorAll('.tag-click').forEach(tagElement => {
    tagElement.addEventListener('click', function(event) {
        event.preventDefault();  // デフォルトのリンク動作を無効化

        let tagId = this.dataset.tagId;
        let favoriteId = this.dataset.favoriteId;

        // AJAXリクエストでクリック数を増加
        fetch(`/oshiTag/increment/${favoriteId}/${tagId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // 新しいカウントをDOMに反映
                let countElement = document.querySelector(`#click-count-${tagId}`);
                if (countElement) {
                    countElement.textContent = data.newCount; // 新しいカウントを表示
                }
            } else {
                console.error('タグのカウントの増加に失敗しました');
            }
        })
        .catch(error => {
            console.error('エラー:', error);
        });
    });
});
