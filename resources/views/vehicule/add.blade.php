@include('layouts.header')
@include('layouts.menu')

<style>
    .vehicle-page {
        --vehicle-ink: #111827;
        --vehicle-muted: #64748b;
        --vehicle-line: #e8edf4;
        --vehicle-soft: #f7faff;
        --vehicle-shadow: 0 18px 48px rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #fbfcfe 0%, #f7f9fc 48%, #ffffff 100%);
        border-radius: 8px;
        margin: -6px -4px 0;
        padding: 8px 4px 24px;
    }

    .vehicle-hero,
    .vehicle-card {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--vehicle-line);
        border-radius: 8px;
        box-shadow: var(--vehicle-shadow);
    }

    .vehicle-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 22px;
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
        max-width: 650px;
    }

    .vehicle-card {
        margin-bottom: 22px;
        overflow: hidden;
    }

    .vehicle-card-header {
        align-items: center;
        background: #ffffff;
        border-bottom: 1px solid var(--vehicle-line);
        display: flex;
        justify-content: space-between;
        gap: 14px;
        padding: 20px 22px;
    }

    .vehicle-card-title {
        color: var(--vehicle-ink);
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }

    .vehicle-card-caption {
        color: var(--vehicle-muted);
        font-size: 13px;
        margin: 4px 0 0;
    }

    .vehicle-card-body {
        padding: 22px;
    }

    .vehicle-icon-pill {
        align-items: center;
        background: #eff6ff;
        border-radius: 8px;
        color: #2563eb;
        display: inline-flex;
        height: 42px;
        justify-content: center;
        width: 42px;
    }

    .vehicle-icon-pill svg {
        height: 20px;
        width: 20px;
    }

    .vehicle-form-grid {
        row-gap: 6px;
    }

    .vehicle-page label {
        color: #334155;
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .vehicle-page .form-control,
    .vehicle-page .bootstrap-select > .dropdown-toggle {
        background: #fbfdff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        color: #1f2937;
        min-height: 44px;
    }

    .vehicle-page .form-control:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .vehicle-upload {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 18px;
    }

    .vehicle-upload-hint {
        color: var(--vehicle-muted);
        font-size: 13px;
        margin: 8px 0 0;
    }

    .vehicle-preview-grid {
        display: grid;
        gap: 12px;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-top: 16px;
    }

    .vehicle-preview-item {
        background: #ffffff;
        border: 1px solid #e8edf4;
        border-radius: 8px;
        overflow: hidden;
    }

    .vehicle-preview-item img {
        aspect-ratio: 4 / 3;
        object-fit: cover;
        width: 100%;
    }

    .vehicle-preview-item span {
        color: var(--vehicle-muted);
        display: block;
        font-size: 11px;
        overflow: hidden;
        padding: 8px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .vehicle-action-row {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 18px;
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

    .vehicle-import-note {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        color: var(--vehicle-muted);
        padding: 14px;
    }

    .vehicle-import-note code {
        color: #2563eb;
    }

    @media (max-width: 767.98px) {
        .vehicle-hero,
        .vehicle-card-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .vehicle-title {
            font-size: 23px;
        }

        .vehicle-preview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .vehicle-action-row {
            align-items: stretch;
            flex-direction: column-reverse;
        }

        .vehicle-action-row .btn,
        .vehicle-action-row a {
            justify-content: center;
            width: 100%;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="vehicle-page">
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

            <div class="vehicle-hero">
                <div>
                    <div class="vehicle-eyebrow">Nouveau véhicule</div>
                    <h1 class="vehicle-title">Créer une fiche véhicule</h1>
                    <p class="vehicle-subtitle">Ajoutez les informations clés, affectez un utilisateur et chargez les 4 photos du véhicule. Les images sont envoyées dans Wasabi.</p>
                </div>
                <a href="{{ route('vehicule.index') }}" class="btn vehicle-btn-secondary">
                    <i data-feather="arrow-left"></i>
                    Retour à la liste
                </a>
            </div>

            <div class="vehicle-card">
                <div class="vehicle-card-header">
                    <div>
                        <h3 class="vehicle-card-title">Import Excel</h3>
                        <p class="vehicle-card-caption">Ajoutez plusieurs véhicules en une fois avec le modèle fourni.</p>
                    </div>
                    <span class="vehicle-icon-pill"><i data-feather="file-plus"></i></span>
                </div>
                <div class="vehicle-card-body">
                    <form action="{{ route('vehicule.importExcel') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row align-items-end">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="excel_file">Fichier Excel (.xlsx, .xls)</label>
                                    <input type="file" name="excel_file" id="excel_file" class="form-control" accept=".xlsx,.xls" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="vehicle-action-row mt-lg-0">
                                    <a href="{{ route('vehicule.downloadTemplate') }}" class="btn vehicle-btn-secondary" download>
                                        <i data-feather="download"></i>
                                        Modèle Excel
                                    </a>
                                    <button type="submit" class="btn vehicle-btn-primary">
                                        <i data-feather="upload-cloud"></i>
                                        Importer
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="vehicle-import-note mt-3">
                            Colonnes obligatoires: <code>matricule</code>, <code>carte_grise</code>, <code>modele</code>, <code>mobile</code>. Les types, marques, carburants et couleurs peuvent être créés automatiquement.
                        </div>
                    </form>
                </div>
            </div>

            <div class="vehicle-card">
                <div class="vehicle-card-header">
                    <div>
                        <h3 class="vehicle-card-title">Informations du véhicule</h3>
                        <p class="vehicle-card-caption">Renseignez les données administratives et techniques.</p>
                    </div>
                    <span class="vehicle-icon-pill"><i data-feather="truck"></i></span>
                </div>
                <div class="vehicle-card-body">
                    <form action="{{ route('vehicule.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row vehicle-form-grid">
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="matricule">Numéro d'immatriculation</label>
                                    <input type="text" class="form-control" id="matricule" name="matricule" value="{{ old('matricule') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="carte_grise">Numéro de carte grise</label>
                                    <input type="text" class="form-control" id="carte_grise" name="carte_grise" value="{{ old('carte_grise') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="type_de_vehicule_id">Type de véhicule</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="type_de_vehicule_id" id="type_de_vehicule_id" required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach ($type_de_vehicules as $type_de_vehicule)
                                            <option value="{{ $type_de_vehicule->id }}" {{ old('type_de_vehicule_id') == $type_de_vehicule->id ? 'selected' : '' }}>{{ $type_de_vehicule->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="marque_id">Marque</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="marque_id" id="marque_id" required>
                                        <option value="">Sélectionnez une marque</option>
                                        @foreach ($marques as $marque)
                                            <option value="{{ $marque->id }}" {{ old('marque_id') == $marque->id ? 'selected' : '' }}>{{ $marque->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="type_de_carburant_id">Type d'énergie</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="type_de_carburant_id" id="type_de_carburant_id" required>
                                        <option value="">Sélectionnez une énergie</option>
                                        @foreach ($type_de_carburants as $type_de_carburant)
                                            <option value="{{ $type_de_carburant->id }}" {{ old('type_de_carburant_id') == $type_de_carburant->id ? 'selected' : '' }}>{{ $type_de_carburant->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="couleur_vehicule_id">Couleur</label>
                                    <select class="form-control selectpicker" data-live-search="true" name="couleur_vehicule_id" id="couleur_vehicule_id" required>
                                        <option value="">Sélectionnez une couleur</option>
                                        @foreach ($couleur_vehicules as $couleur_vehicule)
                                            <option value="{{ $couleur_vehicule->id }}" {{ old('couleur_vehicule_id') == $couleur_vehicule->id ? 'selected' : '' }}>{{ $couleur_vehicule->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="modele">Modèle</label>
                                    <input type="text" class="form-control" id="modele" name="modele" value="{{ old('modele') }}" required>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="vehicleFonctionId">Fonction</label>
                                    <select class="form-control selectpicker" data-live-search="true" id="vehicleFonctionId">
                                        <option value="">Sélectionnez une fonction</option>
                                        @foreach ($fonctions as $fonction)
                                            <option value="{{ $fonction->id }}" {{ old('fonction_id') == $fonction->id ? 'selected' : '' }}>{{ $fonction->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6 col-12">
                                <div class="form-group">
                                    <label for="vehicleChauffeurId">Utilisateur affecté <span class="text-danger">*</span></label>
                                    <select class="form-control" name="chauffeur_id" id="vehicleChauffeurId" data-selected="{{ old('chauffeur_id') }}" required>
                                        <option value="">Sélectionnez une fonction</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="photos">Photos du véhicule</label>
                                    <div class="vehicle-upload">
                                        <input multiple type="file" class="form-control" name="photos[]" id="photos" accept="image/*" required>
                                        <p class="vehicle-upload-hint">4 photos requises. Formats image acceptés, 2 Mo maximum par fichier.  </p>
                                        <div class="vehicle-preview-grid" id="vehiclePhotoPreview"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="vehicle-action-row">
                            <a href="{{ route('vehicule.index') }}" class="btn vehicle-btn-secondary">Annuler</a>
                            <button class="btn vehicle-btn-primary" type="submit">
                                <i data-feather="save"></i>
                                Enregistrer le véhicule
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

        var photoInput = document.getElementById('photos');
        var preview = document.getElementById('vehiclePhotoPreview');

        if (photoInput && preview) {
            photoInput.addEventListener('change', function (event) {
                preview.innerHTML = '';

                Array.from(event.target.files || []).slice(0, 4).forEach(function (file) {
                    if (!file.type || !file.type.startsWith('image/')) {
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function (readerEvent) {
                        var item = document.createElement('div');
                        item.className = 'vehicle-preview-item';
                        item.innerHTML = '<img src="' + readerEvent.target.result + '" alt="Aperçu photo"><span>' + file.name + '</span>';
                        preview.appendChild(item);
                    };
                    reader.readAsDataURL(file);
                });
            });
        }

        var fonctionSelect = document.getElementById('vehicleFonctionId');
        var chauffeurSelect = document.getElementById('vehicleChauffeurId');

        function refreshVehicleChauffeurSelect() {
            if (window.jQuery && jQuery.fn.selectpicker) {
                jQuery(chauffeurSelect).selectpicker('refresh');
            }
        }

        function setVehicleChauffeurOptions(optionsHtml) {
            chauffeurSelect.innerHTML = optionsHtml;
            refreshVehicleChauffeurSelect();
        }

        function loadVehicleChauffeursByFonction(fonctionId) {
            var fonctionLabel = fonctionSelect.options[fonctionSelect.selectedIndex]?.text || '';
            console.log('[Vehicule] Fonction sélectionnée:', {
                id: fonctionId,
                libelle: fonctionLabel
            });

            setVehicleChauffeurOptions('<option value="">Chargement...</option>');

            if (!fonctionId) {
                console.log('[Vehicule] Aucune fonction sélectionnée, liste utilisateurs vidée.');
                setVehicleChauffeurOptions('<option value="">Sélectionnez une fonction</option>');
                return;
            }

            fetch('{{ url('/chauffeurs-by-fonction') }}/' + encodeURIComponent(fonctionId), {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(function (response) {
                    return response.text().then(function (text) {
                        if (!response.ok) {
                            console.error('[Vehicule] Réponse serveur brute:', text);
                            throw new Error('Erreur HTTP ' + response.status);
                        }

                        try {
                            return JSON.parse(text);
                        } catch (error) {
                            var jsonStart = text.search(/[\[{]/);

                            if (jsonStart !== -1) {
                                try {
                                    return JSON.parse(text.slice(jsonStart));
                                } catch (secondError) {
                                    console.error('[Vehicule] Réponse non JSON brute:', text);
                                    throw secondError;
                                }
                            }

                            console.error('[Vehicule] Réponse non JSON brute:', text);
                            throw error;
                        }
                    });
                })
                .then(function (chauffeurs) {
                    console.log('[Vehicule] Chauffeurs chargés pour la fonction:', {
                        fonction_id: fonctionId,
                        total: Array.isArray(chauffeurs) ? chauffeurs.length : 0,
                        chauffeurs: chauffeurs
                    });

                    var selectedChauffeur = chauffeurSelect.getAttribute('data-selected') || '';
                    var options = '<option value="">Sélectionnez un utilisateur</option>';

                    if (!Array.isArray(chauffeurs) || chauffeurs.length === 0) {
                        setVehicleChauffeurOptions('<option value="">Aucun utilisateur disponible</option>');
                        return;
                    }

                    chauffeurs.forEach(function (chauffeur) {
                        var shouldAutoSelect = !selectedChauffeur && chauffeurs.length === 1;
                        var selected = String(chauffeur.id) === String(selectedChauffeur) || shouldAutoSelect ? ' selected' : '';
                        options += '<option value="' + chauffeur.id + '"' + selected + '>' + chauffeur.nom + ' ' + chauffeur.prenoms + '</option>';
                    });

                    setVehicleChauffeurOptions(options);

                    if (!selectedChauffeur && chauffeurs.length === 1) {
                        chauffeurSelect.value = chauffeurs[0].id;
                    }
                })
                .catch(function (error) {
                    console.error('[Vehicule] Erreur chargement chauffeurs:', error);
                    setVehicleChauffeurOptions('<option value="">Impossible de charger les utilisateurs</option>');
                });
        }

        if (fonctionSelect && chauffeurSelect) {
            fonctionSelect.addEventListener('change', function () {
                chauffeurSelect.setAttribute('data-selected', '');
                loadVehicleChauffeursByFonction(this.value);
            });

            if (window.jQuery) {
                jQuery(fonctionSelect).on('changed.bs.select', function () {
                    chauffeurSelect.setAttribute('data-selected', '');
                    loadVehicleChauffeursByFonction(this.value);
                });
            }

            if (fonctionSelect.value) {
                loadVehicleChauffeursByFonction(fonctionSelect.value);
            } else {
                refreshVehicleChauffeurSelect();
            }
        }
    });
</script>
