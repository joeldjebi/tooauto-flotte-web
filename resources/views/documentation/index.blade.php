@include('layouts.header')
@include('layouts.menu')

<style>
    .doc-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .doc-hero,
    .doc-section,
    .doc-summary {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
    }

    .doc-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 20px;
    }

    .doc-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .doc-title {
        color: #130d0d;
        font-size: 25px;
        font-weight: 900;
        margin: 0;
    }

    .doc-copy {
        color: #64748b;
        font-size: 14px;
        margin: 7px 0 0;
        max-width: 780px;
    }

    .doc-badge {
        align-items: center;
        background: #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        padding: 11px 14px;
        white-space: nowrap;
    }

    .doc-grid {
        display: grid;
        gap: 16px;
        grid-template-columns: minmax(240px, 0.8fr) minmax(0, 2fr);
    }

    .doc-summary {
        align-self: start;
        padding: 16px;
        position: sticky;
        top: 92px;
    }

    .doc-summary-title {
        color: #130d0d;
        font-size: 14px;
        font-weight: 900;
        margin-bottom: 10px;
    }

    .doc-summary a {
        align-items: center;
        border-radius: 8px;
        color: #475569;
        display: flex;
        font-size: 13px;
        font-weight: 800;
        justify-content: space-between;
        padding: 9px 10px;
    }

    .doc-summary a:hover {
        background: rgba(239, 194, 66, 0.18);
        color: #130d0d;
    }

    .doc-section {
        margin-bottom: 16px;
        overflow: hidden;
    }

    .doc-section-header {
        align-items: center;
        background: #130d0d;
        color: #ffffff;
        display: flex;
        gap: 10px;
        padding: 15px 18px;
    }

    .doc-section-icon {
        align-items: center;
        background: #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        height: 36px;
        justify-content: center;
        width: 36px;
    }

    .doc-section-title {
        font-size: 16px;
        font-weight: 900;
        margin: 0;
    }

    .doc-items {
        display: grid;
        gap: 0;
    }

    .doc-item {
        border-top: 1px solid #eef2f7;
        display: grid;
        gap: 12px;
        grid-template-columns: 230px minmax(0, 1fr);
        padding: 16px 18px;
    }

    .doc-item:first-child {
        border-top: 0;
    }

    .doc-item-name {
        color: #130d0d;
        font-size: 14px;
        font-weight: 900;
        margin: 0;
    }

    .doc-item-path {
        color: #94a3b8;
        display: block;
        font-size: 12px;
        font-weight: 800;
        margin-top: 4px;
    }

    .doc-item p {
        color: #475569;
        font-size: 13px;
        line-height: 1.6;
        margin: 0;
    }

    @media (max-width: 991px) {
        .doc-hero,
        .doc-item {
            display: block;
        }

        .doc-badge {
            margin-top: 14px;
        }

        .doc-grid {
            grid-template-columns: 1fr;
        }

        .doc-summary {
            position: static;
        }

        .doc-item-name {
            margin-bottom: 8px;
        }
    }
</style>

@php
    $sections = [
        'Accueil' => [
            'icon' => 'grid',
            'items' => [
                ['Tableau de bord', 'dashboard', 'Vue globale de la flotte: alertes importantes, véhicules concernés, chauffeurs et indicateurs rapides pour piloter les priorités.'],
            ],
        ],
        'Opérations' => [
            'icon' => 'truck',
            'items' => [
                ['Véhicules', 'vehicule.index', 'Liste, création et modification des véhicules rattachés à la flotte, avec leurs informations techniques et photos.'],
                ['Documents auto', 'autodoc.index', 'Gestion des documents liés aux véhicules: cartes grises, assurances, visites, fichiers administratifs et dates de suivi.'],
                ['Pièces & accessoires', 'index.annonce', 'Catalogue interne des pièces disponibles, avec type, marque, modèle, catégorie et photo stockée sur Wasabi.'],
            ],
        ],
        'Services flotte' => [
            'icon' => 'tool',
            'items' => [
                ['Entretien', 'alerte.entretien', 'Planification et suivi des entretiens: vidange, révision, pneus, freins, coûts et prestataires.'],
                ['Assistance', 'alerte.assistance', 'Suivi des demandes d’assistance: panne, urgence, lieu, prestataire, statut et clôture.'],
                ['Réparations & Suivi', 'alerte.reparation', 'Historique des réparations effectuées sur les véhicules, avec panne, montant, immobilisation et statut.'],
                ['Carburant & Conso.', 'alerte.carburant', 'Enregistrement des approvisionnements carburant, quantité, coût, station, kilométrage et consommation.'],
            ],
        ],
        'Alertes' => [
            'icon' => 'bell',
            'items' => [
                ['Toutes les alertes', 'alerte.index', 'Liste complète des alertes avec filtres par type, véhicule, marque et période.'],
                ['Assurance', 'alerte.assurance', 'Alertes dédiées aux échéances d’assurance des véhicules.'],
                ['Vidange', 'alerte.vidange', 'Alertes de vidange et rappels liés au kilométrage ou aux dates prévues.'],
                ['Visite technique', 'alerte.visite-technique', 'Suivi des visites techniques arrivant à échéance ou déjà expirées.'],
                ['Contrôle technique', 'alerte.controle-technique', 'Suivi des contrôles techniques par véhicule avec état de validité.'],
            ],
        ],
        'Réseau' => [
            'icon' => 'briefcase',
            'items' => [
                ['Prestataires', 'index.garage', 'Annuaire des prestataires: garages, dépanneurs, centres d’entretien et contacts utiles.'],
                ['Concessionnaires', 'index.concessionnaire', 'Liste des concessionnaires, leurs véhicules disponibles et les demandes ou rendez-vous associés.'],
                ['Offres', 'index.offre-concessionnaire', 'Gestion des offres commerciales provenant des concessionnaires.'],
            ],
        ],
        'Utilisateurs' => [
            'icon' => 'users',
            'items' => [
                ['Fonctions', 'fonction.index', 'Titres ou fonctions attribués aux chauffeurs, sans lien avec les permissions admin.'],
                ['Rôles', 'roles.index', 'Création des rôles admin et association des features visibles dans le backoffice.'],
                ['Users admin', 'admin-users.index', 'Création des utilisateurs admin rattachés au gestionnaire principal, avec attribution d’un rôle.'],
                ['Chauffeurs', 'chauffeur.index', 'Gestion des chauffeurs qui utilisent l’application mobile et peuvent être rattachés à des véhicules.'],
            ],
        ],
        'Paramètres' => [
            'icon' => 'settings',
            'items' => [
                ['Mon profil', 'profil.index', 'Modification des informations du compte connecté et de la photo de profil.'],
                ['Mot de passe', 'password.index', 'Changement sécurisé du mot de passe du compte connecté.'],
                ['Documentation', 'documentation.index', 'Guide rapide des liens du menu et de leur rôle dans le backoffice.'],
            ],
        ],
    ];
@endphp

<div class="page-wrapper doc-page">
    <div class="content">
        @include('layouts.fileariane')

        <div class="doc-hero">
            <div>
                <span class="doc-kicker">Aide backoffice</span>
                <h4 class="doc-title">Documentation du menu</h4>
                <p class="doc-copy">Cette page explique rapidement le rôle de chaque lien disponible dans le menu FLOTTE PRO.</p>
            </div>
            <span class="doc-badge">
                <i data-feather="book-open"></i>
                Guide interne
            </span>
        </div>

        <div class="doc-grid">
            <aside class="doc-summary">
                <div class="doc-summary-title">Sections</div>
                @foreach($sections as $sectionTitle => $section)
                    <a href="#doc-{{ \Illuminate\Support\Str::slug($sectionTitle) }}">
                        <span>{{ $sectionTitle }}</span>
                        <i data-feather="chevron-right"></i>
                    </a>
                @endforeach
            </aside>

            <div>
                @foreach($sections as $sectionTitle => $section)
                    <section class="doc-section" id="doc-{{ \Illuminate\Support\Str::slug($sectionTitle) }}">
                        <div class="doc-section-header">
                            <span class="doc-section-icon"><i data-feather="{{ $section['icon'] }}"></i></span>
                            <h5 class="doc-section-title">{{ $sectionTitle }}</h5>
                        </div>
                        <div class="doc-items">
                            @foreach($section['items'] as $item)
                                <div class="doc-item">
                                    <div>
                                        <h6 class="doc-item-name">{{ $item[0] }}</h6>
                                        <span class="doc-item-path">{{ $item[1] }}</span>
                                    </div>
                                    <p>{{ $item[2] }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
