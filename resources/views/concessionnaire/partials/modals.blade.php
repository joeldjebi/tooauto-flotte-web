<!-- Modal Prise de RDV -->
<div class="modal fade" id="addRDV{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content dealer-modal">
            <form action="{{ route('store.concessionnaire-rdv') }}" method="post">
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="dealer-modal-kicker">Rendez-vous</span>
                        <h5 class="modal-title">Planifier avec {{ $item->name }}</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="dealer-modal-section">
                        <div class="mb-3">
                            <label for="jour{{ $item->id }}" class="form-label">Jour du RDV</label>
                            <select class="form-control" name="jour" id="jour{{ $item->id }}" required>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                                <option value="Samedi">Samedi</option>
                                <option value="Dimanche">Dimanche</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label for="heure{{ $item->id }}" class="form-label">Date et heure</label>
                            <input type="datetime-local" class="form-control" name="heure" id="heure{{ $item->id }}" required>
                            <input type="hidden" name="concessionnaire_id" value="{{ $item->id }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Demande Spécifique -->
<div class="modal fade" id="addDemande{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content dealer-modal">
            <form action="{{ route('store.concessionnaire-demande') }}" method="post">
                @csrf
                <input type="hidden" name="concessionnaire_id" value="{{ $item->id }}">
                <div class="modal-header">
                    <div>
                        <span class="dealer-modal-kicker">Demande spécifique</span>
                        <h5 class="modal-title">{{ $item->name }}</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="dealer-modal-section">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="type_de_demande_id{{ $item->id }}" class="form-label">Type de demande</label>
                                <select class="form-control" name="type_de_demande_id" id="type_de_demande_id{{ $item->id }}" required>
                                    @foreach ($type_de_demandes as $type_de_demande)
                                        <option value="{{ $type_de_demande->id }}">{{ $type_de_demande->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="type_de_vehicule_id{{ $item->id }}" class="form-label">Type de véhicule</label>
                                <select class="form-control" name="type_de_vehicule_id" id="type_de_vehicule_id{{ $item->id }}" required>
                                    @foreach ($type_de_vehicules as $type_de_vehicule)
                                        <option value="{{ $type_de_vehicule->id }}">{{ $type_de_vehicule->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="marque_id{{ $item->id }}" class="form-label">Marque</label>
                                <select class="form-control" name="marque_id" id="marque_id{{ $item->id }}" required>
                                    @foreach ($marques as $marque)
                                        <option value="{{ $marque->id }}">{{ $marque->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="modele{{ $item->id }}" class="form-label">Modèle</label>
                                <input type="text" class="form-control" name="modele" id="modele{{ $item->id }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('index.demande-concessionnaire') }}" class="btn btn-outline-secondary">Liste des demandes</a>
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
