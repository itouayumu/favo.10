@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://via.placeholder.com/150' }}"
                 alt="Profile Image" class="icon" width="150">
            <div class="prate">
                <div class="name">
                    <h3 class="username">{{ $user->name }}</h3>
                </div>
            </div>
        </div>

        <div class="tag">
            @php
                $tags = $user->tags()->wherePivot('hidden_flag', 0)->get();
            @endphp

            @if ($tags->isNotEmpty())
                @foreach ($tags as $tag)
                    <!-- コンポーネントにデータを渡す -->
                    <x-tag :tagId="$tag->id" :tagName="$tag->name" :tagCount="$tag->pivot->count" />
                @endforeach
            @else
                <p>タグはありません。</p>
            @endif
        </div>

        <div class="card-body">
            <div class="introduction">
                <div class="intro_content">
                    <p>{{ $user->introduction ?? '詳細情報はありません。' }}</p>
                </div>
            </div>
            <hr>
            <div class="d-flex justify-content-between mt-3">
                <button class="btn btn-primary" onclick="location.href='{{ route('profile.edit') }}'">編集</button>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit">ログアウト</button>
                </form>
                
                <a href="{{ url()->previous() }}" class="btn btn-secondary">戻る</a>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // タグをクリックしたとき
        document.querySelectorAll('.tag-click').forEach(function (tagLink) {
            tagLink.addEventListener('click', function (event) {
                event.preventDefault();  // デフォルトのリンク動作を無効化

                let tagId = this.dataset.tagId;

                // AJAXリクエストでクリック数を増加
                fetch(`/tags/increment/${tagId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // クリック数を更新
                        document.getElementById(`click-count-${tagId}`).innerText = data.click_count;
                    } else {
                        alert(data.message);  // エラーメッセージ
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        });
    });
</script>
@endsection
