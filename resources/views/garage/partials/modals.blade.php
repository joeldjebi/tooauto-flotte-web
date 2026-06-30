<div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content fleet-module-modal">
            <form action="{{ route('update.garage', ['id' => $item->id]) }}" method="post">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Modifier un prestataire</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="fleet-form-section mb-0">
                        <span class="fleet-form-section-title">Informations</span>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nom du prestataire *</label>
                                <input type="text" class="form-control" name="name" value="{{ $item->name }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Adresse</label>
                                <input type="text" class="form-control" name="adresse" value="{{ $item->adresse }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Adresse map</label>
                                <input type="text" class="form-control" name="adresse_map" value="{{ $item->adresse_map }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Numéro de téléphone</label>
                                <input type="text" class="form-control" name="contact" value="{{ $item->contact }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermer</button>
                    <button class="btn btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
