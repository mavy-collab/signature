@php
    $firstName = Str::of(Auth::user()->name)->explode(' ')->first();
    $isCompany = Auth::user()->account_type === 'company';

    $summary = $signedCount > 0 || $pendingCount > 0
        ? __('Vous avez actuellement :signed document(:signedPlural) sécurisé(:signedPlural) dans votre coffre-fort et :pending signature(:pendingPlural) en attente.', [
            'signed' => $signedCount,
            'signedPlural' => $signedCount > 1 ? 's' : '',
            'pending' => $pendingCount,
            'pendingPlural' => $pendingCount > 1 ? 's' : '',
        ])
        : __('Vous n\'avez pas encore de document. Déposez-en un pour commencer.');
@endphp

<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

                <!-- Left column -->
                <div class="lg:col-span-4">
                    <h1 class="font-serif text-5xl leading-[1.1] text-stone-900">
                        {{ __('Bonjour,') }}<br>{{ $firstName }}
                    </h1>
                    <p class="mt-5 text-stone-500 leading-relaxed">
                        {{ $summary }}
                    </p>

                    <div class="mt-8 pt-6 border-t border-stone-200">
                        <p class="text-xs font-medium tracking-wider text-stone-400 uppercase">
                            {{ $isCompany ? __('Compte entreprise') : __('Compte particulier') }}
                        </p>
                        <p class="mt-1 font-medium text-stone-900">
                            {{ $isCompany ? Auth::user()->company_name : Auth::user()->name }}
                            <span class="text-stone-400 font-normal">— {{ __('Compte actif') }}</span>
                        </p>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <a href="{{ route('documents.create') }}">
                            <x-primary-button>{{ __('Nouveau document') }}</x-primary-button>
                        </a>
                        <a href="{{ route('profile.edit') }}">
                            <x-secondary-button>{{ __('Paramètres') }}</x-secondary-button>
                        </a>
                    </div>
                </div>

                <!-- Right column -->
                <div class="lg:col-span-8 space-y-6">

                    <!-- Signature status -->
                    <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $hasSignature ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $hasSignature ? __('Ma signature est prête') : __('Signature à enregistrer') }}
                                </span>
                                <h3 class="mt-4 font-serif text-xl text-stone-900">{{ __('Signature électronique') }}</h3>
                                <p class="mt-2 text-sm text-stone-500 max-w-md">
                                    {{ $hasSignature
                                        ? __('Votre signature manuscrite est enregistrée et sécurisée. Elle est utilisée pour signer tous vos documents.')
                                        : __('Dessinez ou importez votre signature pour pouvoir commencer à signer vos documents.') }}
                                </p>
                                <a href="{{ $hasSignature ? route('signature.edit') : route('signature.show') }}"
                                   class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-stone-900 hover:text-stone-600 transition">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12l-3-3M6 18l3.75.75L21 8.25" /></svg>
                                    {{ $hasSignature ? __('Modifier le tracé') : __('Enregistrer ma signature') }}
                                </a>
                            </div>
                            <a href="{{ $hasSignature ? route('signature.edit') : route('signature.show') }}"
                               class="shrink-0 h-10 w-10 rounded-full bg-stone-100 hover:bg-stone-200 flex items-center justify-center transition">
                                <svg class="h-4 w-4 text-stone-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Documents & vault -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <a href="{{ route('documents.index') }}" class="block bg-white ring-1 ring-stone-200 hover:ring-stone-300 rounded-2xl p-6 transition">
                            <div class="h-10 w-10 rounded-full bg-stone-100 flex items-center justify-center mb-4">
                                <svg class="h-5 w-5 text-stone-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                            </div>
                            <h4 class="font-serif text-lg text-stone-900">{{ __('Documents') }}</h4>
                            <p class="mt-1 text-sm {{ $pendingCount > 0 ? 'text-amber-700' : 'text-stone-500' }}">
                                {{ $pendingCount }} {{ __('document(s) en attente') }}
                            </p>
                            <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between text-sm font-medium text-stone-900">
                                {{ __('Déposer un fichier') }}
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </div>
                        </a>

                        <a href="{{ route('vault.index') }}" class="block bg-white ring-1 ring-stone-200 hover:ring-stone-300 rounded-2xl p-6 transition">
                            <div class="h-10 w-10 rounded-full bg-stone-100 flex items-center justify-center mb-4">
                                <svg class="h-5 w-5 text-stone-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                            </div>
                            <h4 class="font-serif text-lg text-stone-900">{{ __('Coffre-fort') }}</h4>
                            <p class="mt-1 text-sm text-stone-500">{{ $signedCount }} {{ __('documents signés') }}</p>
                            <div class="mt-5 pt-4 border-t border-stone-100 flex items-center justify-between text-sm font-medium text-stone-900">
                                {{ __('Consulter les archives') }}
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </div>
                        </a>
                    </div>

                    <!-- Recent activity -->
                    <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-serif text-lg text-stone-900">{{ __('Activité récente') }}</h3>
                            <a href="{{ route('history.index') }}" class="text-xs font-semibold tracking-wider text-stone-500 hover:text-stone-900 uppercase transition">
                                {{ __('Voir tout') }}
                            </a>
                        </div>

                        @if ($recentEvents->isEmpty())
                            <p class="text-sm text-stone-500 py-3">{{ __('Aucune activité pour le moment.') }}</p>
                        @else
                            <ul class="divide-y divide-stone-100">
                                @foreach ($recentEvents as $event)
                                    <li class="flex items-center gap-3 py-3">
                                        <span class="h-1.5 w-1.5 rounded-full shrink-0 {{ in_array($event['action'], ['signed', 'uploaded_signed'], true) ? 'bg-green-500' : 'bg-stone-300' }}"></span>
                                        @if ($event['route'])
                                            <a href="{{ route($event['route'], $event['route_params']) }}" class="flex-1 min-w-0 text-sm text-stone-700 hover:text-stone-950 truncate transition">
                                                {{ $event['label'] }}
                                            </a>
                                        @else
                                            <span class="flex-1 min-w-0 text-sm text-stone-700 truncate">{{ $event['label'] }}</span>
                                        @endif
                                        <span class="text-xs text-stone-400 whitespace-nowrap">{{ $event['created_at']->locale('fr')->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
