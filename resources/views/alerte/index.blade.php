@include('layouts.header')
@include('layouts.menu')
<!-- Ajout des dépendances Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Ajout du meta tag CSRF -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Style général pour Select2 */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ddd;
        border-radius: 4px;
        background-color: #fff;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px;
        padding-left: 12px;
        color: #333;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }

    /* Style pour le dropdown */
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }

    .select2-dropdown {
        border: 1px solid #ddd;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Style pour la recherche */
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 6px;
    }

    .select2-container--default .select2-search--dropdown .select2-search__field:focus {
        border-color: #0d6efd;
        outline: none;
    }

    /* Style pour le placeholder */
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }

    /* Style pour l'option sélectionnée */
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #333;
    }

    /* Style pour le bouton clear */
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #999;
        margin-right: 10px;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        color: #333;
    }
</style>

<div class="page-wrapper">
    <div class="content">
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

        <!-- En-tête avec statistiques amélioré -->
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center position-relative p-4">
                        <div class="stat-icon-circle bg-primary position-absolute top-0 start-0 translate-middle-y ms-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                            <i data-feather="shield" class="text-white" style="width:24px;height:24px;"></i>
                        </div>
                        <div class="stat-number display-4 fw-bold my-3">{{ $alertes->where('type_alert_id', 1)->count() }}</div>
                        <div class="stat-label mb-2 text-muted fs-5">Assurances</div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center position-relative p-4">
                        <div class="stat-icon-circle bg-warning position-absolute top-0 start-0 translate-middle-y ms-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                            <i data-feather="droplet" class="text-white" style="width:24px;height:24px;"></i>
                        </div>
                        <div class="stat-number display-4 fw-bold my-3">{{ $alertes->where('type_alert_id', 2)->count() }}</div>
                        <div class="stat-label mb-2 text-muted fs-5">Vidanges</div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center position-relative p-4">
                        <div class="stat-icon-circle bg-info position-absolute top-0 start-0 translate-middle-y ms-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                            <i data-feather="tool" class="text-white" style="width:24px;height:24px;"></i>
                        </div>
                        <div class="stat-number display-4 fw-bold my-3">{{ $alertes->where('type_alert_id', 3)->count() }}</div>
                        <div class="stat-label mb-2 text-muted fs-5">Visites Techniques</div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center position-relative p-4">
                        <div class="stat-icon-circle bg-success position-absolute top-0 start-0 translate-middle-y ms-3" style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;border-radius:50%;">
                            <i data-feather="check-circle" class="text-white" style="width:24px;height:24px;"></i>
                        </div>
                        <div class="stat-number display-4 fw-bold my-3">{{ $alertes->where('type_alert_id', 4)->count() }}</div>
                        <div class="stat-label mb-2 text-muted fs-5">Contrôles Techniques</div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" style="width: 50%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .stat-icon-circle {
                box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            }
            .stat-number {
                font-size: 2.5rem;
            }
            .stat-label {
                letter-spacing: 0.5px;
            }
        </style>
        <script>
            if (window.feather) { feather.replace(); }
        </script>

        <!-- Filtres et bouton d'ajout -->
        <div class="row mb-3">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('alerte.index') }}" method="GET" class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <select name="type_alert_id" class="form-select">
                                    <option value="">Tous les types</option>
                                    @foreach($type_alertes as $type)
                                        <option value="{{ $type->id }}" {{ request('type_alert_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="vehicule_id" class="form-select">
                                    <option value="">Tous les véhicules</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" {{ request('vehicule_id') == $vehicule->id ? 'selected' : '' }}>
                                            {{ $vehicule->matricule }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="statut" class="form-select">
                                    <option value="">Tous les statuts</option>
                                    <option value="expire" {{ request('statut') == 'expire' ? 'selected' : '' }}>Expiré</option>
                                    <option value="proche" {{ request('statut') == 'proche' ? 'selected' : '' }}>Expire bientôt</option>
                                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Valide</option>
                                </select>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <button type="submit" class="btn btn-primary">Filtrer</button>
                                <a href="{{ route('alerte.index') }}" class="btn btn-secondary">Réinitialiser</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_alert">
                    <i class="fe fe-plus"></i> Nouvelle alerte
                </button>
            </div>
        </div>

        <!-- Liste des alertes -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Liste des alertes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Véhicule</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>
                                        <th>Kilométrage</th>
                                        <th>Statut</th>
                                        <th>Provenance</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alertes as $key => $alerte)
                                    <div class="modal fade" id="show{{ $alerte->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Informations du conducteur</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="modal-body">
                                                        <p>Nom: {{ $alerte->user->nom ?? 'N/A' }}</p>
                                                        <p>Prénom: {{ $alerte->user->prenoms ?? 'N/A' }}</p>
                                                        <p>Email: {{ $alerte->user->email ?? 'N/A' }}</p>
                                                        <p>Téléphone: {{ $alerte->user->mobile ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>
                                                <span class="badge bg-{{ $alerte->type_alert_id == 1 ? 'primary' : ($alerte->type_alert_id == 2 ? 'warning' : ($alerte->type_alert_id == 3 ? 'info' : 'success')) }}">
                                                    {{ $alerte->type_alert->libelle }}
                                                </span>
                                            </td>
                                            <td>{{ $alerte->vehicule->matricule ?? 'N/A' }}</td>
                                            <td>{{ $alerte->date_debut }}</td>
                                            <td>{{ $alerte->date_fin }}</td>
                                            <td>{{ $alerte->kilometrage ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $today = \Carbon\Carbon::today();
                                                    $dateFin = \Carbon\Carbon::parse($alerte->date_fin);
                                                    $daysUntilExpiration = $today->diffInDays($dateFin, false);
                                                @endphp
                                                @if($daysUntilExpiration < 0)
                                                    <span class="badge bg-danger">Expiré</span>
                                                @elseif($daysUntilExpiration <= 30)
                                                    <span class="badge bg-warning">Expire bientôt ({{ $daysUntilExpiration }} jours)</span>
                                                @else
                                                    <span class="badge bg-success">Valide ({{ $daysUntilExpiration }} jours)</span>
                                                @endif
                                            </td>
                                            <td>{{ $alerte->provenance ? 'Conducteur' : 'Administrateur' }}</td>
                                            <td>
                                                <div class="d-flex justify-content-between btn-actions">
                                                    <a href="{{ route('alerte.edit', $alerte->id) }}" class="mt-3">
                                                        <img src="../assets/img/icons/edit.svg" alt="img">
                                                    </a>
                                                    <a href="#" class="mt-3" data-bs-toggle="modal" data-bs-target="#delete{{ $alerte->id }}">
                                                        <img src="../assets/img/icons/delete.svg" alt="img">
                                                    </a>
                                                    @if($alerte->provenance == 'flotte')
                                                        <a title="Détails du conducteur" href="#" class="mt-3" data-bs-toggle="modal" data-bs-target="#show{{ $alerte->id }}">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'ajout d'alerte -->
<div class="modal fade" id="add_alert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle alerte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('alerte.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type d'alerte</label>
                                <select name="type_alert_id" class="form-select" required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($type_alertes as $type)
                                        <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Marque</label>
                                <select name="marque_id" class="form-select" required>
                                    <option value="">Sélectionner une marque</option>
                                    @foreach($marques as $marque)
                                        <option value="{{ $marque->id }}">{{ $marque->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Véhicule</label>
                                <select name="vehicule_id" class="form-select" required>
                                    <option value="">Sélectionner un véhicule</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 kilometrage-field" style="display: none;">
                            <div class="form-group">
                                <label>Kilométrage</label>
                                <input type="number" name="kilometrage" class="form-control" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de début</label>
                                <input type="date" name="date_debut" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de fin</label>
                                <input type="date" name="date_fin" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal d'édition -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Modifier l'alerte</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_type_alert_id">Type d'alerte</label>
                        <select class="form-control" id="edit_type_alert_id" name="type_alert_id" required>
                            <option value="">Sélectionner un type d'alerte</option>
                            @foreach($type_alertes as $type)
                                <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_vehicule_id">Véhicule</label>
                        <select class="form-control" id="edit_vehicule_id" name="vehicule_id" required>
                            <option value="">Sélectionner un véhicule</option>
                            @foreach($vehicules as $vehicule)
                                <option value="{{ $vehicule->id }}">{{ $vehicule->matricule }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_date_debut">Date de début</label>
                        <input type="date" class="form-control" id="edit_date_debut" name="date_debut" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="edit_date_fin">Date de fin</label>
                        <input type="date" class="form-control" id="edit_date_fin" name="date_fin" required>
                    </div>

                    <div class="form-group mb-3" id="edit_kilometrage_group" style="display: none;">
                        <label for="edit_kilometrage">Kilométrage</label>
                        <input type="number" class="form-control" id="edit_kilometrage" name="kilometrage">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    // Configuration globale d'AJAX
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialisation de Select2 pour les sélecteurs
    $(document).ready(function() {
        var addAlertModal = $('#add_alert');
        var addMarqueSelect = addAlertModal.find('select[name="marque_id"]');
        var addVehiculeSelect = addAlertModal.find('select[name="vehicule_id"]');
        var addTypeAlertSelect = addAlertModal.find('select[name="type_alert_id"]');

        addMarqueSelect.select2({
            dropdownParent: $('#add_alert')
        });

        addVehiculeSelect.select2({
            dropdownParent: $('#add_alert')
        });

        addTypeAlertSelect.select2({
            dropdownParent: $('#add_alert')
        });

        // Gestion de l'affichage du champ kilométrage
        addTypeAlertSelect.on('change', function() {
            if ($(this).val() == 2) { // ID pour Vidange
                $('.kilometrage-field').show();
                $('input[name="kilometrage"]').prop('required', true);
            } else {
                $('.kilometrage-field').hide();
                $('input[name="kilometrage"]').prop('required', false);
            }
        });

        var vehiculesByMarqueUrlTemplate = '{{ route('alerte.vehicules.by.marque', ['marqueId' => '__MARQUE_ID__']) }}';

        function setVehiculeOptions(optionsHtml) {
            addVehiculeSelect.html(optionsHtml).val('').trigger('change');
        }

        // Chargement des véhicules par marque
        addMarqueSelect.on('change', function() {
            var marqueId = $(this).val();
            var vehiculeSelect = addVehiculeSelect;

            setVehiculeOptions('<option value="">Chargement des véhicules...</option>');

            if (marqueId) {
                $.ajax({
                    url: vehiculesByMarqueUrlTemplate.replace('__MARQUE_ID__', encodeURIComponent(marqueId)),
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        if (!Array.isArray(data) || data.length === 0) {
                            setVehiculeOptions('<option value="">Aucun véhicule pour cette marque</option>');
                            return;
                        }

                        vehiculeSelect.empty().append('<option value="">Sélectionner un véhicule</option>');
                        $.each(data, function(key, value) {
                            vehiculeSelect.append('<option value="' + value.id + '">' + value.matricule + '</option>');
                        });
                        vehiculeSelect.val('').trigger('change');
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', xhr.responseText);
                        setVehiculeOptions('<option value="">Erreur de chargement</option>');
                    }
                });
            } else {
                setVehiculeOptions('<option value="">Sélectionner une marque d'abord</option>');
            }
        });

        // Gestion de l'édition
        $('.edit-alert').click(function() {
            var id = $(this).data('id');
            var typeAlertId = $(this).data('type-alert');
            var vehiculeId = $(this).data('vehicule');
            var dateDebut = $(this).data('date-debut');
            var dateFin = $(this).data('date-fin');
            var kilometrage = $(this).data('kilometrage');

            // Mettre à jour le formulaire
            $('#editForm').attr('action', '/alerte/' + id);
            $('#edit_type_alert_id').val(typeAlertId);
            $('#edit_vehicule_id').val(vehiculeId);
            $('#edit_date_debut').val(dateDebut);
            $('#edit_date_fin').val(dateFin);

            // Gérer l'affichage du champ kilométrage
            if (typeAlertId == 2) { // Si c'est une alerte de type vidange
                $('#edit_kilometrage_group').show();
                $('#edit_kilometrage').val(kilometrage);
            } else {
                $('#edit_kilometrage_group').hide();
                $('#edit_kilometrage').val('');
            }

            // Afficher le modal
            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });

        // Gestion du changement de type d'alerte
        $('#edit_type_alert_id').change(function() {
            if ($(this).val() == 2) { // Si c'est une alerte de type vidange
                $('#edit_kilometrage_group').show();
            } else {
                $('#edit_kilometrage_group').hide();
                $('#edit_kilometrage').val('');
            }
        });
    });

    function confirmDelete(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette alerte ?')) {
            document.getElementById('deleteForm' + id).submit();
        }
    }
</script>
