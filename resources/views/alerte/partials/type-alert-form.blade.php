<div class="modal fade" id="addTypeAlert" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content fleet-module-modal">
            <form method="POST" action="{{ route('alerte.store') }}">
                @csrf
                <input type="hidden" name="type_alert_id" value="{{ $alertTypeId }}">
                <div class="modal-header">
                    <h5 class="modal-title">Créer une alerte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fleet-form-section">
                        <span class="fleet-form-section-title">Informations</span>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Véhicule *</label>
                                <select name="vehicule_id" class="form-control" required>
                                    <option value="">Sélectionner un véhicule</option>
                                    @foreach($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" @selected(old('vehicule_id') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de début *</label>
                                <input type="date" name="date_debut" class="form-control" value="{{ old('date_debut', now()->format('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Date de fin *</label>
                                <input type="date" name="date_fin" class="form-control" value="{{ old('date_fin') }}" required>
                            </div>
                            @if($showKilometrage)
                                <div class="col-md-12">
                                    <label class="form-label">Kilométrage *</label>
                                    <input type="number" name="kilometrage" class="form-control" value="{{ old('kilometrage') }}" min="0" required>
                                </div>
                            @endif
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
