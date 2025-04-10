@props(['message'])

@if ($message)
    <p class="text-sm text-red-600">{{ $message }}</p>
@endif
