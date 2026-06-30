@php
    $alertTypeId = 2;
    $showKilometrage = true;
    $pageTitle = 'Alertes Vidange';
    $pageDescription = 'Suivez les prochaines vidanges par date et kilométrage pour éviter les retards de maintenance.';
    $listTitle = 'Liste des alertes de vidange';
    $emptyText = 'Aucune alerte de vidange enregistrée pour le moment.';
@endphp

@include('alerte.partials.type-alert-page')
