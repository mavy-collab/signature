<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} — Signature électronique tracée</title>
        <meta name="description" content="Créez un compte particulier ou entreprise, enregistrez votre signature et signez vos documents en toute sécurité. Chaque action est tracée, chaque document n'est signé qu'une seule fois.">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&family=playfair-display:500,600,600i,700i&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white text-stone-900">

        <!-- Hero -->
        <div class="relative bg-stone-950 overflow-hidden rounded-b-[2.5rem]">
            <!-- Background video -->
            <video
                class="absolute inset-0 w-full h-full object-cover"
                src="{{ asset('videos/demo.mp4') }}"
                autoplay
                muted
                loop
                playsinline
                preload="metadata"
            >
            </video>
            <!-- Scrim for text legibility -->
            <div class="absolute inset-0 bg-stone-950/80"></div>

            <div class="relative z-10">
                <header class="max-w-7xl mx-auto px-6 lg:px-8 py-6 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-application-logo class="h-8 w-auto" />
                        <span class="text-white font-serif italic text-lg">{{ config('app.name') }}</span>
                    </div>

                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-5 py-2.5 rounded-full text-sm font-medium text-stone-900 bg-white hover:bg-stone-100 transition">
                                {{ __('Accéder au tableau de bord') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-2 text-sm font-medium text-stone-300 hover:text-white transition">
                                {{ __('Se connecter') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-2.5 rounded-full text-sm font-medium text-stone-900 bg-white hover:bg-stone-100 transition">
                                    {{ __('Créer un compte') }}
                                </a>
                            @endif
                        @endauth
                    </nav>
                </header>

                <div class="max-w-4xl mx-auto px-6 lg:px-8 pt-16 pb-32 text-center">
                    <p class="inline-flex items-center gap-2 text-xs font-medium text-stone-300 bg-stone-500/10 ring-1 ring-inset ring-stone-400/30 rounded-full px-3 py-1 mb-6">
                        {{ __('Particuliers & entreprises') }}
                    </p>
                    <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-white leading-tight">
                        {{ __('La signature électronique,') }}<br>
                        <span class="font-serif italic text-white">{{ __('simple et intégralement tracée') }}</span>
                    </h1>
                    <p class="mt-6 text-lg text-stone-300 max-w-2xl mx-auto">
                        {{ __('Créez votre compte, enregistrez votre signature manuscrite, et signez vos documents PDF en quelques clics. Chaque document n\'est signé qu\'une seule fois, puis conservé dans un coffre-fort protégé par mot de passe.') }}
                    </p>
                    <div class="mt-10 flex items-center justify-center gap-4">
                        @auth
                            <a href="{{ route('documents.index') }}" class="inline-flex items-center px-6 py-3 rounded-full text-sm font-medium text-stone-900 bg-white hover:bg-stone-100 shadow-lg shadow-black/20 transition">
                                {{ __('Voir mes documents') }}
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 rounded-full text-sm font-medium text-stone-900 bg-white hover:bg-stone-100 shadow-lg shadow-black/20 transition">
                                {{ __('Créer un compte gratuitement') }}
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 rounded-full text-sm font-medium text-white ring-1 ring-inset ring-white/30 hover:bg-white/10 transition">
                                {{ __('Se connecter') }}
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <!-- How it works -->
        <div class="max-w-7xl mx-auto px-6 lg:px-8 py-24">
            <div class="max-w-2xl mx-auto text-center">
                <p class="text-xs font-medium tracking-widest text-stone-400 uppercase">{{ __('Comment ça marche') }}</p>
                <h2 class="mt-3 text-3xl font-semibold tracking-tight text-stone-900">
                    {{ __('Trois étapes,') }} <span class="font-serif italic">{{ __('un document scellé') }}</span>
                </h2>
            </div>

            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-12 relative">
                <div class="hidden md:block absolute top-6 left-[16.5%] right-[16.5%] h-px bg-stone-200"></div>

                <div class="relative text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-stone-900 text-white flex items-center justify-center font-serif italic text-lg relative z-10">1</div>
                    <div class="mx-auto mt-5 h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2.5 2.5 0 113.536 3.536L12.536 14.5H9V11z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h4" /></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-stone-900">{{ __('Enregistrez votre signature') }}</h3>
                    <p class="mt-2 text-sm text-stone-500">{{ __('Dessinez-la une fois : elle est stockée de façon sécurisée et réutilisée pour tous vos documents.') }}</p>
                </div>

                <div class="relative text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-stone-900 text-white flex items-center justify-center font-serif italic text-lg relative z-10">2</div>
                    <div class="mx-auto mt-5 h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 7.5L12 3m0 0L7.5 7.5M12 3v13.5" /></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-stone-900">{{ __('Téléversez votre document') }}</h3>
                    <p class="mt-2 text-sm text-stone-500">{{ __('Déposez un PDF : la plateforme l\'analyse et propose automatiquement un emplacement de signature.') }}</p>
                </div>

                <div class="relative text-center">
                    <div class="mx-auto h-12 w-12 rounded-full bg-stone-900 text-white flex items-center justify-center font-serif italic text-lg relative z-10">3</div>
                    <div class="mx-auto mt-5 h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.745 3.745 0 013.296-1.043A3.745 3.745 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 013.296 1.043 3.745 3.745 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                    </div>
                    <h3 class="mt-4 font-semibold text-stone-900">{{ __('Signez et récupérez-le scellé') }}</h3>
                    <p class="mt-2 text-sm text-stone-500">{{ __('Ajustez l\'emplacement si besoin, signez en un clic : le document est scellé et rangé dans votre coffre-fort.') }}</p>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="bg-stone-50 border-y border-stone-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-24">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-6 bg-white rounded-2xl ring-1 ring-stone-200 hover:ring-stone-300 hover:shadow-sm transition">
                        <div class="h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                        </div>
                        <h3 class="font-semibold text-stone-900">{{ __('Comptes particuliers & entreprises') }}</h3>
                        <p class="mt-2 text-sm text-stone-500">{{ __('Chaque compte est personnel : seul son propriétaire peut créer ou utiliser sa signature.') }}</p>
                    </div>

                    <div class="p-6 bg-white rounded-2xl ring-1 ring-stone-200 hover:ring-stone-300 hover:shadow-sm transition">
                        <div class="h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6.232-6.232a2.5 2.5 0 113.536 3.536L12.536 14.5H9V11z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h4" /></svg>
                        </div>
                        <h3 class="font-semibold text-stone-900">{{ __('Signature manuscrite enregistrée') }}</h3>
                        <p class="mt-2 text-sm text-stone-500">{{ __('Dessinez votre signature une fois, elle est stockée de façon sécurisée et réutilisée pour chaque document.') }}</p>
                    </div>

                    <div class="p-6 bg-white rounded-2xl ring-1 ring-stone-200 hover:ring-stone-300 hover:shadow-sm transition">
                        <div class="h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                        </div>
                        <h3 class="font-semibold text-stone-900">{{ __('Emplacement proposé automatiquement') }}</h3>
                        <p class="mt-2 text-sm text-stone-500">{{ __('La plateforme analyse le document et suggère une zone de signature, ajustable et redimensionnable par glisser-déposer.') }}</p>
                    </div>

                    <div class="p-6 bg-white rounded-2xl ring-1 ring-stone-200 hover:ring-stone-300 hover:shadow-sm transition">
                        <div class="h-10 w-10 rounded-lg bg-stone-50 text-stone-600 flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        </div>
                        <h3 class="font-semibold text-stone-900">{{ __('Coffre-fort protégé') }}</h3>
                        <p class="mt-2 text-sm text-stone-500">{{ __('Un document n\'est signé qu\'une seule fois, puis rangé dans un espace qui redemande votre mot de passe.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trust / security -->
        <div class="bg-stone-950">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-20">
                <div class="max-w-2xl mx-auto text-center">
                    <p class="text-xs font-medium tracking-widest text-stone-400 uppercase">{{ __('Sécurité & traçabilité') }}</p>
                    <h2 class="mt-3 text-3xl font-semibold tracking-tight text-white">
                        {{ __('Une preuve,') }} <span class="font-serif italic">{{ __('pas une simple image') }}</span>
                    </h2>
                </div>

                <div class="mt-14 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <div class="text-center sm:text-left">
                        <div class="mx-auto sm:mx-0 h-10 w-10 rounded-lg bg-white/10 text-white flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                        </div>
                        <h3 class="font-semibold text-white">{{ __('Scellement PAdES') }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ __('Chaque document signé reçoit un certificat électronique reconnu nativement par les lecteurs PDF.') }}</p>
                    </div>

                    <div class="text-center sm:text-left">
                        <div class="mx-auto sm:mx-0 h-10 w-10 rounded-lg bg-white/10 text-white flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0119.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 004.5 10.5a7.464 7.464 0 01-1.15 3.993m1.989 3.559A11.209 11.209 0 008.25 10.5a3.75 3.75 0 117.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 01-3.6 9.75m6.633-4.596a18.666 18.666 0 01-2.485 5.33" /></svg>
                        </div>
                        <h3 class="font-semibold text-white">{{ __('Empreinte vérifiable') }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ __('Une empreinte SHA-256 unique permet de vérifier l\'intégrité de n\'importe quel document signé.') }}</p>
                    </div>

                    <div class="text-center sm:text-left">
                        <div class="mx-auto sm:mx-0 h-10 w-10 rounded-lg bg-white/10 text-white flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="font-semibold text-white">{{ __('Journal d\'audit horodaté') }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ __('Chaque action — dépôt, signature, consultation — est enregistrée avec sa date et son heure précises.') }}</p>
                    </div>

                    <div class="text-center sm:text-left">
                        <div class="mx-auto sm:mx-0 h-10 w-10 rounded-lg bg-white/10 text-white flex items-center justify-center mb-4">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                        </div>
                        <h3 class="font-semibold text-white">{{ __('Coffre-fort protégé') }}</h3>
                        <p class="mt-2 text-sm text-stone-400">{{ __('Vos documents signés sont rangés dans un espace qui redemande votre mot de passe à chaque accès.') }}</p>
                    </div>
                </div>

                <div class="mt-14 text-center">
                    <a href="{{ route('document-verification.show') }}" class="inline-flex items-center px-6 py-3 rounded-full text-sm font-medium text-white ring-1 ring-inset ring-white/30 hover:bg-white/10 transition">
                        {{ __('Vérifier un document') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="border-t border-stone-100">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-stone-500 text-sm">
                    <x-application-logo class="h-5 w-auto" />
                    {{ config('app.name') }}
                </div>
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                    <a href="{{ route('legal.notice') }}" class="text-xs text-stone-400 hover:text-stone-700">{{ __('Mentions légales') }}</a>
                    <a href="{{ route('legal.terms') }}" class="text-xs text-stone-400 hover:text-stone-700">{{ __('CGU') }}</a>
                    <a href="{{ route('legal.privacy') }}" class="text-xs text-stone-400 hover:text-stone-700">{{ __('Confidentialité') }}</a>
                </div>
            </div>
        </footer>
    </body>
</html>
