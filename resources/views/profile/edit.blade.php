<x-app-layout>
    <div class="py-10 sm:py-14">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-medium tracking-wider text-stone-400 hover:text-stone-600 uppercase transition">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" /></svg>
                {{ __('Tableau de bord') }}
            </a>

            <h1 class="font-serif text-4xl sm:text-5xl text-stone-900">{{ __('Profil') }}</h1>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>

            <div class="bg-white ring-1 ring-stone-200 rounded-2xl p-6 sm:p-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
