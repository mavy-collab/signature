<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ type: '{{ old('account_type', 'individual') }}' }">
            <!-- Account Type -->
            <div class="mt-4">
                <x-input-label for="account_type" :value="__('Type de compte')" />
                <select id="account_type" name="account_type" x-model="type"
                        class="border-stone-300 focus:border-stone-500 focus:ring-stone-500 rounded-lg shadow-sm block mt-1 w-full" required>
                    <option value="individual" @selected(old('account_type', 'individual') === 'individual')>{{ __('Particulier') }}</option>
                    <option value="company" @selected(old('account_type') === 'company')>{{ __('Entreprise') }}</option>
                </select>
                <x-input-error :messages="$errors->get('account_type')" class="mt-2" />
            </div>

            <!-- Company Name (only for company accounts) -->
            <div class="mt-4" x-show="type === 'company'" x-cloak>
                <x-input-label for="company_name" :value="__('Nom de l\'entreprise')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" autocomplete="organization" />
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Terms acceptance -->
        <div class="mt-4">
            <label for="terms" class="inline-flex items-start gap-2">
                <input id="terms" type="checkbox" name="terms" value="1" :checked="old('terms') ? true : false"
                       class="mt-0.5 rounded border-stone-300 text-stone-600 shadow-sm focus:ring-stone-500" required>
                <span class="text-sm text-stone-600">
                    {{ __("J'accepte les") }}
                    <a href="{{ route('legal.terms') }}" target="_blank" class="underline hover:text-stone-900">{{ __("conditions générales d'utilisation") }}</a>
                    {{ __('et la') }}
                    <a href="{{ route('legal.privacy') }}" target="_blank" class="underline hover:text-stone-900">{{ __('politique de confidentialité') }}</a>.
                </span>
            </label>
            <x-input-error :messages="$errors->get('terms')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-stone-600 hover:text-stone-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500" href="{{ route('login') }}">
                {{ __('Déjà inscrit ?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Créer mon compte') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
