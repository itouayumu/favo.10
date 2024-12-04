<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>マウス & タッチで移動可能な要素（重なり検出）</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #container {
            position: relative;
            width: 600px;
            height: 400px;
            border: 2px solid #333;
        }
        #movable {
            position: absolute;
            width: 100px;
            height: 100px;
            background-color: #FF0000;
            cursor: grab;
        }
        #movable:active {
            cursor: grabbing;
        }
        #target {
            position: absolute;
            width: 150px;
            height: 150px;
            top: 50px;
            left: 200px;
            background-color: #00FF00;
            border: 2px solid #006400;
        }
    </style>
</head>
<body>
    <div id="container">
        <div id="movable"></div>
        <div id="target"></div>
    </div>

    <script>
        const movable = document.getElementById("movable");
        const container = document.getElementById("container");
        const target = document.getElementById("target");
        let isDragging = false;
        let offsetX = 0;
        let offsetY = 0;

        // 汎用関数: イベントから座標を取得
        function getEventCoordinates(event) {
            if (event.touches && event.touches.length > 0) {
                return { x: event.touches[0].clientX, y: event.touches[0].clientY };
            }
            return { x: event.clientX, y: event.clientY };
        }

        // ドラッグ開始時の処理
        function startDrag(event) {
            event.preventDefault();
            const { x, y } = getEventCoordinates(event);
            const rect = movable.getBoundingClientRect();
            offsetX = x - rect.left;
            offsetY = y - rect.top;
            isDragging = true;
        }

        // ドラッグ中の処理
        function drag(event) {
            if (!isDragging) return;
            const { x, y } = getEventCoordinates(event);
            const containerRect = container.getBoundingClientRect();

            // 新しい位置を計算
            let newX = x - offsetX - containerRect.left;
            let newY = y - offsetY - containerRect.top;

            // コンテナ内に制限
            newX = Math.max(0, Math.min(newX, containerRect.width - movable.offsetWidth));
            newY = Math.max(0, Math.min(newY, containerRect.height - movable.offsetHeight));

            // 要素を移動
            movable.style.left = `${newX}px`;
            movable.style.top = `${newY}px`;

            // 重なりの検出
            checkCollision();
        }

        // ドラッグ終了時の処理
        function endDrag() {
            isDragging = false;
        }

        // 重なり検出処理
        function checkCollision() {
            const movableRect = movable.getBoundingClientRect();
            const targetRect = target.getBoundingClientRect();

            // 移動中の要素とターゲット要素の位置を比較
            if (
                movableRect.left < targetRect.right &&
                movableRect.right > targetRect.left &&
                movableRect.top < targetRect.bottom &&
                movableRect.bottom > targetRect.top
            ) {
                console.log("重なっています！");
                target.style.backgroundColor = "#FFD700"; // 重なったらターゲットを黄色に
            } else {
                console.log("重なっていません！");
                target.style.backgroundColor = "#00FF00"; // 重なっていなければ元の色
            }
        }

        // マウスイベントの登録
        movable.addEventListener("mousedown", startDrag);
        window.addEventListener("mousemove", drag);
        window.addEventListener("mouseup", endDrag);

        // タッチイベントの登録
        movable.addEventListener("touchstart", startDrag, { passive: false });
        window.addEventListener("touchmove", drag, { passive: false });
        window.addEventListener("touchend", endDrag);
    </script>
</body>
</html>