@php
    $isEdit = !empty($reparation);
    $modalId = $isEdit ? 'editReparation' . $reparation->id : 'addReparation';
    $action = $isEdit ? route('alerte.reparation.update', $reparation->id) : route('alerte.reparation.store');
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
                    <h5 class="modal-title">{{ $isEdit ? 'Modifier un dossier de réparation' : 'Ajouter un dossier de réparation' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Dossier</span>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Véhicule *</label>
                                <select name="vehicule_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" @selected(old('vehicule_id', $reparation->vehicule_id ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Utilisateur</label>
                                <select name="chauffeur_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" @selected(old('chauffeur_id', $reparation->chauffeur_id ?? '') == $chauffeur->id)>{{ $chauffeur->nom }} {{ $chauffeur->prenoms }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Assistance liée</label>
                                <select name="assistance_id" class="form-control">
                                    <option value="">Aucune</option>
                                    @foreach($assistances as $assistance)
                                        <option value="{{ $assistance->id }}" @selected(old('assistance_id', $reparation->assistance_id ?? '') == $assistance->id)>#{{ $assistance->id }} - {{ $assistance->titre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Titre *</label>
                                <input type="text" name="titre" class="form-control" value="{{ old('titre', $reparation->titre ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prestataire</label>
                                <select name="prestataire_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($prestataires as $prestataire)
                                        <option value="{{ $prestataire->id }}" @selected(old('prestataire_id', $reparation->prestataire_id ?? '') == $prestataire->id)>{{ $prestataire->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Diagnostic et proforma</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Description de la panne</label>
                                <textarea name="description_panne" class="form-control" rows="3">{{ old('description_panne', $reparation->description_panne ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Diagnostic</label>
                                <textarea name="diagnostic" class="form-control" rows="3">{{ old('diagnostic', $reparation->diagnostic ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Référence proforma</label>
                                <input type="text" name="proforma_reference" class="form-control" value="{{ old('proforma_reference', $reparation->proforma_reference ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Montant proforma</label>
                                <input type="number" step="0.01" name="proforma_montant" class="form-control" value="{{ old('proforma_montant', $reparation->proforma_montant ?? '') }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Validation financière *</label>
                                <select name="validation_financiere" class="form-control" required>
                                    @foreach($validations as $value => $label)
                                        <option value="{{ $value }}" @selected(old('validation_financiere', $reparation->validation_financiere ?? 'en_attente') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Suivi</span>
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Date d'entrée</label>
                                <input type="date" name="date_entree" class="form-control" value="{{ old('date_entree', $reparation->date_entree ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Sortie prévue</label>
                                <input type="date" name="date_sortie_prevue" class="form-control" value="{{ old('date_sortie_prevue', $reparation->date_sortie_prevue ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date de sortie</label>
                                <input type="date" name="date_sortie" class="form-control" value="{{ old('date_sortie', $reparation->date_sortie ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Coût final</label>
                                <input type="number" step="0.01" name="cout_final" class="form-control" value="{{ old('cout_final', $reparation->cout_final ?? '') }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut *</label>
                                <select name="statut" class="form-control" required>
                                    @foreach($statuts as $value => $label)
                                        <option value="{{ $value }}" @selected(old('statut', $reparation->statut ?? 'nouveau') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="2">{{ old('commentaire', $reparation->commentaire ?? '') }}</textarea>
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
