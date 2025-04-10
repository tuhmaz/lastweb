@props(['id' => null, 'maxWidth' => '2xl'])

<div x-data="{ show: @entangle($attributes->wire('model')).defer }"
     x-show="show"
     x-on:keydown.escape.window="show = false"
     class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0">

    <div x-show="show" class="fixed inset-0 transform transition-opacity bg-gray-900 bg-opacity-50"></div>

    <div x-show="show"
         x-transition
         class="bg-white rounded-lg shadow-xl transform transition-all sm:w-full sm:mx-auto sm:max-w-{{ $maxWidth }}">
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                {{ $title }}
            </div>
            <div class="mt-4 text-sm text-gray-600">
                {{ $content }}
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-100 text-right">
            {{ $footer }}
        </div>
    </div>
</div>
