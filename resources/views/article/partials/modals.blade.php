@php
    $fullDescription = html_entity_decode($item->description);
@endphp
<!-- Modal Détail -->
<div class="modal fade" id="view{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye"></i> Détails de l'article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6 class="fw-bold">Nom :</h6>
                <p>{{ $item->libelle }}</p>
                <h6 class="fw-bold">Prix :</h6>
                <p>{{ $item->amount }} F CFA</p>
                <h6 class="fw-bold">Description :</h6>
                <p>{!! $fullDescription !!}</p>
                <h6 class="fw-bold">Photo :</h6>
                @if (!empty($item->image))
                    <img src="/images/article/{{ $item->image }}" alt="Photo article" class="img-fluid rounded border mb-2" style="max-width:300px;">
                @else
                    <span class="text-muted">Aucune image</span>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Edition -->
<div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Modifier l'article</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('update.article', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="libelle{{ $item->id }}" class="form-label">Nom de l'article</label>
                        <input type="text" class="form-control" id="libelle{{ $item->id }}" name="libelle" value="{{ $item->libelle }}">
                    </div>
                    <div class="mb-3">
                        <label for="amount{{ $item->id }}" class="form-label">Prix (F CFA)</label>
                        <input type="number" class="form-control" id="amount{{ $item->id }}" name="amount" value="{{ $item->amount }}">
                    </div>
                    <div class="mb-3">
                        <label for="image{{ $item->id }}" class="form-label">Photo</label>
                        <input type="file" class="form-control" id="image{{ $item->id }}" name="image">
                        @if (!empty($item->image))
                            <img src="/images/article/{{ $item->image }}" alt="Photo article" class="img-fluid rounded border mt-2" style="max-width:200px;">
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="description{{ $item->id }}" class="form-label">Description</label>
                        <textarea class="form-control" id="description{{ $item->id }}" name="description" rows="3">{!! $fullDescription !!}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
