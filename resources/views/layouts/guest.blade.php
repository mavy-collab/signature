<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&family=playfair-display:500,600,600i,700i&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-stone-100">
            <div>
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="w-9 h-9" />
                    <span class="font-serif italic text-2xl text-stone-900">{{ config('app.name') }}</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-8 px-6 py-8 bg-white ring-1 ring-stone-200 shadow-sm overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            <div class="mt-6 mb-6 flex flex-col items-center gap-2">
                <p class="text-xs text-stone-500">{{ __('Signature électronique tracée et sécurisée.') }}</p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('legal.notice') }}" class="text-xs text-stone-400 hover:text-stone-700">{{ __('Mentions légales') }}</a>
                    <a href="{{ route('legal.terms') }}" class="text-xs text-stone-400 hover:text-stone-700">{{ __('CGU') }}</a>
                    <a href="{{ route('legal.privacy') }}" class="text-xs text-stone-400 hover:text-stone-700">{{ __('Confidentialité') }}</a>
                </div>
            </div>
        </div>
    </body>
</html>
