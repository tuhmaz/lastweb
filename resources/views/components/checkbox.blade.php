@props(['name', 'value' => '', 'checked' => false])

<input type="checkbox"
       name="{{ $name }}"
       value="{{ $value }}"
       {{ $checked ? 'checked' : '' }}
       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
