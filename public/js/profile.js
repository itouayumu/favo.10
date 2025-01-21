// 公開/非公開切り替え
function toggleVisibility(tagId, hiddenFlag) {
    const maxPublicTags = 3;
    const publicTagsCount = document.getElementById('public-tags').childElementCount;

    if (hiddenFlag === 0 && publicTagsCount >= maxPublicTags) {
        alert('公開できるタグは最大3つまでです。');
        return;
    }

    fetch(`/tags/${tagId}/visibility`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ hidden_flag: hiddenFlag })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tagElement = document.getElementById(`tag-${tagId}`);
            const targetList = hiddenFlag === 0 ? 'public-tags' : 'private-tags';
            document.getElementById(targetList).appendChild(tagElement);

            const button = tagElement.querySelector('button');
            button.textContent = hiddenFlag === 0 ? '非公開にする' : '公開にする';
            button.setAttribute('onclick', `toggleVisibility(${tagId}, ${hiddenFlag === 0 ? 1 : 0})`);
            button.classList.toggle('btn-secondary', hiddenFlag === 0);
            button.classList.toggle('btn-warning', hiddenFlag !== 0);
        } else {
            alert('公開/非公開の切り替えに失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    });
}

// タグ削除
function deleteTag(tagId) {
    fetch(`/tags/${tagId}/delete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`tag-${tagId}`).remove();
        } else {
            alert(data.message || 'タグの削除に失敗しました。');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラーが発生しました。');
    });
}
