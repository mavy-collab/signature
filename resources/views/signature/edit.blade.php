<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('signature.show') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Ma signature') }}
            </a>

            <h1 class="font-serif text-3xl sm:text-4xl text-stone-900">{{ __('Modifier ma signature') }}</h1>

            @if ($errors->any())
                <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <div class="p-4 bg-stone-50 text-stone-800 rounded-lg text-sm">
                {{ __('Remplacer votre signature n\'affecte pas les documents déjà signés : ils conservent l\'image de la signature telle qu\'elle était au moment de leur signature.') }}
            </div>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Signature actuelle') }}</span>
                <div class="mt-3 bg-stone-50 rounded-xl flex items-center justify-center py-6 px-6">
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($signature->image_path) }}" alt="{{ __('Signature actuelle') }}" class="max-w-full" style="max-height: 120px;">
                </div>
            </div>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8 space-y-6">
                <header>
                    <h3 class="text-lg font-medium text-stone-900">{{ __('Nouvelle signature') }}</h3>
                    <p class="mt-1 text-sm text-stone-500">
                        {{ __('Dessinez votre nouvelle signature ou importez une image pour remplacer l\'actuelle.') }}
                    </p>
                </header>

                @include('signature.partials.pad', ['actionRoute' => route('signature.update')])
            </div>
        </div>
    </div>
</x-app-layout>
