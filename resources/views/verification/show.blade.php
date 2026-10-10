<x-guest-layout>
    <h2 class="font-serif text-2xl text-stone-900 text-center">{{ __('Vérifier un document') }}</h2>
    <p class="mt-2 text-sm text-stone-500 text-center">
        {{ __('Déposez un PDF signé sur OtoSign pour confirmer qu\'il est identique à l\'original scellé lors de la signature.') }}
    </p>

    @if ($result)
        @if ($result['match'])
            <div class="mt-6 p-4 rounded-lg bg-green-50 ring-1 ring-green-200">
                <p class="flex items-center gap-2 text-sm font-medium text-green-800">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Document authentique') }}
                </p>
                <p class="mt-2 text-sm text-green-700">
                    {{ __('Ce fichier est identique, au bit près, au document scellé lors de sa signature. Aucune modification n\'a été détectée depuis.') }}
                </p>
                <dl class="mt-4 text-sm text-green-900 space-y-1">
                    <div class="flex justify-between gap-4">
                        <dt class="text-green-700">{{ __('Document') }}</dt>
                        <dd class="font-medium text-right">{{ $result['document']->original_name }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-green-700">{{ __('Signé par') }}</dt>
                        <dd class="font-medium text-right">
                            {{ $result['document']->user->account_type === 'company' && $result['document']->user->company_name
                                ? $result['document']->user->company_name
                                : $result['document']->user->name }}
                        </dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-green-700">{{ __('Signé le') }}</dt>
                        <dd class="font-medium text-right">
                            {{ ($result['document']->signature->signed_at ?? $result['document']->updated_at)->format('d/m/Y à H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>
        @else
            <div class="mt-6 p-4 rounded-lg bg-amber-50 ring-1 ring-amber-200">
                <p class="flex items-center gap-2 text-sm font-medium text-amber-800">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    {{ __('Authenticité non confirmée') }}
                </p>
                <p class="mt-2 text-sm text-amber-700">
                    {{ __('Ce fichier ne correspond à aucun document signé sur OtoSign, ou son contenu a été modifié depuis sa signature.') }}
                </p>
            </div>
        @endif
    @endif

    <form method="POST" action="{{ route('document-verification.check') }}" enctype="multipart/form-data" class="mt-6">
        @csrf

        <x-input-label for="file" :value="__('Fichier PDF à vérifier')" />
        <input id="file" type="file" name="file" accept="application/pdf,.pdf" required
               class="block mt-1 w-full text-sm text-stone-600 border border-stone-300 rounded-md file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-medium file:bg-stone-100 file:text-stone-700 hover:file:bg-stone-200">
        <x-input-error :messages="$errors->get('file')" class="mt-2" />

        <div class="flex justify-end mt-6">
            <x-primary-button type="submit">{{ __('Vérifier') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
