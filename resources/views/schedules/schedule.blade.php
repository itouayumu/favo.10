<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>スケジュール一覧</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }

        table {
            width: 70%;
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            height: 100px;
            width: 100px;
            box-sizing: border-box;
            cursor: pointer;
        }

        th {
            background-color: #f4f4f4;
        }

        td {
            vertical-align: top;
            word-wrap: break-word;
        }

        .today {
            background-color: #f2f2f2;
        }

        .schedule-list {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff3e6;
            width: 80%;
            margin: 0 auto;
        }

        .schedule-item {
            padding: 15px;
            background-color: #fefefe;
            margin: 10px 0;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .arrow-buttons {
            display: flex;
            justify-content: center;
            margin-top: 15px;
        }

        .arrow-buttons button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .arrow-buttons button:hover {
            background-color: #45a049;
        }

        .add-schedule-btn {
            background-color: #007bff;
            color: white;
            padding: 15px 25px;
            font-size: 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            position: fixed;
            bottom: 20px;
            right: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .add-schedule-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <a href="/users/profile_edit"><img src="" alt="アイコン"></a>
    <h1>スケジュール管理</h1>

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
        <button onclick="moveDate(-1)">◀ 前の日</button>
        <button onclick="moveDate(1)">次の日 ▶</button>
    </div>

    <button class="add-schedule-btn" onclick="window.location.href='/schedules/create'">予定を追加</button>

    <script>
        let currentDate = new Date();
        let schedules = @json($schedules); // サーバーから受け取ったスケジュールデータ

        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        function updateMonthDisplay() {
            const monthDisplay = document.getElementById('current-month');
            const monthName = currentDate.toLocaleString('default', { month: 'long' });
            monthDisplay.textContent = `${currentDate.getFullYear()}年${monthName}のスケジュール`;
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
                        row += `<td class="${date.toDateString() === new Date().toDateString() ? 'today' : ''}" onclick="selectDate(${currentDay})">
                            <strong>${currentDay}</strong>
                            <div>${displaySchedulesForDate(formattedDate)}</div>
                        </td>`;
                        currentDay++;
                    }
                }
                row += '</tr>';
                calendarBody.innerHTML += row;
            }
        }

        function displaySchedulesForDate(date) {
            let scheduleHTML = '';
            schedules.forEach(schedule => {
                if (schedule.start_date === date) {
                    scheduleHTML += `<div><a href="/schedules/${schedule.id}">${schedule.oshiname}: ${schedule.title}</a></div>`;
                }
            });
            return scheduleHTML; // 予定なしの場合は空文字列
        }

        function changeMonth(offset) {
            currentDate.setMonth(currentDate.getMonth() + offset);
            updateMonthDisplay();
            updateCalendar();
            displaySchedules(currentDate); // 月変更時に今日の日付の予定を表示
        }

        function moveDate(offset) {
            currentDate.setDate(currentDate.getDate() + offset);
            displaySchedules(currentDate);
            updateCalendar();
        }

        function selectDate(day) {
            const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
            displaySchedules(selectedDate);
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
                    const item = document.createElement('div');
                    item.classList.add('schedule-item');
                    item.innerHTML = `
            <strong>${schedule.oshiname}: ${schedule.title}</strong>
            ${schedule.thumbnail ? `<br><img src="/storage/${schedule.thumbnail}" alt="サムネイル" width="50">` : ''}
        `;
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
</body>
</html>
