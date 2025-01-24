$(document).ready(function () {
    const favoriteSearchInput = document.getElementById('favorite-search');
    const favoriteList = document.getElementById('favorite-list');
    const favoriteIdInput = document.getElementById('favorite_id');
    const favoriteError = document.getElementById('favorite-error');
    let currentIndex = -1;

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

                // 候補リストをクリア
                favoriteList.innerHTML = '';

                // 候補をリストに追加
                if (favorites.length === 0) {
                    const noResultItem = document.createElement('li');
                    noResultItem.textContent = '結果が見つかりません';
                    noResultItem.classList.add('list-group-item', 'text-muted');
                    favoriteList.appendChild(noResultItem);
                } else {
                    favorites.forEach(favorite => {
                        const listItem = document.createElement('li');
                        listItem.textContent = favorite.name;
                        listItem.dataset.favoriteId = favorite.id;
                        listItem.classList.add('list-group-item', 'list-group-item-action');
                        favoriteList.appendChild(listItem);
                    });
                }

                favoriteList.style.display = 'block';
            } else {
                console.error('推し検索に失敗しました');
            }
        } catch (error) {
            console.error('エラー:', error);
        }
    });

    // 候補リストから選択
    favoriteList.addEventListener('click', function (event) {
        if (event.target.tagName === 'LI') {
            const selectedName = event.target.textContent;
            const selectedId = event.target.dataset.favoriteId;

            favoriteSearchInput.value = selectedName;
            favoriteIdInput.value = selectedId;

            // エラーを非表示
            favoriteError.style.display = 'none';
            favoriteError.textContent = ''; // エラーメッセージをクリア

            // リストを隠す
            favoriteList.style.display = 'none';
            favoriteList.innerHTML = '';
        }
    });

    // キーボード操作による候補選択
    favoriteSearchInput.addEventListener('keydown', function (event) {
        const items = favoriteList.querySelectorAll('li');
        if (items.length === 0) return;

        if (event.key === 'ArrowDown') {
            currentIndex = (currentIndex + 1) % items.length;
            highlightItem(items, currentIndex);
        } else if (event.key === 'ArrowUp') {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            highlightItem(items, currentIndex);
        } else if (event.key === 'Enter') {
            event.preventDefault(); // デフォルト動作を防止
            if (currentIndex >= 0) {
                items[currentIndex].click();
            }
        }
    });

    function highlightItem(items, index) {
        items.forEach((item, i) => {
            item.classList.toggle('active', i === index);
        });
    }

    // フォーム送信前の検証
    $('#postForm').on('submit', function (e) {
        if (!favoriteIdInput.value) {
            e.preventDefault();
            favoriteError.textContent = '推しの名前を選択してください。';
            favoriteError.style.display = 'block';
        } else {
            favoriteError.style.display = 'none';
        }
    });

    // 検索ボックス外をクリックしたら候補リストを隠す
    document.addEventListener('click', (event) => {
        if (!favoriteSearchInput.contains(event.target) && !favoriteList.contains(event.target)) {
            favoriteList.style.display = 'none';
        }
    });
});
