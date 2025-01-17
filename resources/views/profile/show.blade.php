@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header text-center">
            <img src="{{ $user->image ? asset('storage/' . $user->image) : 'https://via.placeholder.com/150' }}" 
                 alt="Profile Image" class="img-thumbnail rounded-circle" width="150">
            <h3>{{ $user->name }}</h3>
        </div>

        <div>
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
            <div class="mb-3">
                <strong>プロフィール:</strong>
                <p>{{ $user->introduction ?? '詳細情報はありません。' }}</p>
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
