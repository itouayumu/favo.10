document.querySelectorAll('.tag-link').forEach(function(tagLink) {
    tagLink.addEventListener('click', function(e) {
        e.preventDefault();
        const favoriteId = e.target.dataset.favoriteId;
        const tagId = e.target.dataset.tagId;

        // AJAXリクエストでカウントをインクリメント
        fetch(`/oshiTag/increment/${favoriteId}/${tagId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // カウントを更新
                e.target.querySelector('.tag-count').textContent = data.newCount;
            }
        });
    });
});