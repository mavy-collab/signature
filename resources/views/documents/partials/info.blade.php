<div class="p-4 sm:p-8 bg-white ring-1 ring-stone-200 sm:rounded-2xl space-y-4">
    <header>
        <h3 class="text-lg font-medium text-stone-900">{{ __('Informations du document') }}</h3>
    </header>

    <dl class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
        <div>
            <dt class="text-stone-500">{{ __('Pages') }}</dt>
            <dd class="font-medium text-stone-900">{{ $document->page_count }}</dd>
        </div>
        <div class="col-span-3">
            <dt class="text-stone-500">{{ __('Dimensions par page (pt)') }}</dt>
            <dd class="font-medium text-stone-900">
                @foreach ($document->pages as $page)
                    <span class="inline-block mr-3">{{ __('p.') }}{{ $page['page'] }} : {{ $page['width'] }}&times;{{ $page['height'] }}</span>
                @endforeach
            </dd>
        </div>
        @if ($document->isSigned() && $document->content_hash)
            <div class="col-span-4 pt-4 border-t border-stone-100">
                <dt class="text-stone-500">{{ __('Empreinte d\'authenticité (SHA-256)') }}</dt>
                <dd class="font-mono text-xs text-stone-600 mt-1 break-all">{{ $document->content_hash }}</dd>
                <a href="{{ route('document-verification.show') }}" class="mt-2 inline-flex items-center gap-1 text-xs font-medium text-stone-900 hover:text-stone-600">
                    {{ __('Vérifier l\'authenticité de ce fichier') }} &rarr;
                </a>
            </div>
        @endif
    </dl>
</div>
