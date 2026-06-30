{{-- Modal Détail --}}
<div class="modal fade" id="show{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content" style="border:0;border-radius:8px;overflow:hidden;">
            <div class="modal-header" style="background:#f8fafc;border-bottom:1px solid #e8edf4;">
                <div>
                    <h5 class="modal-title" style="color:#111827;font-weight:800;">{{ $item->matricule }}</h5>
                    <div style="color:#64748b;font-size:13px;">{{ $item->marque->libelle ?? '-' }} {{ $item->modele }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="background:#ffffff;padding:22px;">
                @php $imagePaths = json_decode($item->photos, true) ?: []; @endphp

                @if(count($imagePaths) > 0)
                    <div class="row mb-4">
                        @foreach($imagePaths as $photo)
                            <div class="col-md-3 col-6 mb-3">
                                <img src="{{ $vehiclePhotoUrl($photo, $item) }}" alt="Photo véhicule" class="w-100" style="aspect-ratio:4/3;object-fit:cover;border-radius:8px;border:1px solid #e8edf4;">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mb-4" style="background:#f8fafc;border:1px dashed #d9e2ef;border-radius:8px;color:#64748b;padding:18px;text-align:center;">Aucune photo disponible.</div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="vehicle-detail-tile">
                            <span>Carte grise</span>
                            <strong>{{ $item->carte_grise }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="vehicle-detail-tile">
                            <span>Utilisateur</span>
                            <strong>{{ $item->chauffeur->nom ?? '-' }} {{ $item->chauffeur->prenoms ?? '' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="vehicle-detail-tile">
                            <span>Fonction</span>
                            <strong>{{ $item->chauffeur->fonction->libelle ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="vehicle-detail-tile">
                            <span>Type</span>
                            <strong>{{ $item->type_de_vehicule->libelle ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="vehicle-detail-tile">
                            <span>Énergie</span>
                            <strong>{{ $item->type_de_carburant->libelle ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="vehicle-detail-tile">
                            <span>Couleur</span>
                            <strong>{{ $item->couleur_vehicule->libelle ?? $item->couleur }}</strong>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #e8edf4;">
                <a href="{{ route('vehicule.edit', $item->id) }}" class="btn btn-primary" style="border-radius:8px;font-weight:800;">Modifier</a>
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;font-weight:800;">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
    .vehicle-detail-tile {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 14px;
    }

    .vehicle-detail-tile span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .vehicle-detail-tile strong {
        color: #111827;
        display: block;
        font-size: 14px;
        font-weight: 800;
    }
</style>
