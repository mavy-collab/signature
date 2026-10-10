<nav x-data="{ open: false }" class="bg-white border-b border-stone-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <x-application-logo class="block h-7 w-auto" />
                        <span class="font-serif italic text-lg text-stone-900 hidden sm:inline">{{ config('app.name') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Tableau de bord') }}
                    </x-nav-link>
                    <x-nav-link :href="route('signature.show')" :active="request()->routeIs('signature.show')">
                        {{ __('Ma signature') }}
                    </x-nav-link>
                    <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                        {{ __('Documents') }}
                    </x-nav-link>
                    <x-nav-link :href="route('vault.index')" :active="request()->routeIs('vault.*')">
                        {{ __('Coffre-fort') }}
                    </x-nav-link>
                    <x-nav-link :href="route('history.index')" :active="request()->routeIs('history.*')">
                        {{ __('Historique') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-3 px-2 py-1.5 rounded-lg hover:bg-stone-100 focus:outline-none transition ease-in-out duration-150">
                            <span class="text-sm text-stone-700 leading-tight text-right">
                                <span class="block font-medium text-stone-900">{{ Auth::user()->name }}</span>
                                @if (Auth::user()->account_type === 'company' && Auth::user()->company_name)
                                    <span class="block text-xs text-stone-400">{{ Auth::user()->company_name }}</span>
                                @endif
                            </span>
                            <span class="h-8 w-8 rounded-full bg-stone-200 flex items-center justify-center text-xs font-semibold text-stone-600 shrink-0">
                                {{ Str::of(Auth::user()->name)->explode(' ')->map(fn ($p) => Str::substr($p, 0, 1))->take(2)->implode('') }}
                            </span>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-stone-400 hover:text-stone-900 hover:bg-stone-100 focus:outline-none focus:bg-stone-100 focus:text-stone-900 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Tableau de bord') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('signature.show')" :active="request()->routeIs('signature.show')">
                {{ __('Ma signature') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                {{ __('Documents') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('vault.index')" :active="request()->routeIs('vault.*')">
                {{ __('Coffre-fort') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('history.index')" :active="request()->routeIs('history.*')">
                {{ __('Historique') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-stone-200">
            <div class="px-4">
                <div class="font-medium text-base text-stone-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-stone-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profil') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Déconnexion') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
