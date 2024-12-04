


    <link rel="stylesheet" href="{{ asset('css/callender.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <a href="/users/profile_edit"><img src="" alt="アイコン"></a>
    <h1>スケジュール管理</h1>

    <!-- 月の範囲表示 -->
    <p id="month-info"></p>

    <!-- カレンダー表示 -->
    <div>
        <button onclick="changeMonth(-1)">前月</button>
        <span id="current-month"></span>
        <button onclick="changeMonth(1)">次月</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>日</th>
                <th>月</th>
                <th>火</th>
                <th>水</th>
                <th>木</th>
                <th>金</th>
                <th>土</th>
            </tr>
        </thead>
        <tbody id="calendar-body">
            <!-- カレンダーの日付がここに動的に生成されます -->
        </tbody>
    </table>

    <div id="today-schedule" class="schedule-list">
        <h2 id="current-date"></h2>
        <div id="schedule-items"></div>
    </div>

    <div class="arrow-buttons">
        <button onclick="changeMonth(-1)">◀ 前の日</button>
        <button onclick="changeMonth(+1)">次の日 ▶</button>
    </div>

    <button class="add-schedule-btn" onclick="window.location.href='/schedules/create'">予定を追加</button>

    <!-- モーダル -->
    <div id="schedule-modal">
        <h2 id="modal-title"></h2>
        <p id="modal-content"></p>
        <div class="modal-actions">
            <button id="delete-schedule-btn" onclick="deleteSchedule()">削除</button>
            <button id="edit-schedule-btn" onclick="editSchedule()">更新</button>
            <button onclick="closeModal()">閉じる</button>
        </div>
    </div>
    <div id="modal-overlay" onclick="closeModal()"></div>

    <script>
        let currentDate = new Date();
        let schedules = @json($schedules); // サーバーから受け取ったスケジュールデータ
        let selectedSchedule = null; // 選択中のスケジュールを保存
        let selectedDateElement = null;

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function updateMonthDisplay() {
            const monthDisplay = document.getElementById('current-month');
            const monthName = currentDate.toLocaleString('default', { month: 'long' });
            const firstDay = formatDate(new Date(currentDate.getFullYear(), currentDate.getMonth(), 1));
            const lastDay = formatDate(new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0));

            monthDisplay.textContent = `${currentDate.getFullYear()}年${monthName}のスケジュール`;
            document.getElementById('month-info').textContent = `この月の範囲: ${firstDay} 〜 ${lastDay}`;
        }

        function displaySchedulesForDate(date) {
            let hasSchedule = false;

            schedules.forEach(schedule => {
                if (schedule.start_date === date) {
                    hasSchedule = true;
                }
            });

            return hasSchedule ? '<div class="has-schedule"></div>' : ''; // 予定なしの場合は空
        }

        function updateCalendar() {
            const calendarBody = document.getElementById('calendar-body');
            calendarBody.innerHTML = ''; // 既存のカレンダーをクリア

            const startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
            const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
            const startDay = startOfMonth.getDay();

            let currentDay = 1;
            for (let i = 0; i < 6; i++) {
                let row = '<tr>';
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < startDay || currentDay > daysInMonth) {
                        row += '<td></td>';
                    } else {
                        const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDay);
                        const formattedDate = formatDate(date);
                        const scheduleIndicator = displaySchedulesForDate(formattedDate); // 予定の丸
                        row += `<td class="${date.toDateString() === new Date().toDateString() ? 'today' : ''}" onclick="selectDate(${currentDay})">
                            <strong>${currentDay}</strong>
                            ${scheduleIndicator} <!-- 予定があれば緑の丸 -->
                        </td>`;
                        currentDay++;
                    }
                }
                row += '</tr>';
                calendarBody.innerHTML += row;
            }
        }

        function changeMonth(offset) {
            currentDate.setMonth(currentDate.getMonth() + offset);
            updateMonthDisplay();
            updateCalendar();
            displaySchedules(currentDate); // 月変更時に今日の日付の予定を表示
        }

        function selectDate(day) {
            const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);

            // 以前の選択をクリア
            if (selectedDateElement) {
                selectedDateElement.classList.remove('selected-date');
            }

            // 新しく選択された日付を強調
            const cells = document.querySelectorAll('#calendar-body td');
            cells.forEach(cell => {
                if (cell.textContent.trim() === String(day)) {
                    cell.classList.add('selected-date');
                    selectedDateElement = cell;
                }
            });

            displaySchedules(selectedDate);
        }

        function showModal(schedule) {
            selectedSchedule = schedule; // 選択されたスケジュールを保存
            document.getElementById('modal-title').textContent = `${schedule.oshiname}: ${schedule.title}`;
            document.getElementById('modal-content').innerHTML = `
                <p>日付: ${schedule.start_date}</p>
                ${schedule.thumbnail ? `<img src="/storage/${schedule.thumbnail}" alt="サムネイル" style="width: 100%;">` : ''}
            `;
            document.getElementById('schedule-modal').style.display = 'block';
            document.getElementById('modal-overlay').style.display = 'block';
        }

        function deleteSchedule() {
    if (selectedSchedule) {
        if (confirm('本当にこの予定を削除しますか？')) {
            fetch(`/schedules/${selectedSchedule.id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (response.ok) {
                    alert('予定が削除されました');
                    closeModal();
                    location.reload(); // ページをリロードして予定を更新
                } else {
                    alert('削除に失敗しました');
                }
            });
        }
    }
}


        function editSchedule() {
            if (selectedSchedule) {
                // 更新ページへの遷移
                window.location.href = `/schedules/${selectedSchedule.id}/edit`;
            }
        }

        function closeModal() {
            document.getElementById('schedule-modal').style.display = 'none';
            document.getElementById('modal-overlay').style.display = 'none';
        }

        function displaySchedules(date) {
            const formattedDate = formatDate(date);
            document.getElementById('current-date').textContent = `日付: ${formattedDate}`;
            const scheduleItems = document.getElementById('schedule-items');
            scheduleItems.innerHTML = '';
            let foundSchedule = false;

            schedules.forEach(schedule => {
                if (schedule.start_date === formattedDate) {
                    foundSchedule = true;
                    const item = document.createElement('button'); // ボタンとして作成
                    item.classList.add('schedule-item');
                    item.innerHTML = `<strong>${schedule.oshiname}: ${schedule.title}</strong>`;
                    item.onclick = () => showModal(schedule); // モーダル表示を設定
                    scheduleItems.appendChild(item);
                }
            });

            if (!foundSchedule) {
                scheduleItems.innerHTML = '<p>予定はありません</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateMonthDisplay();
            updateCalendar();
            displaySchedules(currentDate); // 初期表示で今日の日付の予定を表示
        });
    </script>

