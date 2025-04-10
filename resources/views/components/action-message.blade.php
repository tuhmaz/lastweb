@props(['on'])

<div x-data="{ shown: false, timeout: null }"
     x-init="@this.on('{{ $on }}', () => { clearTimeout(timeout); shown = true; timeout = setTimeout(() => { shown = false }, 2000); })"
     x-show.transition.opacity.out.duration.1500ms="shown"
     style="display: none;">
    <span class="text-sm text-green-600">{{ $slot }}</span>
</div>
