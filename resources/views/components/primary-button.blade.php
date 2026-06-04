<button {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800']) }}>
    {{ $slot }}
</button>
