<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Tableau de bord') }}
            </a>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-4xl sm:text-5xl text-stone-900">{{ __('Documents') }}</h1>
                    <p class="mt-3 text-stone-500 max-w-md">
                        {{ __('Téléversez vos fichiers, suivez leur statut de signature et retrouvez-les automatiquement archivés dans votre coffre-fort.') }}
                    </p>
                </div>
                <a href="{{ route('documents.create') }}" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-stone-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 7.5L12 3m0 0L7.5 7.5M12 3v13.5" /></svg>
                    {{ __('Téléverser un document') }}
                </a>
            </div>

            @if (session('bulkSignedCount') !== null)
                <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                    {{ session('bulkSignedCount') }} {{ session('bulkSignedCount') > 1 ? __('documents signés.') : __('document signé.') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @php
                $pendingCount = $documents->where('status', 'pending')->count();
            @endphp

            <div x-data="{ selected: [] }">
                <form method="POST" action="{{ route('documents.sign-many') }}"
                      @submit="if (!confirm('Signer ' + selected.length + ' document(s) à l\'emplacement suggéré (dernière page, bas droite) ?')) $event.preventDefault()">
                    @csrf

                    @if ($pendingCount > 0)
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center gap-2 text-sm text-stone-600">
                                <input type="checkbox"
                                       class="rounded border-stone-300 text-stone-600 focus:ring-stone-500"
                                       @change="selected = $event.target.checked ? [{{ $documents->where('status', 'pending')->pluck('id')->implode(',') }}] : []">
                                {{ __('Tout sélectionner') }}
                            </label>

                            <button type="submit"
                                    x-show="selected.length > 0" x-cloak
                                    @disabled(! $hasSignature)
                                    class="inline-flex items-center px-5 py-2.5 bg-stone-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-stone-800 disabled:opacity-40 disabled:cursor-not-allowed transition">
                                <span x-text="'{{ __('Signer la sélection') }} (' + selected.length + ')'"></span>
                            </button>
                        </div>

                        @unless ($hasSignature)
                            <div class="p-3 mb-3 bg-amber-50 text-amber-800 rounded-lg text-xs">
                                {{ __('Enregistrez d\'abord votre signature pour pouvoir signer des documents.') }}
                                <a href="{{ route('signature.show') }}" class="underline font-medium">{{ __('Enregistrer ma signature') }}</a>
                            </div>
                        @endunless
                    @endif

                    <div class="bg-white ring-1 ring-stone-200 rounded-2xl overflow-hidden">
                        @if ($documents->isEmpty())
                            <p class="p-6 text-sm text-stone-500">{{ __('Aucun document pour le moment.') }}</p>
                        @else
                            <div class="flex items-center justify-between px-6 py-3 border-b border-stone-100">
                                <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">
                                    {{ $documents->count() }} {{ $documents->count() > 1 ? __('documents') : __('document') }}
                                </span>
                                <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Statut') }}</span>
                            </div>

                            <ul class="divide-y divide-stone-100">
                                @foreach ($documents as $document)
                                    <li class="flex items-center gap-4 px-6 py-4 hover:bg-stone-50 transition">
                                        @if (! $document->isSigned())
                                            <input type="checkbox" name="document_ids[]" value="{{ $document->id }}"
                                                   x-model.number="selected"
                                                   class="rounded border-stone-300 text-stone-600 focus:ring-stone-500">
                                        @endif

                                        <a href="{{ $document->isSigned() ? route('vault.show', $document) : route('documents.show', $document) }}" class="flex-1 flex items-center gap-4 min-w-0">
                                            <span class="shrink-0 h-11 w-11 rounded-xl bg-stone-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-stone-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                                            </span>
                                            <div class="min-w-0 flex-1 flex items-center justify-between gap-4">
                                                <div class="min-w-0">
                                                    <p class="text-sm font-medium text-stone-900 truncate">{{ $document->original_name }}</p>
                                                    <p class="text-xs text-stone-500 mt-0.5">
                                                        {{ __(':count page(s)', ['count' => $document->page_count]) }}
                                                        &middot;
                                                        {{ $document->created_at->format('d/m/Y H:i') }}
                                                    </p>
                                                </div>
                                                @if ($document->isSigned())
                                                    <span class="shrink-0 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                        {{ $document->uploaded_signed ? __('Déposé signé') : __('Signé') }} &middot; {{ __('coffre-fort') }}
                                                    </span>
                                                @else
                                                    <span class="shrink-0 inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-amber-100 text-amber-800">{{ __('En attente') }}</span>
                                                @endif
                                            </div>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </form>
            </div>

            @if ($documents->contains(fn ($document) => $document->isSigned()))
                <p class="text-center text-xs text-stone-400">
                    {{ __('Tous les documents signés sont horodatés, scellés et vérifiables via leur empreinte numérique.') }}
                    <a href="{{ route('document-verification.show') }}" class="underline hover:text-stone-600">{{ __('Vérifier un document') }}</a>
                </p>
            @endif
        </div>
    </div>
</x-app-layout>
