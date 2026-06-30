@php
    $alertTypeId = 4;
    $showKilometrage = false;
    $pageTitle = 'Alertes Contrôle Technique';
    $pageDescription = 'Centralisez les contrôles techniques et anticipez les expirations importantes.';
    $listTitle = 'Liste des alertes de contrôle technique';
    $emptyText = 'Aucune alerte de contrôle technique enregistrée pour le moment.';
@endphp

@include('alerte.partials.type-alert-page')
