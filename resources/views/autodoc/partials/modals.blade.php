@php
    $imagePaths = json_decode($item->images, true) ?: [];
@endphp

<div class="modal fade autodoc-modal" id="show{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">Détails du document</h5>
                    <div class="text-muted small">{{ $item->vehicule->matricule ?? '-' }} - {{ $item->type_docauto->libelle ?? '-' }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if(count($imagePaths) > 0)
                    <div class="row mb-4">
                        @foreach($imagePaths as $photo)
                            @php
                                $meta = $docFileMeta($photo);
                                $url = $docFileUrl($photo, $item);
                            @endphp
                            <div class="col-md-3 col-6 mb-3">
                                <a href="{{ $url }}" target="_blank" class="text-decoration-none">
                                    <div class="autodoc-file-preview {{ $meta['is_pdf'] ? 'pdf' : '' }}">
                                        @if($meta['is_image'])
                                            <img src="{{ $url }}" alt="Document">
                                        @elseif($meta['is_pdf'])
                                            <i data-feather="file-text"></i>
                                        @else
                                            <i data-feather="file"></i>
                                        @endif
                                    </div>
                                    <div class="text-muted small mt-2 text-center">{{ strtoupper($meta['extension']) }}</div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-muted text-center p-4">Aucun fichier disponible.</div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="autodoc-detail-tile">
                            <span>Véhicule</span>
                            <strong>{{ $item->vehicule->matricule ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="autodoc-detail-tile">
                            <span>Type de document</span>
                            <strong>{{ $item->type_docauto->libelle ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade autodoc-modal" id="edit{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Modifier un document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('autodoc.update', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>Type de document</label>
                                <select class="form-control" name="type_docauto_id" required>
                                    @foreach ($type_docautos as $type_autodoc)
                                        <option value="{{ $type_autodoc->id }}" {{ $type_autodoc->id == $item->type_docauto_id ? 'selected' : '' }}>{{ $type_autodoc->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>Véhicule</label>
                                <select name="vehicule_id" class="form-control" required>
                                    @foreach ($vehicules as $vehicule)
                                        <option value="{{ $vehicule->id }}" {{ $vehicule->id == $item->vehicule_id ? 'selected' : '' }}>
                                            {{ $vehicule->matricule }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Remplacer les fichiers</label>
                                <input type="file" class="form-control" name="images[]" multiple accept="image/*,.pdf,.heic,.heif">
                                <div class="text-muted small mt-2">Laissez vide pour conserver les fichiers actuels.</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn autodoc-btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn autodoc-btn-primary" type="submit">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .autodoc-file-preview {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e8edf4;
        border-radius: 8px;
        color: #475569;
        display: flex;
        height: 120px;
        justify-content: center;
        overflow: hidden;
        width: 100%;
    }

    .autodoc-file-preview.pdf {
        background: #fff1f2;
        color: #be123c;
    }

    .autodoc-file-preview img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .autodoc-file-preview svg {
        height: 34px;
        width: 34px;
    }

    .autodoc-detail-tile {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 14px;
    }

    .autodoc-detail-tile span {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .autodoc-detail-tile strong {
        color: #111827;
        display: block;
        font-size: 14px;
        font-weight: 800;
    }
</style>
