@php
    $vehiclePhotoUrl = function ($photo, $vehicle = null) {
        if (!empty($vehicle?->photo_url_map) && !empty($vehicle->photo_url_map[$photo])) {
            return $vehicle->photo_url_map[$photo];
        }

        if (empty($photo)) {
            return asset('assets/img/default-car.png');
        }

        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            return $photo;
        }

        if (file_exists(public_path($photo))) {
            return asset($photo);
        }

        if (($vehicle->provenance_by ?? null) != 1 && ($vehicle->provenance ?? null) !== 'wasabi') {
            return 'https://api-usager.tooauto.com/' . ltrim($photo, '/');
        }

        $wasabiUrl = rtrim((string) config('wasabi.url'), '/');

        return $wasabiUrl !== '' ? $wasabiUrl . '/' . ltrim($photo, '/') : asset($photo);
    };
@endphp

@include('layouts.header')
@include('layouts.menu')

<style>
    .vehicle-list-page {
        --vehicle-ink: #111827;
        --vehicle-muted: #64748b;
        --vehicle-line: #e8edf4;
        --vehicle-shadow: 0 18px 48px rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #fbfcfe 0%, #f7f9fc 48%, #ffffff 100%);
        border-radius: 8px;
        margin: -6px -4px 0;
        padding: 8px 4px 24px;
    }

    .vehicle-list-hero,
    .vehicle-filter-panel,
    .vehicle-table-panel {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--vehicle-line);
        border-radius: 8px;
        box-shadow: var(--vehicle-shadow);
    }

    .vehicle-list-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding: 24px;
    }

    .vehicle-eyebrow {
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .vehicle-title {
        color: var(--vehicle-ink);
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
    }

    .vehicle-subtitle {
        color: var(--vehicle-muted);
        font-size: 14px;
        margin: 10px 0 0;
    }

    .vehicle-hero-actions {
        align-items: center;
        display: flex;
        gap: 10px;
    }

    .vehicle-btn-primary,
    .vehicle-btn-secondary {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
    }

    .vehicle-btn-primary {
        background: #2563eb;
        border: 1px solid #2563eb;
        color: #ffffff;
    }

    .vehicle-btn-secondary {
        background: #ffffff;
        border: 1px solid #dfe7f1;
        color: #334155;
    }

    .vehicle-filter-panel {
        margin-bottom: 20px;
        padding: 18px;
    }

    .vehicle-filter-title {
        align-items: center;
        color: var(--vehicle-ink);
        display: flex;
        font-size: 16px;
        font-weight: 800;
        gap: 8px;
        margin-bottom: 16px;
    }

    .vehicle-filter-title svg {
        color: #2563eb;
        height: 18px;
        width: 18px;
    }

    .vehicle-list-page label {
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .vehicle-list-page .form-control {
        background: #fbfdff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        min-height: 42px;
    }

    .vehicle-table-panel {
        overflow: hidden;
    }

    .vehicle-table-head {
        align-items: center;
        border-bottom: 1px solid var(--vehicle-line);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
    }

    .vehicle-table-title {
        color: var(--vehicle-ink);
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }

    .vehicle-count-pill {
        background: #eff6ff;
        border-radius: 999px;
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        padding: 7px 10px;
    }

    .vehicle-table {
        margin: 0;
    }

    .vehicle-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--vehicle-line);
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        padding: 13px 14px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .vehicle-table tbody td {
        border-color: #eef2f7;
        color: #334155;
        font-size: 13px;
        padding: 14px;
        vertical-align: middle;
    }

    .vehicle-table tbody tr:hover {
        background: #fbfdff;
    }

    .vehicle-photo-stack {
        align-items: center;
        display: flex;
    }

    .vehicle-photo-stack img {
        border: 2px solid #ffffff;
        border-radius: 8px;
        box-shadow: 0 7px 18px rgba(15, 23, 42, 0.12);
        cursor: pointer;
        height: 42px;
        margin-right: -10px;
        object-fit: cover;
        width: 42px;
    }

    .vehicle-more-photos {
        align-items: center;
        background: #f1f5f9;
        border: 2px solid #ffffff;
        border-radius: 8px;
        color: #475569;
        display: inline-flex;
        font-size: 11px;
        font-weight: 800;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .vehicle-plate {
        color: var(--vehicle-ink);
        display: block;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .vehicle-muted {
        color: var(--vehicle-muted);
        font-size: 12px;
    }

    .vehicle-soft-badge {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 999px;
        color: #475569;
        display: inline-flex;
        font-size: 12px;
        font-weight: 800;
        padding: 7px 10px;
        white-space: nowrap;
    }

    .vehicle-actions {
        align-items: center;
        display: flex;
        gap: 6px;
    }

    .vehicle-icon-btn {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        color: #475569;
        display: inline-flex;
        height: 36px;
        justify-content: center;
        width: 36px;
    }

    .vehicle-icon-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
    }

    .vehicle-icon-btn.danger:hover {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #be123c;
    }

    .vehicle-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: var(--vehicle-muted);
        padding: 30px;
        text-align: center;
    }

    @media (max-width: 767.98px) {
        .vehicle-list-hero,
        .vehicle-table-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .vehicle-title {
            font-size: 23px;
        }

        .vehicle-hero-actions,
        .vehicle-hero-actions .btn {
            justify-content: center;
            width: 100%;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="vehicle-list-page">
            @include('layouts.fileariane')

            @if(session()->has("message"))
                <div style="padding: 10px" class="alert {{session()->get('type')}}">{{ session()->get('message') }} </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="vehicle-list-hero">
                <div>
                    <div class="vehicle-eyebrow">Parc automobile</div>
                    <h1 class="vehicle-title">Liste des véhicules</h1>
                    <p class="vehicle-subtitle">Consultez, filtrez et maintenez les fiches véhicules de votre flotte.</p>
                </div>
                <div class="vehicle-hero-actions">
                    <a href="{{ route('vehicule.add') }}" class="btn vehicle-btn-primary">
                        <i data-feather="plus"></i>
                        Nouveau véhicule
                    </a>
                </div>
            </div>

            <div class="vehicle-filter-panel">
                <div class="vehicle-filter-title">
                    <i data-feather="sliders"></i>
                    Filtres rapides
                </div>
                <div class="row">
                    <div class="col-xl-2 col-md-4 col-12">
                        <div class="form-group">
                            <label for="filterTypeVehicule">Type de véhicule</label>
                            <select class="form-control" id="filterTypeVehicule">
                                <option value="">Tous</option>
                                @foreach($type_de_vehicules as $type)
                                    <option value="{{ $type->libelle }}">{{ $type->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-12">
                        <div class="form-group">
                            <label for="filterMarque">Marque</label>
                            <select class="form-control" id="filterMarque">
                                <option value="">Toutes</option>
                                @foreach($marques as $marque)
                                    <option value="{{ $marque->libelle }}">{{ $marque->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-12">
                        <div class="form-group">
                            <label for="filterCarburant">Carburant</label>
                            <select class="form-control" id="filterCarburant">
                                <option value="">Tous</option>
                                @foreach($type_de_carburants as $carburant)
                                    <option value="{{ $carburant->libelle }}">{{ $carburant->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-12">
                        <div class="form-group">
                            <label for="filterCouleur">Couleur</label>
                            <select class="form-control" id="filterCouleur">
                                <option value="">Toutes</option>
                                @foreach($couleur_vehicules as $couleur)
                                    <option value="{{ $couleur->libelle }}">{{ $couleur->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-12">
                        <div class="form-group">
                            <label for="filterChauffeur">Utilisateur</label>
                            <select class="form-control" id="filterChauffeur">
                                <option value="">Tous</option>
                                @foreach($chauffeurs as $chauffeur)
                                    <option value="{{ $chauffeur->nom }} {{ $chauffeur->prenoms }}">{{ $chauffeur->nom }} {{ $chauffeur->prenoms }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-12">
                        <div class="form-group">
                            <label for="searchVehicule">Recherche</label>
                            <input type="text" class="form-control" id="searchVehicule" placeholder="Matricule, modèle...">
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($vehicules) && count($vehicules) > 0)
                <div class="vehicle-table-panel">
                    <div class="vehicle-table-head">
                        <h3 class="vehicle-table-title">Véhicules enregistrés</h3>
                        <span class="vehicle-count-pill">{{ $vehicules->total() }} véhicule(s)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table vehicle-table" id="vehiculeTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photos</th>
                                    <th>Immatriculation</th>
                                    <th>Carte grise</th>
                                    <th>Utilisateur</th>
                                    <th>Fonction</th>
                                    <th>Type</th>
                                    <th>Marque</th>
                                    <th>Modèle</th>
                                    <th>Énergie</th>
                                    <th>Couleur</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehicules as $key => $item)
                                    @php $imagePaths = json_decode($item->photos, true) ?: []; @endphp
                                    <tr>
                                        <td>{{ $vehicules->firstItem() + $key }}</td>
                                        <td>
                                            @if(count($imagePaths) > 0)
                                                <div class="vehicle-photo-stack">
                                                    @foreach(array_slice($imagePaths, 0, 3) as $photo)
                                                        <img src="{{ $vehiclePhotoUrl($photo, $item) }}" alt="Photo véhicule" data-bs-toggle="modal" data-bs-target="#show{{ $item->id }}">
                                                    @endforeach
                                                    @if(count($imagePaths) > 3)
                                                        <span class="vehicle-more-photos">+{{ count($imagePaths) - 3 }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="vehicle-muted">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="vehicle-plate">{{ $item->matricule }}</span>
                                        </td>
                                        <td>{{ $item->carte_grise }}</td>
                                        <td>
                                            <strong>{{ $item->chauffeur->nom ?? '-' }} {{ $item->chauffeur->prenoms ?? '' }}</strong>
                                        </td>
                                        <td><span class="vehicle-muted">{{ $item->chauffeur->fonction->libelle ?? '-' }}</span></td>
                                        <td><span class="vehicle-soft-badge">{{ $item->type_de_vehicule->libelle ?? '-' }}</span></td>
                                        <td>{{ $item->marque->libelle ?? '-' }}</td>
                                        <td>{{ $item->modele }}</td>
                                        <td>{{ $item->type_de_carburant->libelle ?? '-' }}</td>
                                        <td>{{ $item->couleur_vehicule->libelle ?? $item->couleur }}</td>
                                        <td>
                                            <div class="vehicle-actions">
                                                <button class="vehicle-icon-btn" data-bs-toggle="modal" data-bs-target="#show{{ $item->id }}" title="Détails">
                                                    <i data-feather="eye"></i>
                                                </button>
                                                <a class="vehicle-icon-btn" href="{{ route('vehicule.edit', $item->id) }}" title="Modifier">
                                                    <i data-feather="edit-2"></i>
                                                </a>
                                                <form action="{{ route('vehicule.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="vehicle-icon-btn danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ?')">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('vehicule.partials.modals', ['item' => $item, 'type_de_vehicules' => $type_de_vehicules, 'marques' => $marques, 'type_de_carburants' => $type_de_carburants, 'couleur_vehicules' => $couleur_vehicules, 'fonctions' => $fonctions, 'chauffeurs' => $chauffeurs, 'vehiclePhotoUrl' => $vehiclePhotoUrl])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center p-3">
                        {{ $vehicules->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="vehicle-empty">Aucun véhicule enregistré pour le moment.</div>
            @endif
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather) {
            feather.replace();
        }

        function normalize(str) {
            return (str || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
        }

        function filterVehicules() {
            var typeVehicule = normalize(document.getElementById('filterTypeVehicule').value);
            var marque = normalize(document.getElementById('filterMarque').value);
            var carburant = normalize(document.getElementById('filterCarburant').value);
            var couleur = normalize(document.getElementById('filterCouleur').value);
            var chauffeur = normalize(document.getElementById('filterChauffeur').value);
            var search = normalize(document.getElementById('searchVehicule').value);

            document.querySelectorAll('#vehiculeTable tbody tr').forEach(function (row) {
                var typeVehiculeText = normalize(row.querySelector('td:nth-child(7)')?.textContent);
                var marqueText = normalize(row.querySelector('td:nth-child(8)')?.textContent);
                var carburantText = normalize(row.querySelector('td:nth-child(10)')?.textContent);
                var couleurText = normalize(row.querySelector('td:nth-child(11)')?.textContent);
                var chauffeurText = normalize(row.querySelector('td:nth-child(5)')?.textContent);
                var searchText = normalize(row.textContent);

                var show = true;
                if (typeVehicule && !typeVehiculeText.includes(typeVehicule)) show = false;
                if (marque && !marqueText.includes(marque)) show = false;
                if (carburant && !carburantText.includes(carburant)) show = false;
                if (couleur && !couleurText.includes(couleur)) show = false;
                if (chauffeur && !chauffeurText.includes(chauffeur)) show = false;
                if (search && !searchText.includes(search)) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        ['filterTypeVehicule', 'filterMarque', 'filterCarburant', 'filterCouleur', 'filterChauffeur'].forEach(function (id) {
            var element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', filterVehicules);
            }
        });

        var search = document.getElementById('searchVehicule');
        if (search) {
            search.addEventListener('keyup', filterVehicules);
        }
    });
</script>
