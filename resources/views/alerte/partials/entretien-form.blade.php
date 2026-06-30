@php
    $isEdit = !empty($entretien);
    $modalId = $isEdit ? 'editEntretien' . $entretien->id : 'addEntretien';
    $action = $isEdit ? route('alerte.entretien.update', $entretien->id) : route('alerte.entretien.store');
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content fleet-module-modal">
            <form method="POST" action="{{ $action }}">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Modifier un entretien' : 'Ajouter un entretien' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Véhicule et opération</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Véhicule *</label>
                                <select name="vehicule_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" @selected(old('vehicule_id', $entretien->vehicule_id ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Utilisateur</label>
                                <select name="chauffeur_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" @selected(old('chauffeur_id', $entretien->chauffeur_id ?? '') == $chauffeur->id)>{{ $chauffeur->nom }} {{ $chauffeur->prenoms }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type d'entretien *</label>
                                <select name="type_entretien_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($type_entretiens as $type)
                                        <option value="{{ $type->id }}" @selected(old('type_entretien_id') == $type->id || (!old('type_entretien_id') && ($entretien->type_entretien ?? '') === $type->libelle))>{{ $type->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Titre *</label>
                                <input type="text" name="titre" class="form-control" value="{{ old('titre', $entretien->titre ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Planification et suivi</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Date prévue</label>
                                <input type="date" name="date_prevue" class="form-control" value="{{ old('date_prevue', $entretien->date_prevue ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de réalisation</label>
                                <input type="date" name="date_realisation" class="form-control" value="{{ old('date_realisation', $entretien->date_realisation ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Kilométrage</label>
                                <input type="number" name="kilometrage" class="form-control" value="{{ old('kilometrage', $entretien->kilometrage ?? '') }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Coût</label>
                                <input type="number" step="0.01" name="cout" class="form-control" value="{{ old('cout', $entretien->cout ?? '') }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut *</label>
                                <select name="statut" class="form-control" required>
                                    @foreach($statuts as $value => $label)
                                        <option value="{{ $value }}" @selected(old('statut', $entretien->statut ?? 'planifie') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Prestataire</label>
                                <select name="prestataire_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($prestataires as $prestataire)
                                        <option value="{{ $prestataire->id }}" @selected(old('prestataire_id') == $prestataire->id || (!old('prestataire_id') && ($entretien->prestataire ?? '') === $prestataire->name))>{{ $prestataire->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="fleet-form-section mb-0">
                        <span class="fleet-form-section-title">Notes</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $entretien->description ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="3">{{ old('commentaire', $entretien->commentaire ?? '') }}</textarea>
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
