{{-- Modals pour un chauffeur --}}
{{-- Modal Détail --}}
<div class="modal fade" id="show{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Détails du chauffeur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <div class="mb-2">
                        @if($item->vehicules->isNotEmpty())
                        @foreach ($item->vehicules as $vehicule)
                            @php $imagePaths = json_decode($vehicule->photos, true); @endphp
                            @if($imagePaths && count($imagePaths) > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($imagePaths as $photo)
                                        @php
                                            $imageUrl = $vehicule->photo_url_map[$photo] ?? asset('assets/img/default-car.png');
                                        @endphp
                                        <img src="{{ $imageUrl }}" alt="Photo" class="rounded border" style="width:200px;height:200px;object-fit:cover;cursor:pointer" data-bs-toggle="modal" data-bs-target="#show{{ $vehicule->id }}">
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">Aucune</span>
                            @endif
                        @endforeach
                    @else
                        <span class="text-muted">Aucun véhicule associé à ce chauffeur.</span>
                    @endif
                    </div>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>Nom :</strong> {{ $item->nom }}</li>
                    <li class="list-group-item"><strong>Prénoms :</strong> {{ $item->prenoms }}</li>
                    <li class="list-group-item"><strong>Contact :</strong> <a href="tel:{{ $item->mobile }}">{{ $item->mobile }}</a></li>
                    <li class="list-group-item"><strong>Statut :</strong> <span class="badge bg-{{ $item->statut == 1 ? 'success' : 'danger' }}">{{ $item->statut == 1 ? 'Actif' : 'Non actif' }}</span></li>
                    <li class="list-group-item"><strong>Date d'enregistrement :</strong> {{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>
{{-- Modal Edition --}}
<div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Modifier un utilisateur</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('chauffeur.update', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label>Nom</label>
                        <input type="text" class="form-control" name="nom" value="{{ $item->nom }}">
                    </div>
                    <div class="mb-3">
                        <label>Prénoms</label>
                        <input type="text" class="form-control" name="prenoms" value="{{ $item->prenoms }}">
                    </div>
                    <div class="mb-3">
                        <label>Numéro de téléphone</label>
                        <div class="input-group">
                            <span class="input-group-text">225</span>
                            <input type="text" class="form-control" name="mobile" value="{{ $item->mobile }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Fonction</label>
                        <select class="form-control" name="fonction_id">
                            @foreach ($fonctions as $fonction)
                                <option value="{{ $fonction->id }}" {{ $fonction->id == $item->fonction_id ? 'selected' : '' }}>{{ $fonction->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Ville</label>
                        <select class="form-control" name="ville_id">
                            @foreach ($villes as $ville)
                                <option value="{{ $ville->id }}" {{ $ville->id == $item->ville_id ? 'selected' : '' }}>{{ $ville->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Photo</label>
                        <input type="file" class="form-control" name="image">
                        <div class="mt-2">
                            @if(!empty($item->image))
                                <img src="{{ $chauffeurImageUrl($item->image, $item) }}" width="60" height="60" class="rounded-circle border" style="object-fit:cover;" onerror="this.onerror=null;this.src='{{ asset('assets/img/profiles/avatar-01.jpg') }}'">
                            @else
                                Pas de photo
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-warning">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- Modal Véhicules --}}
<div class="modal fade" id="showVehicule{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Véhicules associés</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($item->vehicules->isNotEmpty())
                    <div class="row">
                        @foreach ($item->vehicules as $vehicule)
                            @php $imagePaths = json_decode($vehicule->photos, true); @endphp
                            <div class="col-md-12 mb-3">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="mb-2">
                                            @if($imagePaths && count($imagePaths) > 0)
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($imagePaths as $photo)
                                                        @php
                                                            $imageUrl = $vehicule->photo_url_map[$photo] ?? asset('assets/img/default-car.png');
                                                        @endphp
                                                        <img src="{{ $imageUrl }}" alt="Photo" class="rounded border" style="width:200px;height:200px;object-fit:cover;cursor:pointer" data-bs-toggle="modal" data-bs-target="#show{{ $item->id }}">
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted">Aucune</span>
                                            @endif
                                        </div>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item"><strong>Modèle :</strong> {{ $vehicule->modele }}</li>
                                            <li class="list-group-item"><strong>Immatriculation :</strong> {{ $vehicule->matricule }}</li>
                                            <li class="list-group-item"><strong>Marque :</strong> {{ $vehicule->marque->libelle ?? '-' }}</li>
                                            <li class="list-group-item"><strong>Type de véhicule :</strong> {{ $vehicule->type_de_vehicule->libelle ?? '-' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">Aucun véhicule associé à ce chauffeur.</div>
                @endif
            </div>
        </div>
    </div>
</div>
