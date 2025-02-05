let currentDate = new Date();
let selectedDateElement = null;
let schedules = []; // スケジュールデータを保存する配列
async function fetchSchedules() {
    try {
        const response = await fetch('/api/schedules');
        const responseText = await response.text(); // レスポンスの生データをテキストとして取得
        console.log('Response Text:', responseText); // レスポンス内容をログ出力

        // JSON にパースする
        schedules = JSON.parse(responseText); // JSONに変換
        updateMonthDisplay();
        updateCalendar();
        displaySchedules(currentDate);
    } catch (error) {
        console.error('スケジュールの取得に失敗しました:', error);
    }
}


document.addEventListener('DOMContentLoaded', fetchSchedules);


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
            scheduleType = schedule.title;
        }
    });

    if (hasSchedule) {
        return `<div class="has-schedule ${getScheduleClass(scheduleType)}"></div>`;
    }
    return '';
}

function updateCalendar() {
    const calendarBody = document.getElementById('calendar-body');
    calendarBody.innerHTML = '';

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
                const scheduleIndicator = displaySchedulesForDate(formattedDate);
                row += `<td class="${date.toDateString() === new Date().toDateString() ? 'today' : ''}" onclick="selectDate(${currentDay})">
                    <strong>${currentDay}</strong>
                    ${scheduleIndicator}
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
    displaySchedules(currentDate);
}

function selectDate(day) {
    const selectedDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);

    if (selectedDateElement) {
        selectedDateElement.classList.remove('selected-date');
    }

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
            const item = document.createElement('button');
            const oshiname = schedule.favorite ? schedule.favorite.name : '推し不明';
            item.classList.add('schedule-item');
            item.innerHTML = `<strong>${oshiname}: ${schedule.title}</strong>`;
            item.onclick = () => openModal(schedule);
            scheduleItems.appendChild(item);
        }
    });

    if (!foundSchedule) {
        scheduleItems.innerHTML = '<p>予定はありません</p>';
    }
}

function getScheduleClass(title) {
    switch (title) {
        case 'リアルライブ': return 'schedule-live';
        case 'リアルイベント': return 'schedule-event';
        case '配信予定': return 'schedule-stream';
        case 'ライブ配信': return 'schedule-onlive';
        case 'グッズ発売日': return 'schedule-goods';
        default: return 'schedule-default';
    }
}

function openModal(schedule) {
    document.getElementById('modal-title').textContent = schedule.title;
    const oshiname = schedule.favorite ? schedule.favorite.name : '推し不明';
    document.getElementById('modal-oshiname').textContent = `推し: ${oshiname}`;
    document.getElementById('modal-start-time').textContent = `開始日時: ${schedule.start_date} ${schedule.start_time}`;
    document.getElementById('modal-end-time').textContent = `終了日時: ${schedule.end_date} ${schedule.end_time}`;
    document.getElementById('modal-content').textContent = schedule.content;

    const modalImage = document.getElementById('modal-image');
    if (schedule.image) {
        modalImage.src = `/storage/${schedule.image}`;
        modalImage.style.display = 'block';
    } else {
        modalImage.style.display = 'none';
    }

    document.getElementById('edit-schedule-btn').onclick = () => {
        window.location.href = `/schedules/${schedule.id}/edit`;
    };

    document.getElementById('schedule-modal').style.display = 'block';
    document.getElementById('modal-overlay').style.display = 'block';
}

function closeModal() {
    document.getElementById('schedule-modal').style.display = 'none';
    document.getElementById('modal-overlay').style.display = 'none';
}

document.getElementById('modal-overlay').addEventListener('click', closeModal);

document.addEventListener('DOMContentLoaded', () => {
    fetchSchedules(); // ページロード時にスケジュールデータを取得
});
