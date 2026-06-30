@php
    $alertTypeId = 1;
    $showKilometrage = false;
    $pageTitle = 'Alertes Assurance';
    $pageDescription = "Suivez les échéances d'assurance et créez rapidement une nouvelle alerte pour un véhicule.";
    $listTitle = "Liste des alertes d'assurance";
    $emptyText = "Aucune alerte d'assurance enregistrée pour le moment.";
@endphp

@include('alerte.partials.type-alert-page')
