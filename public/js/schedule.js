// 入力フィールドへのイベントリスナーを追加
let debounceTimer;
document.getElementById('favorite-search').addEventListener('input', function () {
    const query = this.value.trim(); // 空白を削除
    const favoriteList = document.getElementById('favorite-list');

    // 入力が2文字未満ならリストを非表示
    if (query.length < 2) {
        favoriteList.style.display = 'none';
        favoriteList.innerHTML = ''; // 前回のリストをクリア
        return;
    }

    // デバウンス処理 (最後の入力から300ms待つ)
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        fetch(`/favorites/search?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                favoriteList.innerHTML = ''; // 前回のリストをクリア

                if (data.length > 0) {
                    favoriteList.style.display = 'block'; // リストを表示
                    data.forEach(favorite => {
                        const listItem = document.createElement('li');
                        listItem.textContent = favorite.name;
                        listItem.style.cursor = 'pointer';
                        listItem.style.padding = '5px';
                        listItem.onclick = () => selectFavorite(favorite.id, favorite.name);
                        favoriteList.appendChild(listItem);
                    });
                } else {
                    // 該当するものがない場合
                    const noResult = document.createElement('li');
                    noResult.textContent = 'あてはまるものがありません';
                    noResult.style.color = 'red';
                    noResult.style.padding = '5px';
                    favoriteList.appendChild(noResult);
                    favoriteList.style.display = 'block'; // メッセージも表示
                }
            })
            .catch(error => {
                console.error('検索エラー:', error);
                favoriteList.style.display = 'none';
            });
    }, 300); // 300msの遅延を追加
});

function selectFavorite(id, name) {
    document.getElementById('oshiname').value = id; // IDを隠しフィールドにセット
    document.getElementById('favorite-search').value = name; // 名前を入力欄に表示
    document.getElementById('favorite-list').style.display = 'none'; // リストを非表示
}

document.getElementById('thumbnail').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});