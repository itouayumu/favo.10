@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/callender.css') }}">
@endsection

@section('content')
    <h1>予定表</h1>

<div class="scroll">
    <!-- カレンダー表示 -->
    <div>
        <span id="current-month"></span>
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
</div>
@endsection

@section('scripts')
<script>
    let schedules = JSON.parse('@json($schedules)');
</script>
<script src="{{ asset('js/callender.js') }}"></script>
@endsection