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
                <h4 class="piece-form-title">Modifier la pièce</h4>
                <p class="piece-form-copy">Mettez à jour les informations de la pièce. Si vous changez l'image, elle sera envoyée sur Wasabi.</p>
            </div>
            <button type="button" class="btn piece-form-secondary" onclick="history.back()">
                <i class="fas fa-arrow-left"></i>
                <span>Retour</span>
            </button>
        </div>

        <form action="{{ route('update.annonce', ['id' => $item->id]) }}" method="post" enctype="multipart/form-data" class="piece-form-card">
            @csrf
            <div class="piece-form-section">
                <span class="piece-form-section-title">Informations</span>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nom de la pièce *</label>
                        <input type="text" class="form-control" name="libelle" value="{{ old('libelle', $item->libelle) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type de pièce *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="type_de_piece_id" required>
                            @foreach ($type_de_pieces as $type_de_piece)
                                <option value="{{ $type_de_piece->id }}" @selected(old('type_de_piece_id', $item->type_de_piece_id) == $type_de_piece->id)>{{ $type_de_piece->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catégorie *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="categorie_piece_id" required>
                            @foreach ($categorie_pieces as $categorie_piece)
                                <option value="{{ $categorie_piece->id }}" @selected(old('categorie_piece_id', $item->categorie_piece_id) == $categorie_piece->id)>{{ $categorie_piece->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Sous-catégorie *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="sous_categorie_piece_id" required>
                            @foreach ($sous_categorie_pieces as $sous_categorie_piece)
                                <option value="{{ $sous_categorie_piece->id }}" @selected(old('sous_categorie_piece_id', $item->sous_categorie_piece_id) == $sous_categorie_piece->id)>{{ $sous_categorie_piece->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Marque *</label>
                        <select class="form-control selectpicker" data-live-search="true" name="marque_id" required>
                            @foreach ($marques as $marque)
                                <option value="{{ $marque->id }}" @selected(old('marque_id', $item->marque_id) == $marque->id)>{{ $marque->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Modèle *</label>
                        <input type="text" class="form-control" name="modele" value="{{ old('modele', $item->modele) }}" required>
                    </div>
                </div>
            </div>

            <div class="piece-form-section">
                <span class="piece-form-section-title">Photo et description</span>
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label">Photo actuelle</label>
                        <div class="piece-upload-box">
                            @if (!empty($item->image))
                                <img src="{{ $item->image_url ?? asset('assets/img/default-car.png') }}" alt="Photo pièce" class="piece-current-image">
                            @else
                                <span class="text-muted">Aucune photo.</span>
                            @endif
                            <input type="file" class="form-control mt-3" name="image" accept="image/*">
                            <small>La nouvelle image remplacera l'ancienne sur Wasabi.</small>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control" rows="8" required>{!! old('description', html_entity_decode($item->description)) !!}</textarea>
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
