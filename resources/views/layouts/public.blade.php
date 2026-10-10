<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name') }} — {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&family=playfair-display:500,600,600i,700i&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-stone-900 antialiased bg-stone-50">
        <header class="bg-white border-b border-stone-200">
            <div class="max-w-3xl mx-auto px-6 py-5 flex items-center justify-between">
                <a href="{{ route('welcome') }}" class="flex items-center gap-2">
                    <x-application-logo class="h-7 w-auto" />
                    <span class="font-serif italic text-lg text-stone-900">{{ config('app.name') }}</span>
                </a>
                <a href="{{ url()->previous() === url()->current() ? route('welcome') : url()->previous() }}" class="text-sm font-medium text-stone-500 hover:text-stone-900 transition">
                    {{ __("← Retour") }}
                </a>
            </div>
        </header>

        <main class="max-w-3xl mx-auto px-6 py-12 sm:py-16">
            {{ $slot }}
        </main>

        <footer class="border-t border-stone-200">
            <div class="max-w-3xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-400">
                <span>&copy; {{ now()->year }} {{ config('app.name') }}</span>
                <div class="flex items-center gap-4">
                    <a href="{{ route('legal.notice') }}" class="hover:text-stone-700 transition">{{ __('Mentions légales') }}</a>
                    <a href="{{ route('legal.terms') }}" class="hover:text-stone-700 transition">{{ __('CGU') }}</a>
                    <a href="{{ route('legal.privacy') }}" class="hover:text-stone-700 transition">{{ __('Confidentialité') }}</a>
                </div>
            </div>
        </footer>
    </body>
</html>
