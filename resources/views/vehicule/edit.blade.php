@php
    $jsonImages = $vehicule->photos;
    $imagePaths = json_decode($jsonImages, true);
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
        <div class="row mb-2">
            <h5 class="card-title">Modifier un véhicule</h5>
        </div>
        <div class="card">
            <div class="modal-body">
                <form action="{{ route('vehicule.update', ['id' => $vehicule->id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="matricule">Matricule <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="matricule" value="{{ $vehicule->matricule }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="carte_grise">Carte grise <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="carte_grise" value="{{ $vehicule->carte_grise }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_de_vehicule_id">Type de véhicule <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control selectpicker" data-live-search="true" name="type_de_vehicule_id" id="type_de_vehicule_id" required>
                                        <option value="">Sélectionner un type de véhicule</option>
                                        @foreach ($type_de_vehicules as $type_de_vehicule)
                                            <option value="{{ $type_de_vehicule->id }}" {{ $type_de_vehicule->id == $vehicule->type_de_vehicule_id ? 'selected' : '' }}>{{ $type_de_vehicule->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="marque_id">Marque du véhicule <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control selectpicker" data-live-search="true" name="marque_id" id="marque_id" required>
                                        <option value="">Sélectionner une marque</option>
                                        @foreach ($marques as $marque)
                                            <option value="{{ $marque->id }}" {{ $marque->id == $vehicule->marque_id ? 'selected' : '' }}>{{ $marque->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type_de_carburant_id">Type d'énergie <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control selectpicker" data-live-search="true" name="type_de_carburant_id" id="type_de_carburant_id" required>
                                        <option value="">Sélectionner un type d'énergie</option>
                                        @foreach ($type_de_carburants as $type_de_carburant)
                                            <option value="{{ $type_de_carburant->id }}" {{ $type_de_carburant->id == $vehicule->type_de_carburant_id ? 'selected' : '' }}>{{ $type_de_carburant->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modele">Modèle du véhicule <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="modele" value="{{ $vehicule->modele }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="couleur_vehicule_id">La couleur du véhicule <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control selectpicker" data-live-search="true" name="couleur_vehicule_id" id="couleur_vehicule_id" required>
                                        <option value="">Sélectionner une couleur</option>
                                        @foreach ($couleur_vehicules as $couleur_vehicule)
                                            <option value="{{ $couleur_vehicule->id }}" {{ $couleur_vehicule->id == $vehicule->couleur_vehicule_id ? 'selected' : '' }}>{{ $couleur_vehicule->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fonctionId">Fonction <span class="text-danger">*</span></label>
                                <select class="form-control selectpicker" data-live-search="true" id="fonctionId" required>
                                    <option value="">Sélectionner une fonction</option>
                                    @foreach ($fonctions as $fonction)
                                        <option value="{{ $fonction->id }}" {{ $fonction->id == ($vehicule->chauffeur->fonction_id ?? null) ? 'selected' : '' }}>{{ $fonction->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chauffeur_id">Chauffeur <span class="text-danger">*</span></label>
                                <select name="chauffeur_id" class="form-control selectpicker" data-live-search="true" required>
                                    <option value="">Sélectionner un chauffeur</option>
                                    @foreach ($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" {{ $chauffeur->id == $vehicule->user_id ? 'selected' : '' }}>
                                            {{ $chauffeur->nom }} {{ $chauffeur->prenoms }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="photos">Photos</label>
                                <input type="file" class="form-control" name="photos[]" id="photos" multiple accept="image/*">
                                <small class="text-muted">Ajoutez de nouvelles photos. Formats acceptés : JPG, PNG, GIF</small>
                            </div>
                            
                            <!-- Photos existantes avec boutons de suppression -->
                            <div class="row mt-3" id="existing-photos">
                                @if($imagePaths && count($imagePaths) > 0)
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($imagePaths as $photo)
                                            @php
                                                $imageUrl = $vehiclePhotoUrl($photo, $vehicule);
                                            @endphp
                                            <img src="{{ $imageUrl }}" alt="Photo" class="rounded border" style="width:200px;height:200px;object-fit:cover;cursor:pointer" data-bs-toggle="modal" data-bs-target="#show{{ $vehicule->id }}">
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">Aucune</span>
                                @endif
                            </div>
                            
                            <!-- Aperçu des nouvelles photos -->
                            <div class="row mt-3" id="new-photos-preview" style="display: none;">
                                <div class="col-12">
                                    <h6>Nouvelles photos à ajouter :</h6>
                                </div>
                            </div>
                            
                            <!-- Champ caché pour stocker les photos supprimées -->
                            <input type="hidden" name="deleted_photos" id="deleted_photos" value="">
                        </div>
                    </div>
                    <div class="modal-footer text-center">
                        <a href="{{ route('vehicule.index') }}" class="btn btn-cancel">Annuler</a>
                        <button type="submit" class="btn btn-submit">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
document.addEventListener('DOMContentLoaded', function() {
    let deletedPhotos = [];
    
    // Gestion de la suppression des photos existantes
    document.querySelectorAll('.remove-photo').forEach(button => {
        button.addEventListener('click', function() {
            const photoPath = this.getAttribute('data-photo');
            const photoItem = this.closest('.photo-item');
            
            // Ajouter à la liste des photos supprimées
            deletedPhotos.push(photoPath);
            document.getElementById('deleted_photos').value = JSON.stringify(deletedPhotos);
            
            // Supprimer visuellement l'élément
            photoItem.remove();
            
            // Vérifier s'il reste des photos
            const remainingPhotos = document.querySelectorAll('#existing-photos .photo-item');
            if (remainingPhotos.length === 0) {
                document.getElementById('existing-photos').innerHTML = '<div class="col-12"><p class="text-muted">Aucune photo existante</p></div>';
            }
        });
    });
    
    // Gestion de l'aperçu des nouvelles photos
    document.getElementById('photos').addEventListener('change', function(e) {
        const files = e.target.files;
        const previewContainer = document.getElementById('new-photos-preview');
        
        // Vider le conteneur d'aperçu
        previewContainer.innerHTML = '<div class="col-12"><h6>Nouvelles photos à ajouter :</h6></div>';
        
        if (files.length > 0) {
            previewContainer.style.display = 'block';
            
            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 mb-3';
                        col.innerHTML = `
                            <div class="card">
                                <img src="${e.target.result}" alt="Aperçu" class="img-fluid rounded">
                                <div class="card-body p-2">
                                    <small class="text-muted">${file.name}</small>
                                </div>
                            </div>
                        `;
                        previewContainer.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        } else {
            previewContainer.style.display = 'none';
        }
    });
});
</script>
