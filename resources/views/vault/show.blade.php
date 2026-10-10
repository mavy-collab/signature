@php
    $existingSignatureData = $document->signature ? [
        'page' => $document->signature->page_number,
        'x' => $document->signature->pos_x,
        'y' => $document->signature->pos_y,
        'width' => $document->signature->box_width,
        'height' => $document->signature->box_height,
        'image' => 'data:'.$document->signature->mimeType().';base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('local')->get($document->signature->image_path)),
    ] : null;
@endphp

<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('vault.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Coffre-fort') }}
            </a>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <h1 class="font-serif text-2xl sm:text-3xl text-stone-900 truncate">{{ $document->original_name }}</h1>
                    <span class="shrink-0 px-3 py-1 text-sm font-medium rounded-full bg-green-100 text-green-800">{{ __('Signé') }}</span>
                </div>
                <a href="{{ route('vault.download', $document) }}"
                   class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-stone-800 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                    {{ __('Télécharger le PDF') }}
                </a>
            </div>

            @if (session('status') === 'document-matches-existing-signed')
                <div class="p-4 bg-amber-50 text-amber-800 rounded-lg text-sm">
                    {{ __("Ce fichier correspond exactement, au bit près, à un document déjà signé sur OtoSign : il a été classé directement dans votre coffre-fort et ne peut pas être signé à nouveau.") }}
                </div>
            @endif

            @include('documents.partials.info')

            <div class="p-4 sm:p-8 bg-white ring-1 ring-stone-200 sm:rounded-2xl space-y-4">
                <header>
                    <h3 class="text-lg font-medium text-stone-900">
                        {{ $document->signature ? __('Emplacement de la signature') : __('Document') }}
                    </h3>
                    @if (! $document->signature)
                        <p class="mt-1 text-sm text-stone-600">
                            {{ __('Ce document a été téléversé déjà signé : la plateforme n\'a pas placé de signature dessus.') }}
                        </p>
                    @endif
                </header>

                @include('documents.partials.viewer', [
                    'signed' => true,
                    'fileUrl' => route('vault.file', $document),
                    'existingSignatureData' => $existingSignatureData,
                ])
            </div>

            @include('documents.partials.audit')
        </div>
    </div>

    @vite('resources/js/document-viewer.js')
</x-app-layout>
