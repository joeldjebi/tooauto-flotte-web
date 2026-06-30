@php
    $isEdit = !empty($assistance);
    $modalId = $isEdit ? 'editAssistance' . $assistance->id : 'addAssistance';
    $action = $isEdit ? route('alerte.assistance.update', $assistance->id) : route('alerte.assistance.store');
    $dateDemande = old('date_demande', $assistance->date_demande ?? '');
    $dateIntervention = old('date_intervention', $assistance->date_intervention ?? '');
    $dateCloture = old('date_cloture', $assistance->date_cloture ?? '');
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
                    <h5 class="modal-title">{{ $isEdit ? 'Modifier une assistance' : 'Ajouter une assistance' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Demande</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Véhicule *</label>
                                <select name="vehicule_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" @selected(old('vehicule_id', $assistance->vehicule_id ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Utilisateur</label>
                                <select name="chauffeur_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($chauffeurs as $chauffeur)
                                        <option value="{{ $chauffeur->id }}" @selected(old('chauffeur_id', $assistance->chauffeur_id ?? '') == $chauffeur->id)>{{ $chauffeur->nom }} {{ $chauffeur->prenoms }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Type d'assistance *</label>
                                <select name="type_assistance_id" class="form-control" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($type_assistances as $type)
                                        <option value="{{ $type->id }}" @selected(old('type_assistance_id') == $type->id || (!old('type_assistance_id') && ($assistance->type_assistance ?? '') === $type->libelle))>{{ $type->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Titre *</label>
                                <input type="text" name="titre" class="form-control" value="{{ old('titre', $assistance->titre ?? '') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Localisation et priorité</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Lieu</label>
                                <input type="text" name="lieu" class="form-control" value="{{ old('lieu', $assistance->lieu ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="0.0000001" name="latitude" class="form-control" value="{{ old('latitude', $assistance->latitude ?? '') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="0.0000001" name="longitude" class="form-control" value="{{ old('longitude', $assistance->longitude ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Urgence *</label>
                                <select name="niveau_urgence" class="form-control" required>
                                    @foreach($urgences as $value => $label)
                                        <option value="{{ $value }}" @selected(old('niveau_urgence', $assistance->niveau_urgence ?? 'moyen') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Statut *</label>
                                <select name="statut" class="form-control" required>
                                    @foreach($statuts as $value => $label)
                                        <option value="{{ $value }}" @selected(old('statut', $assistance->statut ?? 'nouvelle') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Prestataire</label>
                                <select name="prestataire_id" class="form-control">
                                    <option value="">Non renseigné</option>
                                    @foreach($prestataires as $prestataire)
                                        <option value="{{ $prestataire->id }}" @selected(old('prestataire_id', $assistance->prestataire_id ?? '') == $prestataire->id || (!old('prestataire_id') && ($assistance->prestataire_nom ?? '') === $prestataire->name))>{{ $prestataire->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Dates</span>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Date demande</label>
                                <input type="datetime-local" name="date_demande" class="form-control" value="{{ $dateDemande ? \Carbon\Carbon::parse($dateDemande)->format('Y-m-d\TH:i') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date intervention</label>
                                <input type="datetime-local" name="date_intervention" class="form-control" value="{{ $dateIntervention ? \Carbon\Carbon::parse($dateIntervention)->format('Y-m-d\TH:i') : '' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Date clôture</label>
                                <input type="datetime-local" name="date_cloture" class="form-control" value="{{ $dateCloture ? \Carbon\Carbon::parse($dateCloture)->format('Y-m-d\TH:i') : '' }}">
                            </div>
                        </div>
                    </div>
                    <div class="fleet-form-section mb-0">
                        <span class="fleet-form-section-title">Notes</span>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description', $assistance->description ?? '') }}</textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Commentaire</label>
                                <textarea name="commentaire" class="form-control" rows="3">{{ old('commentaire', $assistance->commentaire ?? '') }}</textarea>
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
