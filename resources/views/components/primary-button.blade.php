<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-stone-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-stone-800 focus:bg-stone-800 active:bg-black focus:outline-none focus:ring-2 focus:ring-stone-400 focus:ring-offset-2 disabled:opacity-40 disabled:cursor-not-allowed transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
