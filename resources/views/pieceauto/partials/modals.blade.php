<div class="modal fade" id="view{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content piece-modal">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la pièce</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-5">
                        <div class="piece-modal-section h-100">
                            @if (!empty($item->image))
                                <img src="{{ $item->image_url ?? asset('assets/img/default-car.png') }}" alt="Photo pièce" class="w-100" style="aspect-ratio:4/3;object-fit:cover;border-radius:8px;border:1px solid #e5eaf1;">
                            @else
                                <div class="piece-empty">Aucune photo.</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="piece-modal-section h-100">
                            <h6 class="mb-3" style="color:#130d0d;font-weight:900;">{{ $item->libelle }}</h6>
                            <div class="row g-2">
                                <div class="col-md-6"><strong>Type :</strong><br>{{ $item->type_de_piece->libelle ?? '-' }}</div>
                                <div class="col-md-6"><strong>Marque :</strong><br>{{ $item->marque->libelle ?? '-' }}</div>
                                <div class="col-md-6"><strong>Catégorie :</strong><br>{{ $item->categorie_piece->libelle ?? '-' }}</div>
                                <div class="col-md-6"><strong>Sous-catégorie :</strong><br>{{ $item->sous_categorie_piece->libelle ?? '-' }}</div>
                                <div class="col-md-12"><strong>Modèle :</strong><br>{{ $item->modele }}</div>
                                <div class="col-md-12"><strong>Description :</strong><br>{!! html_entity_decode($item->description) !!}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                <a href="{{ route('edit.annonce', $item->id) }}" class="btn btn-warning">Modifier</a>
            </div>
        </div>
    </div>
</div>
