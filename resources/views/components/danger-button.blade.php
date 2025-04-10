<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring']) }}>
    {{ $slot }}
</button>
