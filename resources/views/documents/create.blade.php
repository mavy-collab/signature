<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Documents') }}
            </a>

            <h1 class="font-serif text-3xl sm:text-4xl text-stone-900">{{ __('Téléverser un document') }}</h1>

            <div class="p-4 sm:p-8 bg-white ring-1 ring-stone-200 rounded-2xl">
                <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                    @csrf

                    <x-input-label for="file" :value="__('Fichier PDF')" />
                    <input id="file" type="file" name="file" accept="application/pdf,.pdf" required
                           class="block mt-1 w-full text-sm text-stone-600 border border-stone-300 rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200">
                    <x-input-error :messages="$errors->get('file')" class="mt-2" />

                    <p class="mt-2 text-xs text-stone-500">{{ __('Format PDF uniquement, 20 Mo maximum.') }}</p>

                    <div class="mt-6 flex items-start gap-3 p-4 bg-stone-50 rounded-lg">
                        <input id="already_signed" type="checkbox" name="already_signed" value="1"
                               class="mt-0.5 rounded border-stone-300 text-stone-600 focus:ring-stone-500">
                        <label for="already_signed" class="text-sm text-stone-700">
                            <span class="font-medium">{{ __('Ce document est déjà signé') }}</span>
                            <span class="block text-stone-500 mt-0.5">
                                {{ __('Il sera déposé directement dans le coffre-fort, sans passer par l\'étape de signature.') }}
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end mt-6">
                        <x-primary-button type="submit">
                            {{ __('Continuer') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
