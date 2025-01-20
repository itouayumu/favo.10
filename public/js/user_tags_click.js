document.addEventListener('DOMContentLoaded', function () {
    // タグをクリックしたとき
    document.querySelectorAll('.tag-click').forEach(function (tagLink) {
        tagLink.addEventListener('click', function (event) {
            event.preventDefault();  // デフォルトのリンク動作を無効化

            let tagId = this.dataset.tagId;

            // AJAXリクエストでクリック数を増加
            fetch(`/tags/increment/${tagId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // クリック数を更新
                    document.getElementById(`click-count-${tagId}`).innerText = data.click_count;
                } else {
                    alert(data.message);  // エラーメッセージ
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
