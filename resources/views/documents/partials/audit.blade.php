@if ($document->auditLogs->isNotEmpty())
    <div class="bg-white ring-1 ring-stone-200 rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-stone-100">
            <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Historique (traçabilité)') }}</span>
        </div>
        <ul class="divide-y divide-stone-100 px-6">
            @foreach ($document->auditLogs as $log)
                <li class="py-3 flex items-center gap-3">
                    <span class="h-1.5 w-1.5 rounded-full bg-stone-300 shrink-0"></span>
                    <span class="flex-1 text-sm text-stone-800">{{ match($log->action) {
                        'uploaded' => __('Document déposé'),
                        'uploaded_signed' => __('Document déposé déjà signé'),
                        'signed' => __('Document signé'),
                        'vault_viewed' => __('Consultation dans le coffre-fort'),
                        'downloaded' => __('PDF téléchargé'),
                        default => $log->action,
                    } }}</span>
                    <span class="text-xs text-stone-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif
