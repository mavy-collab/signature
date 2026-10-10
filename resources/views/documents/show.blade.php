<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Documents') }}
            </a>

            <div class="flex items-center justify-between gap-4">
                <h1 class="font-serif text-2xl sm:text-3xl text-stone-900 truncate">{{ $document->original_name }}</h1>
                <span class="shrink-0 px-3 py-1 text-sm font-medium rounded-full bg-amber-100 text-amber-800">{{ __('En attente de signature') }}</span>
            </div>

            @if ($errors->any())
                <div class="p-4 bg-red-50 text-red-700 rounded-lg">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @include('documents.partials.info')

            <div class="p-4 sm:p-8 bg-white ring-1 ring-stone-200 sm:rounded-2xl space-y-4">
                <header class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-stone-900">{{ __('Emplacement proposé pour la signature') }}</h3>
                    <p class="text-sm text-stone-500">{{ __('Faites glisser le cadre pour ajuster son emplacement.') }}</p>
                </header>

                @unless ($hasSignature)
                    <div class="p-4 bg-amber-50 text-amber-800 rounded-lg text-sm">
                        {{ __('Vous n\'avez pas encore enregistré de signature.') }}
                        <a href="{{ route('signature.show') }}" class="underline font-medium">{{ __('Enregistrez-la ici') }}</a>
                        {{ __('avant de pouvoir signer ce document.') }}
                    </div>
                @endunless

                @include('documents.partials.viewer', [
                    'signed' => false,
                    'fileUrl' => route('documents.file', $document),
                    'signRoute' => route('documents.sign', $document),
                ])
            </div>

            @include('documents.partials.audit')
        </div>
    </div>

    @vite('resources/js/document-viewer.js')
</x-app-layout>
