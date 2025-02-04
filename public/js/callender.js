let currentDate = new Date();
let selectedDateElement = null;

function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function updateMonthDisplay() {
    const yearDisplay = document.getElementById('current-year');
    const monthDisplay = document.getElementById('current-month');
    const monthName = currentDate.toLocaleString('default', { month: 'long' });
    yearDisplay.textContent = `${currentDate.getFullYear()}`;
    monthDisplay.textContent = `${monthName}`;
}

function displaySchedulesForDate(date) {
    let hasSchedule = false;
    let scheduleType = '';

    schedules.forEach(schedule => {
        if (schedule.start_date === date) {
            hasSchedule = true;
            scheduleType = schedule.title; // タイトルを取得
        }
    });

    if (hasSchedule) {
        return `<div class="has-schedule ${getScheduleClass(scheduleType)}"></div>`;
    }
    return ''; // 予定なしの場合は空
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
            const oshiname = schedule.favorite ? schedule.favorite.name : '推し不明';
            item.classList.add('schedule-item');
            item.innerHTML = `<strong>${oshiname}: ${schedule.title}</strong>`;
            item.onclick = () => openModal(schedule); // モーダルを開く
            scheduleItems.appendChild(item);
        }
    });
    if (!foundSchedule) {
        scheduleItems.innerHTML = '<p>予定はありません</p>';
    }
}

function getScheduleClass(title) {
    switch (title) {
        case 'リアルライブ':
            return 'schedule-live';
        case 'リアルイベント':
            return 'schedule-event';
        case '配信予定':
            return 'schedule-stream';
        case 'ライブ配信':
            return 'schedule-onlive';
        case 'グッズ発売日':
            return 'schedule-goods';
        default:
            return 'schedule-default';
    }
}

// モーダル詳細を表示する関数
function openModal(schedule) {
    document.getElementById('modal-title').textContent = schedule.title;
    const oshiname = schedule.favorite ? schedule.favorite.name : '推し不明';
    document.getElementById('modal-oshiname').textContent = `推し: ${oshiname}`;
    const startDateTime = `${schedule.start_date} ${schedule.start_time}`;
    const endDateTime = `${schedule.end_date} ${schedule.end_time}`;
    document.getElementById('modal-start-time').textContent = `開始日時: ${startDateTime}`;
    document.getElementById('modal-end-time').textContent = `終了日時: ${endDateTime}`;
    document.getElementById('modal-content').textContent = schedule.content;

    const modalImage = document.getElementById('modal-image');
    if (schedule.image) {
        modalImage.src = `/storage/${schedule.image}`;
        modalImage.style.display = 'block';
    } else {
        modalImage.style.display = 'none';
    }

    document.getElementById('schedule-modal').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
}

// モーダルを閉じる関数
function closeModal() {
    document.getElementById('schedule-modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
}

document.getElementById('modal-overlay').addEventListener('click', closeModal);

document.addEventListener('DOMContentLoaded', () => {
    updateMonthDisplay();
    updateCalendar();
    displaySchedules(currentDate); // 初期表示で今日の日付の予定を表示
});
