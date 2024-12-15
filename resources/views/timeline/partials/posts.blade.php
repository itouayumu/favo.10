<!-- resources/views/partials/post.blade.php -->
<div class="post">
    @if($post->content)
        <p>{{ $post->content }}</p>
    @endif

    @if($post->created_at)
        <p class="card-text">
            <small class="text-muted">
                {{ $post->created_at->format('Y-m-d H:i') }}
            </small>
        </p>
    @endif

    @if($post->favorite_id)
        <p>Favorite ID: {{ $post->favorite_id }}</p>
    @endif

    @if($post->schedule_id)
        <p>Schedule ID: {{ $post->schedule_id }}</p>
    @endif

    @if($post->image)
        <img src="{{ $post->image }}" alt="Post Image">
    @endif
</div>
