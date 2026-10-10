<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Tableau de bord') }}
            </a>

            <div>
                <h1 class="font-serif text-4xl sm:text-5xl text-stone-900">{{ __('Historique') }}</h1>
                <p class="mt-3 text-stone-500 max-w-md">
                    {{ __('Toutes les actions liées à votre signature et à vos documents, dans l\'ordre chronologique.') }}
                </p>
            </div>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl overflow-hidden">
                @if ($events->isEmpty())
                    <p class="p-6 text-sm text-stone-500">{{ __('Aucune activité pour le moment.') }}</p>
                @else
                    <div class="px-6 py-3 border-b border-stone-100">
                        <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">
                            {{ $events->total() }} {{ $events->total() > 1 ? __('évènements') : __('évènement') }}
                        </span>
                    </div>

                    <ul class="divide-y divide-stone-100">
                        @foreach ($events as $event)
                            @php
                                $isVault = $event['action'] === 'vault_viewed';
                                $isSigned = in_array($event['action'], ['signed', 'uploaded_signed'], true);
                                $iconBg = match (true) {
                                    $isSigned => 'bg-green-50 text-green-600',
                                    $isVault => 'bg-stone-100 text-stone-500',
                                    default => 'bg-stone-100 text-stone-500',
                                };
                            @endphp
                            <li class="flex items-center gap-4 px-6 py-4">
                                <span class="h-9 w-9 shrink-0 rounded-full flex items-center justify-center {{ $iconBg }}">
                                    @if ($isSigned)
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    @elseif ($isVault)
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                    @else
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    @endif
                                </span>

                                <div class="min-w-0 flex-1">
                                    @if ($event['route'])
                                        <a href="{{ route($event['route'], $event['route_params']) }}" class="text-sm font-medium text-stone-900 hover:text-stone-600 truncate block transition">
                                            {{ $event['label'] }}
                                        </a>
                                    @else
                                        <p class="text-sm font-medium text-stone-900 truncate">{{ $event['label'] }}</p>
                                    @endif
                                </div>

                                <span class="text-xs text-stone-400 whitespace-nowrap">{{ $event['created_at']->format('d/m/Y H:i:s') }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            @if ($events->hasPages())
                <div>
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
