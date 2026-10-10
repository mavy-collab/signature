<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Tableau de bord') }}
            </a>

            <div>
                <h1 class="font-serif text-4xl sm:text-5xl text-stone-900">{{ __('Coffre-fort') }}</h1>
                <p class="mt-3 text-stone-500 max-w-md">
                    {{ __('Cet espace est protégé : votre mot de passe vous a été redemandé pour y accéder. Il contient tous vos documents définitivement signés.') }}
                </p>
            </div>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl overflow-hidden">
                @if ($documents->isEmpty())
                    <p class="p-6 text-sm text-stone-500">{{ __('Aucun document signé pour le moment.') }}</p>
                @else
                    <div class="flex items-center justify-between px-6 py-3 border-b border-stone-100">
                        <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">
                            {{ $documents->count() }} {{ $documents->count() > 1 ? __('documents') : __('document') }}
                        </span>
                        <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Statut') }}</span>
                    </div>

                    <ul class="divide-y divide-stone-100">
                        @foreach ($documents as $document)
                            <li>
                                <a href="{{ route('vault.show', $document) }}" class="flex items-center gap-4 px-6 py-4 hover:bg-stone-50 transition">
                                    <span class="shrink-0 h-11 w-11 rounded-xl bg-stone-100 flex items-center justify-center">
                                        <svg class="h-5 w-5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                    </span>
                                    <div class="min-w-0 flex-1 flex items-center justify-between gap-4">
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-stone-900 truncate">{{ $document->original_name }}</p>
                                            <p class="text-xs text-stone-500 mt-0.5">
                                                {{ __('Signé le') }} {{ ($document->signature?->signed_at ?? $document->updated_at)->format('d/m/Y H:i') }}
                                            </p>
                                        </div>
                                        <span class="shrink-0 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">{{ __('Signé') }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($documents->isNotEmpty())
                <p class="text-center text-xs text-stone-400">
                    {{ __('Tous les documents signés sont horodatés, scellés et vérifiables via leur empreinte numérique.') }}
                    <a href="{{ route('document-verification.show') }}" class="underline hover:text-stone-600">{{ __('Vérifier un document') }}</a>
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
