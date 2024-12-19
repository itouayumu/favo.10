@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/callender.css') }}">
@endsection

@section('content')
    <h1>予定表</h1>

<div class="scroll">

    <div class="arrow-buttons">
        <button onclick="changeMonth(-1)">◀ 前の月</button>
        <button onclick="changeMonth(+1)">次の月 ▶</button>
    </div>

    <!-- カレンダー表示 -->
    <div class="calendar">
        <div class="month">
            <span id="current-month" class="current-month"></span>
            <span id="current-year" class="current-year"></span>
        </div>

        <img src="{{ asset('img/osipin.png') }}" alt="押しピン" class="osipin1">
        <img src="{{ asset('img/osipin.png') }}" alt="押しピン" class="osipin2">

        <table>
            <thead>
                <tr>
                    <th>SUN</th>
                    <th>MON</th>
                    <th>TUE</th>
                    <th>WED</th>
                    <th>THU</th>
                    <th>FRI</th>
                    <th>SAT</th>
                </tr>
            </thead>
            <tbody id="calendar-body">
                <!-- カレンダーの日付がここに動的に生成されます -->
            </tbody>
        </table>
    </div>

    <div id="today-schedule" class="schedule-list">
        <h2 id="current-date"></h2>
        <div id="schedule-items"></div>
        <img src="{{ asset('img/osipin.png') }}" alt="押しピン" class="osipin3">
    </div>

    <button class="add-schedule-btn" onclick="window.location.href='/schedules/create'">+</button>

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