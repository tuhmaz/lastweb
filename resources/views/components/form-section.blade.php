<div class="p-4 bg-white shadow-md rounded-lg">
    <div class="mb-4 border-b pb-2">
        <h3 class="text-lg font-semibold text-gray-900">
            {{ $title ?? '' }}
        </h3>
        <p class="text-sm text-gray-600">
            {{ $description ?? '' }}
        </p>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
