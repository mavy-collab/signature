<x-public-layout :title="__('Mentions légales')">
    <p class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Informations légales') }}</p>
    <h1 class="mt-2 font-serif text-4xl text-stone-900">{{ __('Mentions légales') }}</h1>

    <div class="mt-4 p-4 bg-amber-50 text-amber-800 rounded-lg text-sm">
        {{ __("Modèle à compléter : les champs entre crochets doivent être remplacés par les informations réelles de l'éditeur avant toute mise en production.") }}
    </div>

    <div class="prose prose-stone mt-8 max-w-none prose-headings:font-serif prose-h2:text-xl prose-a:text-stone-900">
        <h2>{{ __('Éditeur du site') }}</h2>
        <p>
            {{ __('Le site :app est édité par :', ['app' => config('app.name')]) }}<br>
            [{{ __('Nom ou raison sociale de l\'éditeur') }}]<br>
            [{{ __('Forme juridique — le cas échéant') }}]<br>
            [{{ __('Adresse du siège social') }}]<br>
            [{{ __('Numéro SIRET / SIREN — le cas échéant') }}]<br>
            [{{ __('Adresse e-mail de contact') }}]
        </p>

        <h2>{{ __('Directeur de la publication') }}</h2>
        <p>[{{ __('Nom du responsable de la publication') }}]</p>

        <h2>{{ __('Hébergement') }}</h2>
        <p>
            [{{ __("Nom de l'hébergeur") }}]<br>
            [{{ __("Adresse de l'hébergeur") }}]
        </p>

        <h2>{{ __('Propriété intellectuelle') }}</h2>
        <p>
            {{ __("L'ensemble des contenus présents sur :app (textes, éléments graphiques, logo) sont, sauf mention contraire, la propriété de son éditeur et sont protégés par le droit de la propriété intellectuelle. Toute reproduction non autorisée est interdite.", ['app' => config('app.name')]) }}
        </p>

        <h2>{{ __('Nature du service') }}</h2>
        <p>
            {!! __(':app est un outil de signature électronique en ligne. Il ne constitue pas un prestataire de services de confiance qualifié au sens du règlement eIDAS. Voir les :cgu pour le détail de la portée juridique des signatures produites par le service.', ['app' => e(config('app.name')), 'cgu' => '<a href="'.route('legal.terms').'">'.__("conditions générales d'utilisation").'</a>']) !!}
        </p>

        <h2>{{ __('Contact') }}</h2>
        <p>{{ __('Pour toute question relative au site ou à son contenu, vous pouvez écrire à :') }} [{{ __('adresse e-mail de contact') }}].</p>
    </div>
</x-public-layout>
