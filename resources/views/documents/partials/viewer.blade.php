@php
    $signed = $signed ?? false;
@endphp

<div
    data-document-viewer
    data-file-url="{{ $fileUrl }}"
    data-page-count="{{ $document->page_count }}"
    data-signed="{{ $signed ? '1' : '0' }}"
    @unless ($signed) data-suggestion='@json($suggestion)' @endunless
    @isset($existingSignatureData) data-existing='@json($existingSignatureData)' @endisset
>
    <div class="flex items-center gap-3 mb-3">
        <button type="button" id="dv-prev" class="px-2 py-1 text-sm border rounded disabled:opacity-40">&lsaquo;</button>
        <span id="dv-page-label" class="text-sm text-stone-600"></span>
        <button type="button" id="dv-next" class="px-2 py-1 text-sm border rounded disabled:opacity-40">&rsaquo;</button>
    </div>

    <div id="dv-canvas-wrap" class="relative inline-block border border-stone-300 bg-stone-50 max-w-full overflow-hidden" style="touch-action: none;">
        <canvas id="dv-canvas" class="block max-w-full"></canvas>
        <div id="dv-scanner" class="absolute inset-0 pointer-events-none overflow-hidden" style="display:none;">
            <div class="dv-scanner-line"></div>
        </div>
        <div id="dv-box" class="absolute border-2 rounded-sm transition-opacity duration-500" style="touch-action: none; opacity: 0;">
            @unless ($signed)
                <div id="dv-resize-handle" class="absolute -bottom-1.5 -right-1.5 h-3.5 w-3.5 rounded-full bg-white border-2 border-indigo-500" style="touch-action: none; cursor: nwse-resize;"></div>
            @endunless
        </div>
    </div>

    @unless ($signed)
        <p id="dv-scanning-label" class="hidden text-xs text-stone-500 mt-2 items-center gap-2">
            <span class="dv-scanning-dot"></span>
            {{ __('Analyse du document et proposition d\'un emplacement de signature...') }}
        </p>
    @endunless
</div>

@if (! $signed)
    <form id="dv-sign-form" method="POST" action="{{ $signRoute }}" class="hidden">
        @csrf
        <input type="hidden" id="dv-input-page" name="page_number">
        <input type="hidden" id="dv-input-x" name="pos_x">
        <input type="hidden" id="dv-input-y" name="pos_y">
        <input type="hidden" id="dv-input-w" name="box_width">
        <input type="hidden" id="dv-input-h" name="box_height">
        <input type="hidden" id="dv-input-caption" name="include_caption" value="1">
    </form>

    <label class="flex items-center gap-2 text-sm text-stone-600 mt-4">
        <input type="checkbox" id="dv-caption-checkbox" checked
               class="rounded border-stone-300 text-stone-600 focus:ring-stone-500">
        {{ __('Ajouter la mention « Signé le [date] par [nom] » sous la signature') }}
    </label>

    <div class="flex justify-end mt-4">
        <button type="button" id="dv-sign-button" @disabled(! $hasSignature)
                class="inline-flex items-center px-5 py-2.5 bg-stone-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-stone-800 disabled:opacity-40 disabled:cursor-not-allowed transition">
            {{ __('Signer ce document') }}
        </button>
    </div>
@else
    <p class="text-sm text-stone-600 mt-4">
        @if ($document->signature)
            {{ __('Signé le') }} {{ $document->signature->signed_at->format('d/m/Y H:i:s') }}
            {{ __('par') }} {{ $document->user->name }}.
        @else
            {{ __('Déposé déjà signé le') }} {{ $document->created_at->format('d/m/Y H:i:s') }}
            {{ __('par') }} {{ $document->user->name }}.
        @endif
        {{ __('Ce document ne peut plus être modifié.') }}
    </p>
@endif
