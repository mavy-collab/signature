<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Tableau de bord') }}
            </a>

            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
                <div>
                    <h1 class="font-serif text-4xl sm:text-5xl text-stone-900">{{ __('Ma signature') }}</h1>
                    <p class="mt-3 text-stone-500 max-w-md">
                        {{ $signature
                            ? __('Ce tracé appose votre identité sur chacun de vos documents. Chaque usage est horodaté et conservé à des fins de traçabilité.')
                            : __('Dessinez votre signature ou importez une image. Elle est liée uniquement à votre compte et vous sert à signer vos documents.') }}
                    </p>
                </div>

                @if ($signature)
                    <a href="{{ route('signature.edit') }}" class="shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-stone-900 border border-transparent rounded-full font-medium text-sm text-white hover:bg-stone-800 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" /></svg>
                        {{ __('Modifier ma signature') }}
                    </a>
                @endif
            </div>

            @if (session('status') === 'signature-saved')
                <div class="p-4 bg-green-50 text-green-700 rounded-lg text-sm">
                    {{ __('Votre signature a été enregistrée.') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-red-50 text-red-700 rounded-lg text-sm">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            @if ($signature)
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                    <div class="lg:col-span-3 bg-white ring-1 ring-stone-200 rounded-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-stone-100">
                            <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Votre signature') }}</span>
                            <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                {{ __('Signature enregistrée') }}
                            </span>
                        </div>

                        <div class="p-6">
                            <div class="bg-stone-50 rounded-xl flex items-center justify-center py-8 px-6">
                                <img src="{{ \Illuminate\Support\Facades\Storage::url($signature->image_path) }}" alt="{{ __('Signature enregistrée') }}" class="max-w-full" style="max-height: 150px;">
                            </div>
                            <p class="mt-4 text-xs text-stone-400">
                                {{ __('Inscrite le') }} {{ $signature->created_at->format('d/m/Y à H:i') }}
                            </p>
                            <p class="mt-3 text-sm text-stone-500">
                                {{ __('Cette signature est utilisée pour signer vos documents. Elle est stockée de façon sécurisée et chaque document signé reste vérifiable grâce à son') }}
                                <a href="{{ route('document-verification.show') }}" class="underline hover:text-stone-700">{{ __('empreinte numérique') }}</a>.
                            </p>
                        </div>
                    </div>

                    <div class="lg:col-span-2 bg-white ring-1 ring-stone-200 rounded-2xl overflow-hidden">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-stone-100">
                            <span class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Historique (traçabilité)') }}</span>
                            <svg class="h-4 w-4 text-stone-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>

                        @if ($auditLogs->isEmpty())
                            <p class="p-6 text-sm text-stone-500">{{ __('Aucun évènement pour le moment.') }}</p>
                        @else
                            <ul class="divide-y divide-stone-100 px-6">
                                @foreach ($auditLogs as $log)
                                    <li class="py-3 flex items-center gap-3">
                                        <span class="h-1.5 w-1.5 rounded-full bg-stone-300 shrink-0"></span>
                                        <span class="flex-1 text-sm text-stone-800">{{ match($log->action) {
                                            'created' => __('Signature créée'),
                                            'updated' => __('Signature mise à jour'),
                                            'deleted' => __('Signature supprimée'),
                                            default => $log->action,
                                        } }}</span>
                                        <span class="text-xs text-stone-400 whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>

                <p class="text-center text-xs text-stone-400">
                    {{ __('Chaque évolution de votre signature est horodatée et conservée à des fins de traçabilité.') }}
                </p>
            @else
                <div class="max-w-3xl mx-auto bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                    @if ($wasBlank)
                        <div class="mb-6 p-4 bg-amber-50 text-amber-800 rounded-lg text-sm">
                            {{ __('La signature précédemment enregistrée était vide et ne compte pas comme une vraie signature : vous pouvez la redessiner ci-dessous.') }}
                        </div>
                    @endif

                    @include('signature.partials.pad', ['actionRoute' => route('signature.store')])
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
