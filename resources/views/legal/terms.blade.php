<x-public-layout :title="__('Conditions générales d\'utilisation')">
    <p class="text-xs font-medium tracking-wider text-stone-400 uppercase">{{ __('Dernière mise à jour') }} : {{ now()->translatedFormat('d F Y') }}</p>
    <h1 class="mt-2 font-serif text-4xl text-stone-900">{{ __("Conditions générales d'utilisation") }}</h1>

    <div class="prose prose-stone mt-8 max-w-none prose-headings:font-serif prose-h2:text-xl prose-a:text-stone-900">
        <h2>{{ __('1. Objet') }}</h2>
        <p>
            {{ __("Les présentes conditions générales d'utilisation (« CGU ») régissent l'accès et l'utilisation de :app (« le Service »), une plateforme permettant à ses utilisateurs de créer une signature manuscrite numérisée, de l'apposer sur des documents PDF, de conserver ces documents dans un espace protégé et d'en vérifier l'authenticité.", ['app' => config('app.name')]) }}
        </p>

        <h2>{{ __('2. Comptes utilisateurs') }}</h2>
        <p>
            {{ __("L'inscription est ouverte aux particuliers et aux entreprises. Chaque compte est personnel et rattaché à une adresse e-mail unique. L'utilisateur est responsable de la confidentialité de ses identifiants et de toute action réalisée depuis son compte.") }}
        </p>

        <h2>{{ __('3. Nature de la signature électronique produite') }}</h2>
        <p>
            {{ __("La signature produite par :app est une signature électronique dite « simple » au sens du règlement (UE) n° 910/2014 (« eIDAS »). Elle associe :", ['app' => config('app.name')]) }}
        </p>
        <ul>
            <li>{{ __('le tracé manuscrit numérisé de l\'utilisateur, apposé visuellement sur le document ;') }}</li>
            <li>{{ __("une empreinte cryptographique (SHA-256) du document final, permettant d'en vérifier l'intégrité a posteriori via la page de vérification publique ;") }}</li>
            <li>{{ __("un scellement cryptographique du fichier PDF au moyen d'un certificat propre à la plateforme, reconnaissable par les lecteurs PDF standards ;") }}</li>
            <li>{{ __("un journal horodaté des actions effectuées sur le document et la signature.") }}</li>
        </ul>
        <p>
            {{ __(":app n'est pas un prestataire de services de confiance qualifié. Le Service ne procède à aucune vérification d'identité des utilisateurs (KYC) et ne délivre donc pas de signature électronique qualifiée ou avancée au sens du règlement eIDAS. La valeur probante d'un document signé via le Service dépendra du contexte et pourra, le cas échéant, être contestée devant un tribunal ; il appartient à l'utilisateur d'apprécier si ce niveau de signature est suffisant pour son usage.", ['app' => config('app.name')]) }}
        </p>

        <h2>{{ __('4. Fonctionnement du Service') }}</h2>
        <p>
            {{ __("L'utilisateur enregistre une signature (par tracé manuscrit ou import d'image), dépose un document PDF, puis positionne et appose sa signature. Une fois un document signé, il n'est plus modifiable : le fichier final est généré une seule fois et ses octets ne changent plus jamais, ce qui permet de garantir dans le temps que le document consulté ou téléchargé est identique à celui signé à l'origine.") }}
        </p>

        <h2>{{ __('5. Coffre-fort et conservation') }}</h2>
        <p>
            {{ __("Les documents signés sont conservés dans un espace (« coffre-fort ») dont l'accès requiert une reconfirmation du mot de passe du compte. L'utilisateur reste responsable de la conservation de ses documents sur la durée qu'il juge nécessaire à ses besoins probatoires ou légaux.") }}
        </p>

        <h2>{{ __('6. Usage autorisé') }}</h2>
        <p>{{ __("L'utilisateur s'engage à :") }}</p>
        <ul>
            <li>{{ __("n'utiliser sa signature que pour ses propres documents ou ceux qu'il est légitimement autorisé à signer ;") }}</li>
            <li>{{ __("ne pas tenter de contourner les mesures de sécurité du Service ;") }}</li>
            <li>{{ __("ne pas déposer de documents dont le contenu est illicite.") }}</li>
        </ul>

        <h2>{{ __('7. Responsabilité') }}</h2>
        <p>
            {{ __("Le Service est fourni « en l'état ». L'éditeur ne saurait être tenu responsable d'une indisponibilité temporaire, d'une perte de données résultant d'un cas de force majeure, ou d'un usage du Service non conforme aux présentes CGU.") }}
        </p>

        <h2>{{ __('8. Résiliation') }}</h2>
        <p>
            {{ __("L'utilisateur peut supprimer son compte à tout moment depuis la page « Profil », entraînant la suppression de ses données conformément à la politique de confidentialité.") }}
        </p>

        <h2>{{ __('9. Modification des CGU') }}</h2>
        <p>
            {{ __("Les présentes CGU peuvent être modifiées à tout moment. La version en vigueur est celle publiée sur cette page.") }}
        </p>

        <h2>{{ __('10. Contact') }}</h2>
        <p>{{ __('Pour toute question, voir les') }} <a href="{{ route('legal.notice') }}">{{ __('mentions légales') }}</a>.</p>
    </div>
</x-public-layout>
