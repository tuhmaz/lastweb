<button {{ $attributes->merge(['type' => 'submit', 'class' => 'px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring']) }}>
    {{ $slot }}
</button>
