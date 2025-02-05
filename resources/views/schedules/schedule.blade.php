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
        <div class="schedule-legend">
    <div class="legend-item"><span class="legend-color schedule-live"></span> リアルライブ</div>
    <div class="legend-item"><span class="legend-color schedule-event"></span> リアルイベント</div>
    <div class="legend-item"><span class="legend-color schedule-stream"></span> 配信予定</div>
    <div class="legend-item"><span class="legend-color schedule-onlive"></span> ライブ配信</div>
    <div class="legend-item"><span class="legend-color schedule-goods"></span> グッズ発売日</div>
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

    <!-- モーダルオーバーレイ -->
    <div id="modal-overlay" class="modal-overlay"></div>

    <!-- モーダル -->
    <div id="schedule-modal" class="modal">
        <div class="modal-content">
            <div class="modal-image">
                <img id="modal-image" src="" alt="Schedule Image" style=" display: none;" />
            </div>
            <div class="modal-info">
                <h2 id="modal-title"></h2>
                <p id="modal-oshiname"></p> <!-- 推しの名前 -->
                <p id="modal-start-time"></p> <!-- 開始日時 -->
                <p id="modal-end-time"></p> <!-- 終了日時 -->
                <p id="modal-content"></p> <!-- 内容 -->
                <div class="modal-actions">
                    <button onclick="closeModal()">閉じる</button>

    <button id="edit-schedule-btn">編集</button> <!-- 追加 -->

                </div>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')

<script src="{{ asset('js/callender.js') }}"></script>
@endsection
