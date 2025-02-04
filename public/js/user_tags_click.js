document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.tag-click').forEach(function (tagLink) {
        tagLink.addEventListener('click', function (event) {
            event.preventDefault();  // デフォルトのリンク動作を無効化

            let tagId = this.dataset.tagId;
            let userId = this.dataset.userId; // ユーザーIDも取得

            // AJAXリクエストでクリック数を増加
            fetch(`/tags/increment/${tagId}/${userId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`click-count-${tagId}`).innerText = data.click_count;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
});
