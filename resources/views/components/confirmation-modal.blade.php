<div class="fixed inset-0 flex items-center justify-center bg-gray-500 bg-opacity-75">
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-sm w-full">
        <h2 class="text-lg font-semibold">{{ $title }}</h2>
        <p class="text-sm text-gray-600 mt-2">{{ $message }}</p>
        <div class="mt-4 flex justify-end">
            <button class="bg-gray-300 text-gray-700 px-4 py-2 rounded mr-2" @click="closeModal">Cancel</button>
            <button class="bg-red-600 text-white px-4 py-2 rounded" @click="confirmAction">Confirm</button>
        </div>
    </div>
</div>
