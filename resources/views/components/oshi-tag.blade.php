@props(['tagId', 'tagName', 'tagCount', 'favoriteId'])

<div class="oshi-tag">
    <a href="#" class="tag-click" 
       data-tag-id="{{ $tagId }}" 
       data-favorite-id="{{ $favoriteId }}">
        {{ $tagName }}{{ $tagCount }}
    </a>
    <span id="click-count-{{ $tagId }}"></span>
</div>
