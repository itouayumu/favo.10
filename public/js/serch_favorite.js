
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('favorite-search');
    const favoriteList = document.getElementById('favorite-list');
    const oshinameInput = document.getElementById('oshiname');  // 隠しフィールドのID
    const favoriteError = document.getElementById('favorite-error');
    let currentIndex = -1;

    // 推しの名前検索処理
    searchInput.addEventListener('input', function() {
        const query = searchInput.value.trim();

        if (query.length > 0) {
            fetch(`/search-favorite?query=${query}`)
                .then(response => response.json())
                .then(data => {
                    favoriteList.innerHTML = ''; // 前の検索結果をクリア
                    if (data.length > 0) {
                        favoriteList.style.display = 'block';
                        data.forEach(favorite => {
                            const listItem = document.createElement('li');
                            listItem.textContent = favorite.name;
                            listItem.dataset.id = favorite.id; // 推しのIDをデータ属性に設定

                            // リストアイテムクリック処理
                            listItem.addEventListener('click', function() {
                                searchInput.value = favorite.name;
                                oshinameInput.value = favorite.id; // 隠しフィールドに選択したIDをセット
                                favoriteList.style.display = 'none'; // リストを非表示に
                                favoriteError.style.display = 'none'; // エラーメッセージを非表示に
                                favoriteError.textContent = ''; // エラーメッセージをクリア
                            });

                            favoriteList.appendChild(listItem);
                        });
                    } else {
                        favoriteList.style.display = 'none'; // 結果がなければリストを非表示に
                    }
                })
                .catch(error => console.error('検索中にエラーが発生しました:', error));
        } else {
            favoriteList.style.display = 'none'; // 入力がない場合リストを非表示
        }
    });

    // リスト外をクリックしたときに候補リストを隠す
    document.addEventListener('click', function(event) {
        if (!favoriteList.contains(event.target) && event.target !== searchInput) {
            favoriteList.style.display = 'none';
        }
    });

    // キーボードでリストを操作するための処理
    searchInput.addEventListener('keydown', function(event) {
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
    document.getElementById('postForm').addEventListener('submit', function(e) {
        if (!oshinameInput.value) {
            e.preventDefault();
            favoriteError.textContent = '推しの名前を選択してください。';
            favoriteError.style.display = 'block';
        } else {
            favoriteError.style.display = 'none';
        }
    });
});
