<x-public-layout :title="__('Politique de confidentialité')">
    <p class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Dernière mise à jour') }} : {{ now()->translatedFormat('d F Y') }}</p>
    <h1 class="mt-2 font-serif text-4xl text-stone-900">{{ __('Politique de confidentialité') }}</h1>

    <div class="prose prose-stone mt-8 max-w-none prose-headings:font-serif prose-h2:text-xl prose-a:text-stone-900">
        <p>
            {{ __("Cette page décrit précisément les données personnelles traitées par :app et leur usage. Elle ne mentionne que ce qui est réellement collecté par le Service — aucune donnée au-delà de celles listées ici n'est enregistrée.", ['app' => config('app.name')]) }}
        </p>

        <h2>{{ __('1. Données collectées') }}</h2>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Donnée') }}</th>
                    <th>{{ __('Finalité') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ __('Nom, adresse e-mail, mot de passe (chiffré)') }}</td>
                    <td>{{ __("Création et authentification du compte") }}</td>
                </tr>
                <tr>
                    <td>{{ __("Type de compte (particulier/entreprise) et nom d'entreprise") }}</td>
                    <td>{{ __("Personnalisation de l'affichage") }}</td>
                </tr>
                <tr>
                    <td>{{ __('Image de la signature manuscrite') }}</td>
                    <td>{{ __("Apposition sur les documents de l'utilisateur") }}</td>
                </tr>
                <tr>
                    <td>{{ __('Documents PDF déposés et leur contenu') }}</td>
                    <td>{{ __("Signature, conservation et vérification d'authenticité") }}</td>
                </tr>
                <tr>
                    <td>{{ __("Journal d'évènements (action effectuée, date et heure)") }}</td>
                    <td>{{ __('Traçabilité et historique du compte') }}</td>
                </tr>
            </tbody>
        </table>
        <p>
            {{ __("Le Service ne collecte ni adresse IP, ni données de géolocalisation, ni identifiant publicitaire, et ne dépose aucun cookie de mesure d'audience ou publicitaire.") }}
        </p>

        <h2>{{ __('2. Où sont stockées les données') }}</h2>
        <p>
            {{ __("Les données de compte et les journaux d'évènements sont stockés dans une base de données. Les documents PDF déposés ou signés sont stockés dans un espace de fichiers non accessible publiquement. L'image de la signature manuscrite est stockée dans un espace de fichiers dont l'adresse exacte n'est ni communiquée ni indexée.") }}
        </p>

        <h2>{{ __('3. Sécurité et intégrité') }}</h2>
        <p>
            {{ __("Le mot de passe est stocké sous forme hachée, jamais en clair. Une fois un document signé, son contenu final est figé : il n'est plus jamais régénéré, et son empreinte SHA-256 ainsi que son scellement cryptographique permettent de détecter toute altération ultérieure, y compris après téléchargement.") }}
        </p>

        <h2>{{ __('4. Partage des données') }}</h2>
        <p>
            {{ __("Aucune donnée n'est vendue, louée ou partagée avec des tiers à des fins commerciales. La page de vérification publique permet uniquement de confirmer, à partir d'un fichier déposé par un tiers, si celui-ci correspond exactement à un document signé sur le Service — sans exposer le contenu du document, à l'exception du nom du signataire et de la date de signature affichés lorsque la correspondance est confirmée.") }}
        </p>

        <h2>{{ __('5. Durée de conservation') }}</h2>
        <p>
            {{ __("Les données sont conservées tant que le compte existe. La suppression du compte, disponible depuis la page « Profil », entraîne la suppression du compte et de ses données associées.") }}
        </p>

        <h2>{{ __('6. Droits des utilisateurs') }}</h2>
        <p>
            {{ __("Conformément au Règlement Général sur la Protection des Données (RGPD), vous disposez d'un droit d'accès, de rectification et de suppression de vos données. Vous pouvez rectifier votre nom et votre e-mail depuis votre profil, ou supprimer l'intégralité de votre compte à tout moment.") }}
        </p>

        <h2>{{ __('7. Contact') }}</h2>
        <p>{{ __('Pour exercer vos droits ou pour toute question, voir les') }} <a href="{{ route('legal.notice') }}">{{ __('mentions légales') }}</a>.</p>
    </div>
</x-public-layout>
