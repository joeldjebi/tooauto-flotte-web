@php
    $isEdit = !empty($carburant);
    $modalId = $isEdit ? 'editCarburant' . $carburant->id : 'addCarburant';
    $action = $isEdit ? route('alerte.carburant.update', $carburant->id) : route('alerte.carburant.store');
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content fleet-module-modal">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Modifier un approvisionnement' : 'Ajouter un approvisionnement' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Approvisionnement</span>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Véhicule *</label>
                                <select name="vehicule_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" @selected(old('vehicule_id', $carburant->vehicule_id ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Utilisateur</label>
                                <select name="chauffeur_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" @selected(old('chauffeur_id', $carburant->chauffeur_id ?? '') == $chauffeur->id)>{{ $chauffeur->nom }} {{ $chauffeur->prenoms }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Type carburant *</label>
                                <select name="type_de_carburant_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($type_de_carburants as $type)
                                        <option value="{{ $type->id }}" @selected(old('type_de_carburant_id', $carburant->type_de_carburant_id ?? '') == $type->id)>{{ $type->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date *</label>
                                <input type="date" name="date_approvisionnement" class="form-control" value="{{ old('date_approvisionnement', $carburant->date_approvisionnement ?? now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kilométrage</label>
                                <input type="number" name="kilometrage" class="form-control" value="{{ old('kilometrage', $carburant->kilometrage ?? '') }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Mode paiement *</label>
                                <select name="mode_paiement" class="form-control" required>
                                    @foreach($modesPaiement as $value => $label)
                                        <option value="{{ $value }}" @selected(old('mode_paiement', $carburant->mode_paiement ?? 'espece') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Coûts</span>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Quantité litres *</label>
                                <input type="number" step="0.01" name="quantite_litres" class="form-control" value="{{ old('quantite_litres', $carburant->quantite_litres ?? '') }}" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prix unitaire *</label>
                                <input type="number" step="0.01" name="prix_unitaire" class="form-control" value="{{ old('prix_unitaire', $carburant->prix_unitaire ?? '') }}" min="0" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Montant total</label>
                                <input type="number" step="0.01" name="montant_total" class="form-control" value="{{ old('montant_total', $carburant->montant_total ?? '') }}" min="0">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Station / Fournisseur</label>
                                <input type="text" name="station" class="form-control" value="{{ old('station', $carburant->station ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Référence</label>
                                <input type="text" name="reference" class="form-control" value="{{ old('reference', $carburant->reference ?? '') }}">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="2">{{ old('commentaire', $carburant->commentaire ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
