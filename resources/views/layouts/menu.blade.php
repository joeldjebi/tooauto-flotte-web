@php
    $currentMenu = $menu ?? '';
    $hasMenu = $currentMenu !== '';
    $currentPath = trim(request()->path(), '/');
    $pathStartsWith = fn (...$prefixes) => collect($prefixes)->contains(fn ($prefix) => $currentPath === trim($prefix, '/') || str_starts_with($currentPath, trim($prefix, '/') . '/'));
    $currentUser = auth()->user();
    $isFleetAdmin = $currentUser && method_exists($currentUser, 'isMainAdmin')
        ? $currentUser->isMainAdmin()
        : ((string) ($currentUser->role ?? '') === '01' || (int) ($currentUser->role ?? 0) === 1);
    $assignedFeatureKeys = collect();

    if (!$isFleetAdmin && $currentUser && \Illuminate\Support\Facades\Schema::hasTable('fleet_role_menu_features') && !empty($currentUser->fleet_role_id)) {
        $assignedFeatureKeys = \Illuminate\Support\Facades\DB::table('fleet_role_menu_features')
            ->where('fleet_role_id', $currentUser->fleet_role_id)
            ->pluck('menu_feature_key');
    }

    $canAccessFeature = fn (string $feature) => $isFleetAdmin || $assignedFeatureKeys->contains($feature);

    $dashboardActive = $currentMenu === 'dashboard' || (!$hasMenu && (request()->routeIs('dashboard') || in_array($currentPath, ['', 'dashboard'], true)));
    $vehiculesActive = in_array($currentMenu, ['véhicules', 'vehicules'], true) || (!$hasMenu && (request()->routeIs('vehicule.*') || $pathStartsWith('vehicule', 'liste-des-vehicules', 'add-vehicules', 'edit-vehicules')));
    $autodocsActive = $currentMenu === 'autodocs' || (!$hasMenu && (request()->routeIs('autodoc.*') || $pathStartsWith('liste-des-autodocs', 'add-autodocs', 'store-autodocs', 'update-autodocs')));
    $piecesActive = in_array($currentMenu, ['piece-auto', 'annonces'], true) || (!$hasMenu && (request()->routeIs('index.annonce', 'add.annonce', 'edit.annonce', 'store.annonce', 'update.annonce', 'destroy.annonce') || $pathStartsWith('index/annonce', 'add/annonce', 'edit/annonce', 'store/annonce', 'update/annonce', 'destroy/annonce')));
    $articlesActive = $currentMenu === 'article-auto' || (!$hasMenu && request()->routeIs('index.article', 'store.article', 'update.article', 'destroy.article'));
    $entretiensActive = $currentMenu === 'entretiens' || (!$hasMenu && request()->routeIs('alerte.entretien*'));
    $assistancesActive = $currentMenu === 'assistances' || (!$hasMenu && request()->routeIs('alerte.assistance*'));
    $reparationsActive = $currentMenu === 'reparations' || (!$hasMenu && request()->routeIs('alerte.reparation*'));
    $carburantsActive = $currentMenu === 'carburants' || (!$hasMenu && request()->routeIs('alerte.carburant*'));
    $alertsActive = in_array($currentMenu, ['alertes', 'alerte'], true) || (!$hasMenu && request()->routeIs('alerte.index'));
    $assuranceAlertsActive = request()->routeIs('alerte.assurance');
    $vidangeAlertsActive = request()->routeIs('alerte.vidange');
    $visiteTechniqueAlertsActive = request()->routeIs('alerte.visite-technique');
    $controleTechniqueAlertsActive = request()->routeIs('alerte.controle-technique');
    $garagesActive = $currentMenu === 'garage-auto' || (!$hasMenu && request()->routeIs('index.garage', 'store.garage', 'update.garage', 'destroy.garage'));
    $concessionnairesActive = in_array($currentMenu, ['index-concessionnaire', 'rdv-concessionnaire'], true) || (!$hasMenu && request()->routeIs('index.concessionnaire', 'index.concessionnaire-vehicule', 'rdv.concessionnaire', 'store.concessionnaire-rdv', 'destroy.concessionnaire-rdv'));
    $offresActive = $currentMenu === 'index-offre-concessionnaire' || (!$hasMenu && request()->routeIs('index.offre-concessionnaire'));
    $demandesConcessionnairesActive = $currentMenu === 'demande-concessionnaire' || (!$hasMenu && request()->routeIs('index.demande-concessionnaire', 'store.concessionnaire-demande', 'update.concessionnaire-demande', 'destroy.concessionnaire-demande'));
    $annoncesEnvoyeesActive = $currentMenu === 'annonces-envoyees' || (!$hasMenu && request()->routeIs('annonce.sent'));
    $fonctionsActive = $currentMenu === 'fonction' || (!$hasMenu && request()->routeIs('fonction.*'));
    $rolesActive = $currentMenu === 'roles' || (!$hasMenu && request()->routeIs('roles.*'));
    $adminUsersActive = $currentMenu === 'admin-users' || (!$hasMenu && request()->routeIs('admin-users.*'));
    $chauffeursActive = $currentMenu === 'chauffeurs' || (!$hasMenu && request()->routeIs('chauffeur.*'));
    $profilActive = $currentMenu === 'profil' || (!$hasMenu && request()->routeIs('profil.*'));
    $passwordActive = $currentMenu === 'password' || (!$hasMenu && request()->routeIs('password.*'));
    $documentationActive = $currentMenu === 'documentation' || (!$hasMenu && request()->routeIs('documentation.*'));

    $operationsVisible = $canAccessFeature('vehicules') || $canAccessFeature('autodocs') || $canAccessFeature('pieces') || $canAccessFeature('articles');
    $servicesVisible = $canAccessFeature('entretiens') || $canAccessFeature('assistances') || $canAccessFeature('reparations') || $canAccessFeature('carburants');
    $alertsVisible = $canAccessFeature('alertes') || $canAccessFeature('alerte_assurance') || $canAccessFeature('alerte_vidange') || $canAccessFeature('alerte_visite') || $canAccessFeature('alerte_controle');
    $networkVisible = $canAccessFeature('prestataires') || $canAccessFeature('concessionnaires') || $canAccessFeature('offres') || $canAccessFeature('demandes_concessionnaires') || $canAccessFeature('annonces_envoyees');
    $usersVisible = $canAccessFeature('fonctions') || $canAccessFeature('roles') || $canAccessFeature('admin_users') || $canAccessFeature('utilisateurs');
    $settingsVisible = $canAccessFeature('profil') || $canAccessFeature('password');
    $helpVisible = $canAccessFeature('documentation') || ($currentUser && empty($currentUser->parent_gestionnaire_id));
@endphp

<style>
    .premium-sidebar {
        background: #130d0d;
        border-right: 0;
        box-shadow: 10px 0 28px rgba(19, 13, 13, 0.16);
    }

    .premium-sidebar .sidebar-inner {
        background: #130d0d;
        padding: 14px 10px 18px;
    }

    .premium-sidebar .fleet-sidebar-brand {
        align-items: center;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        display: flex;
        gap: 12px;
        margin: 4px 0 18px;
        padding: 12px;
    }

    .premium-sidebar .fleet-brand-mark {
        align-items: center;
        background: #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        height: 38px;
        justify-content: center;
        width: 38px;
    }

    .premium-sidebar .fleet-brand-title {
        color: #ffffff;
        display: block;
        font-size: 14px;
        font-weight: 850;
        line-height: 1.1;
    }

    .premium-sidebar .fleet-brand-subtitle {
        color: #94a3b8;
        display: block;
        font-size: 11px;
        font-weight: 700;
        margin-top: 3px;
    }

    .premium-sidebar .submenu-hdr {
        color: #ffffff !important;
        font-size: 11px;
        font-weight: 900;
        margin: 18px 10px 8px;
        padding: 0;
        text-transform: uppercase;
    }

    .premium-sidebar .sidebar-menu ul {
        padding: 0;
    }

    .premium-sidebar .sidebar-menu ul li {
        margin-bottom: 3px;
        position: relative;
    }

    .premium-sidebar .sidebar-menu ul li a {
        align-items: center;
        border-radius: 8px;
        color: #e5e7eb !important;
        display: flex;
        font-size: 14px;
        font-weight: 700;
        gap: 11px;
        min-height: 42px;
        padding: 9px 11px;
        transition: background 0.18s ease, color 0.18s ease, transform 0.18s ease;
    }

    .premium-sidebar .sidebar-menu ul li a span {
        color: inherit !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .premium-sidebar .sidebar-menu ul li a:hover {
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff !important;
        transform: translateX(2px);
    }

    .premium-sidebar .sidebar-menu ul li a i,
    .premium-sidebar .sidebar-menu ul li a svg {
        align-items: center;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        color: #94a3b8;
        display: inline-flex;
        flex: 0 0 34px;
        height: 34px;
        justify-content: center;
        margin: 0;
        padding: 8px;
        width: 34px;
    }

    .premium-sidebar .sidebar-menu ul li.active > a,
    .premium-sidebar .sidebar-menu ul li > a.active {
        background: #efc242 !important;
        color: #130d0d !important;
        box-shadow: 0 12px 22px rgba(239, 194, 66, 0.22);
    }

    .premium-sidebar .sidebar-menu ul li.active > a i,
    .premium-sidebar .sidebar-menu ul li.active > a svg,
    .premium-sidebar .sidebar-menu ul li > a.active i,
    .premium-sidebar .sidebar-menu ul li > a.active svg {
        background: rgba(19, 13, 13, 0.14);
        border-color: rgba(19, 13, 13, 0.14);
        color: #130d0d;
    }
</style>

<div class="sidebar premium-sidebar" id="sidebar">
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <div class="fleet-sidebar-brand">
                <span class="fleet-brand-mark"><i data-feather="truck"></i></span>
                <span>
                    <span class="fleet-brand-title">FLOTTE PRO</span>
                    <span class="fleet-brand-subtitle">Backoffice</span>
                </span>
            </div>

            <ul>
                @if($canAccessFeature('dashboard'))
                <li class="{{ $dashboardActive ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="{{ $dashboardActive ? 'active' : '' }}">
                        <i data-feather="grid"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                @endif

                @if($operationsVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Opérations</h6>
                    <ul>
                        @if($canAccessFeature('vehicules'))
                        <li class="{{ $vehiculesActive ? 'active' : '' }}">
                            <a href="{{ route('vehicule.index') }}" class="{{ $vehiculesActive ? 'active' : '' }}"><i data-feather="truck"></i><span>Véhicules</span></a>
                        </li>
                        @endif
                        @if($canAccessFeature('autodocs'))
                        <li class="{{ $autodocsActive ? 'active' : '' }}">
                            <a href="{{ route('autodoc.index') }}" class="{{ $autodocsActive ? 'active' : '' }}"><i data-feather="file-text"></i><span>Documents auto</span></a>
                        </li>
                        @endif
                        @if($canAccessFeature('pieces'))
                        <li class="{{ $piecesActive ? 'active' : '' }}">
                            <a href="{{ route('index.annonce') }}" class="{{ $piecesActive ? 'active' : '' }}"><i data-feather="package"></i><span>Pièces & accessoires</span></a>
                        </li>
                        @endif
                        @if($canAccessFeature('articles'))
                        <li class="{{ $articlesActive ? 'active' : '' }}">
                            <a href="{{ route('index.article') }}" class="{{ $articlesActive ? 'active' : '' }}"><i data-feather="archive"></i><span>Articles</span></a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($servicesVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Services flotte</h6>
                    <ul>
                        @if($canAccessFeature('entretiens'))
                        <li class="{{ $entretiensActive ? 'active' : '' }}"><a href="{{ route('alerte.entretien') }}" class="{{ $entretiensActive ? 'active' : '' }}"><i data-feather="tool"></i><span>Entretien</span></a></li>
                        @endif
                        @if($canAccessFeature('assistances'))
                        <li class="{{ $assistancesActive ? 'active' : '' }}"><a href="{{ route('alerte.assistance') }}" class="{{ $assistancesActive ? 'active' : '' }}"><i data-feather="life-buoy"></i><span>Assistance</span></a></li>
                        @endif
                        @if($canAccessFeature('reparations'))
                        <li class="{{ $reparationsActive ? 'active' : '' }}"><a href="{{ route('alerte.reparation') }}" class="{{ $reparationsActive ? 'active' : '' }}"><i data-feather="settings"></i><span>Réparations & Suivi</span></a></li>
                        @endif
                        @if($canAccessFeature('carburants'))
                        <li class="{{ $carburantsActive ? 'active' : '' }}"><a href="{{ route('alerte.carburant') }}" class="{{ $carburantsActive ? 'active' : '' }}"><i data-feather="droplet"></i><span>Carburant & Conso.</span></a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($alertsVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Alertes</h6>
                    <ul>
                        @if($canAccessFeature('alertes'))
                        <li class="{{ $alertsActive ? 'active' : '' }}"><a href="{{ route('alerte.index') }}" class="{{ $alertsActive ? 'active' : '' }}"><i data-feather="bell"></i><span>Toutes les alertes</span></a></li>
                        @endif
                        @if($canAccessFeature('alerte_assurance'))
                        <li class="{{ $assuranceAlertsActive ? 'active' : '' }}"><a href="{{ route('alerte.assurance') }}" class="{{ $assuranceAlertsActive ? 'active' : '' }}"><i data-feather="shield"></i><span>Assurance</span></a></li>
                        @endif
                        @if($canAccessFeature('alerte_vidange'))
                        <li class="{{ $vidangeAlertsActive ? 'active' : '' }}"><a href="{{ route('alerte.vidange') }}" class="{{ $vidangeAlertsActive ? 'active' : '' }}"><i data-feather="refresh-cw"></i><span>Vidange</span></a></li>
                        @endif
                        @if($canAccessFeature('alerte_visite'))
                        <li class="{{ $visiteTechniqueAlertsActive ? 'active' : '' }}"><a href="{{ route('alerte.visite-technique') }}" class="{{ $visiteTechniqueAlertsActive ? 'active' : '' }}"><i data-feather="clipboard"></i><span>Visite technique</span></a></li>
                        @endif
                        @if($canAccessFeature('alerte_controle'))
                        <li class="{{ $controleTechniqueAlertsActive ? 'active' : '' }}"><a href="{{ route('alerte.controle-technique') }}" class="{{ $controleTechniqueAlertsActive ? 'active' : '' }}"><i data-feather="check-square"></i><span>Contrôle technique</span></a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($networkVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Réseau</h6>
                    <ul>
                        @if($canAccessFeature('prestataires'))
                        <li class="{{ $garagesActive ? 'active' : '' }}"><a href="{{ route('index.garage') }}" class="{{ $garagesActive ? 'active' : '' }}"><i data-feather="tool"></i><span>Prestataires</span></a></li>
                        @endif
                        @if($canAccessFeature('concessionnaires'))
                        <li class="{{ $concessionnairesActive ? 'active' : '' }}"><a href="{{ route('index.concessionnaire') }}" class="{{ $concessionnairesActive ? 'active' : '' }}"><i data-feather="briefcase"></i><span>Concessionnaires</span></a></li>
                        @endif
                        @if($canAccessFeature('offres'))
                        <li class="{{ $offresActive ? 'active' : '' }}"><a href="{{ route('index.offre-concessionnaire') }}" class="{{ $offresActive ? 'active' : '' }}"><i data-feather="tag"></i><span>Offres</span></a></li>
                        @endif
                        @if($canAccessFeature('demandes_concessionnaires'))
                        <li class="{{ $demandesConcessionnairesActive ? 'active' : '' }}"><a href="{{ route('index.demande-concessionnaire') }}" class="{{ $demandesConcessionnairesActive ? 'active' : '' }}"><i data-feather="inbox"></i><span>Demandes</span></a></li>
                        @endif
                        @if($canAccessFeature('annonces_envoyees'))
                        <li class="{{ $annoncesEnvoyeesActive ? 'active' : '' }}"><a href="{{ route('annonce.sent') }}" class="{{ $annoncesEnvoyeesActive ? 'active' : '' }}"><i data-feather="send"></i><span>Annonces envoyées</span></a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($usersVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Utilisateurs</h6>
                    <ul>
                        @if($canAccessFeature('fonctions'))
                        <li class="{{ $fonctionsActive ? 'active' : '' }}"><a href="{{ route('fonction.index') }}" class="{{ $fonctionsActive ? 'active' : '' }}"><i data-feather="layers"></i><span>Fonctions</span></a></li>
                        @endif
                        @if($canAccessFeature('roles'))
                        <li class="{{ $rolesActive ? 'active' : '' }}"><a href="{{ route('roles.index') }}" class="{{ $rolesActive ? 'active' : '' }}"><i data-feather="shield"></i><span>Rôles</span></a></li>
                        @endif
                        @if($canAccessFeature('admin_users'))
                        <li class="{{ $adminUsersActive ? 'active' : '' }}"><a href="{{ route('admin-users.index') }}" class="{{ $adminUsersActive ? 'active' : '' }}"><i data-feather="user-check"></i><span>Users admin</span></a></li>
                        @endif
                        @if($canAccessFeature('utilisateurs'))
                        <li class="{{ $chauffeursActive ? 'active' : '' }}"><a href="{{ route('chauffeur.index') }}" class="{{ $chauffeursActive ? 'active' : '' }}"><i data-feather="users"></i><span>Chauffeurs</span></a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($settingsVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Paramètres</h6>
                    <ul>
                        @if($canAccessFeature('profil'))
                        <li class="{{ $profilActive ? 'active' : '' }}"><a href="{{ route('profil.index') }}" class="{{ $profilActive ? 'active' : '' }}"><i data-feather="user"></i><span>Mon profil</span></a></li>
                        @endif
                        @if($canAccessFeature('password'))
                        <li class="{{ $passwordActive ? 'active' : '' }}"><a href="{{ route('password.index') }}" class="{{ $passwordActive ? 'active' : '' }}"><i data-feather="lock"></i><span>Mot de passe</span></a></li>
                        @endif
                    </ul>
                </li>
                @endif

                @if($helpVisible)
                <li class="submenu-open">
                    <h6 class="submenu-hdr">Aide</h6>
                    <ul>
                        @if($canAccessFeature('documentation') || ($currentUser && empty($currentUser->parent_gestionnaire_id)))
                        <li class="{{ $documentationActive ? 'active' : '' }}">
                            <a href="{{ route('documentation.index') }}" class="{{ $documentationActive ? 'active' : '' }}">
                                <i data-feather="book-open"></i>
                                <span>Documentation</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

            </ul>
        </div>
    </div>
</div>
