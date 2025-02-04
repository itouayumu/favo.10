<li id="tag-{{ $tagId }}">
    <a href="#" class="tag-click" data-tag-id="{{ $tagId }}" data-user-id="{{ $user->id }}">
        {{ $tagName }}
    </a>
    <span id="click-count-{{ $tagId }}">{{ $tagCount ?? 0 }}</span>
</li>
