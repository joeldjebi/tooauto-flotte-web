@php
    $defaultUserAvatar = asset('assets/img/profiles/avatar-01.jpg');

    $chauffeurImageUrl = function ($image, $chauffeur = null) {
        if (!empty($chauffeur?->image_url)) {
            return $chauffeur->image_url;
        }

        if (empty($image)) {
            return asset('assets/img/profiles/avatar-01.jpg');
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (file_exists(public_path('images/chauffeur/' . $image))) {
            return asset('images/chauffeur/' . $image);
        }

        if (file_exists(public_path($image))) {
            return asset($image);
        }

        $wasabiUrl = rtrim((string) config('wasabi.url'), '/');

        return $wasabiUrl !== '' ? $wasabiUrl . '/' . ltrim($image, '/') : asset('assets/img/profiles/avatar-01.jpg');
    };
@endphp

@include('layouts.header')
@include('layouts.menu')

<style>
    .user-page {
        --user-ink: #111827;
        --user-muted: #64748b;
        --user-line: #e8edf4;
        --user-shadow: 0 18px 48px rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #fbfcfe 0%, #f7f9fc 48%, #ffffff 100%);
        border-radius: 8px;
        margin: -6px -4px 0;
        padding: 8px 4px 24px;
    }

    .user-hero,
    .user-filter-panel,
    .user-table-panel {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--user-line);
        border-radius: 8px;
        box-shadow: var(--user-shadow);
    }

    .user-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding: 24px;
    }

    .user-eyebrow {
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .user-title {
        color: var(--user-ink);
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
    }

    .user-subtitle {
        color: var(--user-muted);
        font-size: 14px;
        margin: 10px 0 0;
    }

    .user-btn-primary,
    .user-btn-secondary {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
    }

    .user-btn-primary {
        background: #2563eb;
        border: 1px solid #2563eb;
        color: #ffffff;
    }

    .user-btn-secondary {
        background: #ffffff;
        border: 1px solid #dfe7f1;
        color: #334155;
    }

    .user-filter-panel {
        margin-bottom: 20px;
        padding: 18px;
    }

    .user-filter-title {
        align-items: center;
        color: var(--user-ink);
        display: flex;
        font-size: 16px;
        font-weight: 800;
        gap: 8px;
        margin-bottom: 16px;
    }

    .user-filter-title svg {
        color: #2563eb;
        height: 18px;
        width: 18px;
    }

    .user-page label {
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .user-page .form-control,
    .user-page .form-select {
        background: #fbfdff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        min-height: 42px;
    }

    .user-table-panel {
        overflow: hidden;
    }

    .user-table-head {
        align-items: center;
        border-bottom: 1px solid var(--user-line);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
    }

    .user-table-title {
        color: var(--user-ink);
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }

    .user-count-pill {
        background: #eff6ff;
        border-radius: 999px;
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        padding: 7px 10px;
    }

    .user-table {
        margin: 0;
    }

    .user-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--user-line);
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        padding: 13px 14px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .user-table tbody td {
        border-color: #eef2f7;
        color: #334155;
        font-size: 13px;
        padding: 14px;
        vertical-align: middle;
    }

    .user-avatar {
        border: 2px solid #ffffff;
        border-radius: 999px;
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.12);
        height: 48px;
        object-fit: cover;
        width: 48px;
    }

    .user-name {
        color: var(--user-ink);
        display: block;
        font-size: 14px;
        font-weight: 800;
        white-space: nowrap;
    }

    .user-muted {
        color: var(--user-muted);
        font-size: 12px;
    }

    .user-soft-badge {
        border-radius: 999px;
        display: inline-flex;
        font-size: 12px;
        font-weight: 800;
        padding: 7px 10px;
        white-space: nowrap;
    }

    .user-soft-badge.success {
        background: #ecfdf5;
        color: #047857;
    }

    .user-soft-badge.danger {
        background: #fff1f2;
        color: #be123c;
    }

    .user-soft-badge.info {
        background: #eff6ff;
        color: #2563eb;
    }

    .user-actions {
        align-items: center;
        display: flex;
        gap: 6px;
    }

    .user-icon-btn {
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

    .user-icon-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
    }

    .user-icon-btn.danger:hover {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #be123c;
    }

    .user-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: var(--user-muted);
        padding: 30px;
        text-align: center;
    }

    .user-modal .modal-content {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .user-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e8edf4;
        padding: 18px 20px;
    }

    .user-modal .modal-title {
        color: var(--user-ink);
        font-weight: 800;
    }

    .user-upload {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 14px;
    }

    .user-preview {
        align-items: center;
        display: flex;
        gap: 12px;
        margin-top: 12px;
    }

    .user-preview img {
        border-radius: 999px;
        height: 58px;
        object-fit: cover;
        width: 58px;
    }

    .user-feature-box {
        background: #f8fafc;
        border: 1px solid #e8edf4;
        border-radius: 8px;
        margin-top: 10px;
        padding: 14px;
    }

    .user-feature-group {
        margin-bottom: 14px;
    }

    .user-feature-group:last-child {
        margin-bottom: 0;
    }

    .user-feature-title {
        color: #130d0d;
        display: block;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .user-feature-grid {
        display: grid;
        gap: 8px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .user-feature-check {
        align-items: center;
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        display: flex;
        gap: 8px;
        min-height: 38px;
        padding: 8px 10px;
    }

    .user-feature-check input {
        margin: 0;
    }

    .user-feature-check span {
        color: #334155;
        font-size: 12px;
        font-weight: 800;
    }

    @media (max-width: 767.98px) {
        .user-hero,
        .user-table-head {
            align-items: flex-start;
            flex-direction: column;
        }

        .user-title {
            font-size: 23px;
        }

        .user-btn-primary {
            justify-content: center;
            width: 100%;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="user-page">
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

            <div class="user-hero">
                <div>
                    <div class="user-eyebrow">Équipe flotte</div>
                    <h1 class="user-title">Liste des utilisateurs</h1>
                    <p class="user-subtitle">Gérez les utilisateurs, leurs fonctions, leurs statuts et leurs véhicules affectés.</p>
                </div>
                <a href="javascript:void(0);" class="btn user-btn-primary" data-bs-toggle="modal" data-bs-target="#addChauffeur">
                    <i data-feather="user-plus"></i>
                    Ajouter un utilisateur
                </a>
            </div>

            <div class="user-filter-panel">
                <div class="user-filter-title">
                    <i data-feather="sliders"></i>
                    Filtres rapides
                </div>
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="form-group">
                            <label for="filterStatut">Statut</label>
                            <select id="filterStatut" class="form-select">
                                <option value="">Tous les statuts</option>
                                <option value="Actif">Actif</option>
                                <option value="Non actif">Non actif</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="form-group">
                            <label for="filterFonction">Fonction</label>
                            <select id="filterFonction" class="form-select">
                                <option value="">Toutes les fonctions</option>
                                @foreach($fonctions as $fonction)
                                    <option value="{{ $fonction->libelle }}">{{ $fonction->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="form-group">
                            <label for="filterVehicule">Véhicule</label>
                            <select id="filterVehicule" class="form-select">
                                <option value="">Tous les utilisateurs</option>
                                <option value="Oui">Avec véhicule</option>
                                <option value="Non">Sans véhicule</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="form-group">
                            <label for="searchChauffeur">Recherche</label>
                            <input type="text" id="searchChauffeur" class="form-control" placeholder="Nom, contact, ville...">
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($chauffeurs) && count($chauffeurs) > 0)
                <div class="user-table-panel">
                    <div class="user-table-head">
                        <h3 class="user-table-title">Utilisateurs enregistrés</h3>
                        <span class="user-count-pill">{{ $chauffeurs->total() }} utilisateur(s)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table user-table" id="chauffeurTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Utilisateur</th>
                                    <th>Contact</th>
                                    <th>Fonction</th>
                                    <th>Ville</th>
                                    <th>Statut</th>
                                    <th>Véhicule</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chauffeurs as $key => $item)
                                    <tr>
                                        <td>{{ $chauffeurs->firstItem() + $key }}</td>
                                        <td>
                                            <img class="user-avatar" src="{{ $chauffeurImageUrl($item->image, $item) }}" alt="{{ $item->nom }}" onerror="this.onerror=null;this.src='{{ $defaultUserAvatar }}'">
                                        </td>
                                        <td>
                                            <span class="user-name">{{ $item->nom }} {{ $item->prenoms }}</span>
                                            <span class="user-muted">Créé le {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <a href="tel:{{ $item->mobile ?? '' }}" class="user-soft-badge info">{{ $item->mobile ?? '' }}</a>
                                        </td>
                                        <td>{{ $item->fonction->libelle ?? '-' }}</td>
                                        <td>{{ $item->ville->libelle ?? '-' }}</td>
                                        <td>
                                            <span class="user-soft-badge {{ $item->statut == 1 ? 'success' : 'danger' }}">
                                                {{ $item->statut == 1 ? 'Actif' : 'Non actif' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($item->vehicules->isNotEmpty())
                                                <button type="button" class="user-soft-badge info border-0" data-bs-toggle="modal" data-bs-target="#showVehicule{{ $item->id }}">
                                                    {{ $item->vehicules->count() }} véhicule(s)
                                                </button>
                                            @else
                                                <span class="user-muted">Aucun</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="user-actions">
                                                <button type="button" class="user-icon-btn" data-bs-toggle="modal" data-bs-target="#show{{ $item->id }}" title="Détails">
                                                    <i data-feather="eye"></i>
                                                </button>
                                                <button type="button" class="user-icon-btn" data-bs-toggle="modal" data-bs-target="#edit{{ $item->id }}" title="Modifier">
                                                    <i data-feather="edit-2"></i>
                                                </button>
                                                <form action="{{ route('chauffeur.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="user-icon-btn danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('chauffeur.partials.modals', ['item' => $item, 'fonctions' => $fonctions, 'villes' => $villes, 'chauffeurImageUrl' => $chauffeurImageUrl])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center p-3">
                        {{ $chauffeurs->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="user-empty">Aucun utilisateur trouvé.</div>
            @endif
        </div>
    </div>

    <div class="modal fade user-modal" id="addChauffeur" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Ajouter un utilisateur</h5>
                        <div class="user-muted">La photo sera stockée dans Wasabi.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('chauffeur.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="nom">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" value="{{ old('nom') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="prenoms">Prénoms</label>
                                    <input type="text" class="form-control" id="prenoms" name="prenoms" value="{{ old('prenoms') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="mobile">Numéro de téléphone</label>
                                    <div class="input-group">
                                        <span class="input-group-text">225</span>
                                        <input type="number" class="form-control" id="mobile" name="mobile" value="{{ old('mobile') }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="fonction_id">Fonction</label>
                                    <select class="form-control" name="fonction_id" id="fonction_id" required>
                                        <option value="">Sélectionnez une fonction</option>
                                        @foreach ($fonctions as $fonction)
                                            <option value="{{ $fonction->id }}" {{ old('fonction_id') == $fonction->id ? 'selected' : '' }}>{{ $fonction->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="ville_id">Ville</label>
                                    <select class="form-control" name="ville_id" id="ville_id" required>
                                        <option value="">Sélectionnez une ville</option>
                                        @foreach ($villes as $ville)
                                            <option value="{{ $ville->id }}" {{ old('ville_id') == $ville->id ? 'selected' : '' }}>{{ $ville->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="image">Photo</label>
                                    <div class="user-upload">
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div class="user-preview" id="userImagePreview" style="display:none;">
                                            <img src="" alt="Aperçu photo">
                                            <span class="user-muted">Aperçu de la photo sélectionnée</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn user-btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button class="btn user-btn-primary" type="submit">
                                <i data-feather="save"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

        function filterChauffeurs() {
            var search = normalize(document.getElementById('searchChauffeur').value);
            var statut = document.getElementById('filterStatut').value;
            var fonction = document.getElementById('filterFonction').value;
            var vehicule = document.getElementById('filterVehicule').value;

            document.querySelectorAll('#chauffeurTable tbody tr').forEach(function (row) {
                var text = normalize(row.textContent);
                var statutText = row.querySelector('td:nth-child(7) .user-soft-badge')?.textContent.trim();
                var fonctionText = row.querySelector('td:nth-child(5)')?.textContent.trim();
                var vehiculeBtn = row.querySelector('td:nth-child(8) button');
                var hasVehicule = vehiculeBtn !== null;
                var show = true;

                if (search && !text.includes(search)) show = false;
                if (statut && statutText !== statut) show = false;
                if (fonction && fonctionText !== fonction) show = false;
                if (vehicule === 'Oui' && !hasVehicule) show = false;
                if (vehicule === 'Non' && hasVehicule) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        ['filterStatut', 'filterFonction', 'filterVehicule'].forEach(function (id) {
            var element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', filterChauffeurs);
            }
        });

        var searchInput = document.getElementById('searchChauffeur');
        if (searchInput) {
            searchInput.addEventListener('keyup', filterChauffeurs);
        }

        var imageInput = document.getElementById('image');
        var preview = document.getElementById('userImagePreview');

        if (imageInput && preview) {
            imageInput.addEventListener('change', function (event) {
                var file = event.target.files && event.target.files[0];

                if (!file || !file.type.startsWith('image/')) {
                    preview.style.display = 'none';
                    return;
                }

                var reader = new FileReader();
                reader.onload = function (readerEvent) {
                    preview.querySelector('img').src = readerEvent.target.result;
                    preview.style.display = 'flex';
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>
