<button {{ $attributes->merge(['class' => 'inline-flex items-center rounded-md border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50']) }}>
    {{ $slot }}
</button>
