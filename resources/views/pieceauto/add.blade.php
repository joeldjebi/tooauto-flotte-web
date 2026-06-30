@include('layouts.header')
@include('layouts.menu')

@include('pieceauto.partials.form-styles')

<div class="page-wrapper piece-form-page">
    <div class="content">
        @include('layouts.fileariane')

        @if(session()->has("message"))
            <div style="padding: 10px" class="alert {{ session()->get('type') }}">{{ session()->get('message') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="piece-form-hero">
            <div>
                <span class="piece-form-kicker">Pièces & accessoires</span>
                <h4 class="piece-form-title">Nouvelle pièce</h4>
                <p class="piece-form-copy">Renseignez la pièce, associez-la à une marque et ajoutez une photo. L'image sera stockée sur Wasabi.</p>
            </div>
            <button type="button" class="btn piece-form-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </button>
        </div>

        <form action="{{ route('store.annonce') }}" method="post" enctype="multipart/form-data" class="piece-form-card">
            @csrf
            <div class="piece-form-section">
                <span class="piece-form-section-title">Informations</span>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom de la pièce *</label>
                        <input type="text" class="form-control" name="libelle" value="{{ old('libelle') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type de pièce *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="type_de_piece_id" required>
                            <option value="">Sélectionner</option>
                            @foreach ($type_de_pieces as $item)
                                <option value="{{ $item->id }}" @selected(old('type_de_piece_id') == $item->id)>{{ $item->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catégorie *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="categorie_piece_id" required>
                            <option value="">Sélectionner</option>
                            @foreach ($categorie_pieces as $item)
                                <option value="{{ $item->id }}" @selected(old('categorie_piece_id') == $item->id)>{{ $item->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sous-catégorie *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="sous_categorie_piece_id" required>
                            <option value="">Sélectionner</option>
                            @foreach ($sous_categorie_pieces as $item)
                                <option value="{{ $item->id }}" @selected(old('sous_categorie_piece_id') == $item->id)>{{ $item->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Marque véhicule *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="marque_id" required>
                            <option value="">Sélectionner</option>
                            @foreach ($marques as $item)
                                <option value="{{ $item->id }}" @selected(old('marque_id') == $item->id)>{{ $item->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Modèle *</label>
                        <input type="text" class="form-control" name="modele" value="{{ old('modele') }}" required>
                    </div>
                </div>
            </div>

            <div class="piece-form-section">
                <span class="piece-form-section-title">Photo et description</span>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Photo de la pièce</label>
                        <div class="piece-upload-box">
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small> Formats acceptés: JPG, PNG, GIF.</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="piece-form-footer">
                <button type="button" class="btn piece-form-secondary" onclick="history.back()">Annuler</button>
                <button class="btn piece-form-primary" type="submit">
                    <i class="fas fa-save"></i>
                    <span>Enregistrer</span>
                </button>
            </div>
        </form>
    </div>
</div>

@include('layouts.footer')
