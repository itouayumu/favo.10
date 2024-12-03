const currentDate = new Date();
let selectedDate = currentDate;

// サンプルデータ
const schedules = [
    { date: '2024-02-01', title: '呪術アニメ放映日 16:00〜' },
    { date: '2024-02-01', title: '呪術アニメ放映日 16:00〜' }
];

function formatDate(date) {
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
}

function updateCalendar() {
    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = '';

    const startOfMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
    const startDay = startOfMonth.getDay();

    let day = 1;

    for (let i = 0; i < 6; i++) {
        const row = document.createElement('tr');
        for (let j = 0; j < 7; j++) {
            const cell = document.createElement('td');
            if (i === 0 && j < startDay || day > daysInMonth) {
                cell.textContent = '';
            } else {
                const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
                cell.textContent = day;
                const formattedDate = formatDate(date);

                // イベントマーカー
                if (schedules.some(s => s.date === formattedDate)) {
                    const marker = document.createElement('div');
                    marker.classList.add('event-marker');
                    cell.appendChild(marker);
                }

                // 選択時の動作
                cell.addEventListener('click', () => {
                    document.querySelectorAll('td').forEach(td => td.classList.remove('selected'));
                    cell.classList.add('selected');
                    selectedDate = date;
                    updateSchedule();
                });

                day++;
            }
            row.appendChild(cell);
        }
        calendarBody.appendChild(row);
    }

    // 月ラベル更新
    document.getElementById('month-label').textContent = `${currentDate.getFullYear()}年 ${currentDate.getMonth() + 1}月`;
}

function updateSchedule() {
    const scheduleDate = document.getElementById('schedule-date');
    const scheduleList = document.getElementById('schedule-list');
    scheduleList.innerHTML = '';

    const formattedDate = formatDate(selectedDate);
    scheduleDate.textContent = `${selectedDate.getDate()}日`;

    const daySchedules = schedules.filter(s => s.date === formattedDate);
    if (daySchedules.length > 0) {
        daySchedules.forEach(s => {
            const li = document.createElement('li');
            li.textContent = s.title;
            scheduleList.appendChild(li);
        });
    } else {
        const noSchedule = document.createElement('li');
        noSchedule.textContent = '予定はありません';
        scheduleList.appendChild(noSchedule);
    }
}

document.getElementById('prev-month').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    updateCalendar();
});

document.getElementById('next-month').addEventListener('click', () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    updateCalendar();
});

// 初期化
updateCalendar();
updateSchedule();
