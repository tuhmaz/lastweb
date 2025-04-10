<button {{ $attributes->merge(['type' => 'button', 'class' => 'px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring']) }}>
    {{ $slot }}
</button>
