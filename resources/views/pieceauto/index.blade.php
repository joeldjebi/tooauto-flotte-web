@include('layouts.header')
@include('layouts.menu')

<style>
    .piece-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .piece-hero,
    .piece-filter-card,
    .piece-table-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
    }

    .piece-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 18px;
    }

    .piece-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .piece-title {
        color: #130d0d;
        font-size: 24px;
        font-weight: 900;
        margin: 0;
    }

    .piece-copy {
        color: #64748b;
        font-size: 14px;
        margin: 6px 0 0;
        max-width: 720px;
    }

    .piece-action {
        align-items: center;
        background: #efc242;
        border: 0;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
    }

    .piece-filter-card {
        margin-bottom: 18px;
        padding: 14px;
    }

    .piece-filter-card .form-control,
    .piece-filter-card .form-select {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .piece-table-card {
        overflow: hidden;
    }

    .piece-table-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eef2f7;
        padding: 16px 18px;
    }

    .piece-table-card .card-title {
        color: #130d0d;
        font-size: 16px;
        font-weight: 900;
    }

    .piece-table {
        margin: 0;
    }

    .piece-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e5eaf1;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .piece-table tbody td {
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .piece-thumb {
        background: #f1f5f9;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        height: 54px;
        object-fit: cover;
        width: 64px;
    }

    .piece-name {
        color: #130d0d;
        display: block;
        font-size: 14px;
        font-weight: 900;
    }

    .piece-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 3px;
    }

    .piece-pill {
        background: rgba(239, 194, 66, 0.2);
        border-radius: 999px;
        color: #130d0d;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        padding: 6px 10px;
        white-space: nowrap;
    }

    .piece-actions {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .piece-action-btn {
        align-items: center;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        color: #334155;
        display: inline-flex;
        font-size: 12px;
        font-weight: 850;
        gap: 6px;
        min-height: 34px;
        padding: 7px 9px;
    }

    .piece-action-btn:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .piece-action-btn.danger:hover {
        background: #dc2626;
        border-color: #dc2626;
    }

    .piece-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: #64748b;
        padding: 28px;
        text-align: center;
    }

    .piece-modal {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .piece-modal .modal-header {
        background: #130d0d;
        border: 0;
        color: #ffffff;
        padding: 18px 22px;
    }

    .piece-modal .btn-close {
        filter: invert(1);
    }

    .piece-modal .modal-body {
        background: #f8fafc;
        padding: 20px 22px;
    }

    .piece-modal-section {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        padding: 16px;
    }

    .piece-modal .form-control,
    .piece-modal .form-select {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .piece-modal .form-label {
        color: #475569;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .piece-modal .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e5eaf1;
        padding: 14px 22px;
    }

    @media (max-width: 991px) {
        .piece-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
        }
    }
</style>

<div class="page-wrapper piece-page">
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

        <div class="piece-hero">
            <div>
                <span class="piece-kicker">Opérations</span>
                <h4 class="piece-title">Pièces & accessoires</h4>
                <p class="piece-copy">Gérez les pièces disponibles, leurs catégories, marques, modèles et photos stockées sur Wasabi.</p>
            </div>
            <a href="{{ route('add.annonce') }}" class="piece-action">
                <i class="fas fa-plus"></i>
                <span>Nouvelle pièce</span>
            </a>
        </div>

        <div class="piece-filter-card">
            <div class="row g-2">
                <div class="col-lg-3 col-md-6">
                    <select id="filterType" class="form-select">
                        <option value="">Tous les types</option>
                        @foreach($type_de_pieces as $type)
                            <option value="{{ $type->libelle }}">{{ $type->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select id="filterMarque" class="form-select">
                        <option value="">Toutes les marques</option>
                        @foreach($marques as $marque)
                            <option value="{{ $marque->libelle }}">{{ $marque->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <select id="filterCategorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categorie_pieces as $cat)
                            <option value="{{ $cat->libelle }}">{{ $cat->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 col-md-6">
                    <input type="text" id="searchPiece" class="form-control" placeholder="Rechercher une pièce">
                </div>
            </div>
        </div>

        @if (!empty($annonces) && count($annonces) > 0)
            <div class="card piece-table-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des pièces</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle piece-table" id="pieceTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Pièce</th>
                                    <th>Type</th>
                                    <th>Marque</th>
                                    <th>Catégorie</th>
                                    <th>Sous-catégorie</th>
                                    <th>Modèle</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($annonces as $key => $item)
                                    <tr>
                                        <td>{{ $annonces->firstItem() ? $annonces->firstItem() + $key : $key + 1 }}</td>
                                        <td>
                                            @if (!empty($item->image))
                                                <img src="{{ $item->image_url ?? asset('assets/img/default-car.png') }}" alt="Photo pièce" class="piece-thumb" data-bs-toggle="modal" data-bs-target="#view{{ $item->id }}">
                                            @else
                                                <span class="piece-muted">Aucune</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="piece-name">{{ $item->libelle }}</span>
                                            <span class="piece-muted">ID #{{ $item->id }}</span>
                                        </td>
                                        <td>{{ $item->type_de_piece->libelle ?? 'N/A' }}</td>
                                        <td>{{ $item->marque->libelle ?? 'N/A' }}</td>
                                        <td><span class="piece-pill">{{ $item->categorie_piece->libelle ?? 'N/A' }}</span></td>
                                        <td>{{ $item->sous_categorie_piece->libelle ?? 'N/A' }}</td>
                                        <td>{{ $item->modele }}</td>
                                        <td>
                                            <div class="piece-actions">
                                                <button class="piece-action-btn" data-bs-toggle="modal" data-bs-target="#view{{ $item->id }}" title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                    <span>Détails</span>
                                                </button>
                                                <a href="{{ route('edit.annonce', $item->id) }}" class="piece-action-btn" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                    <span>Modifier</span>
                                                </a>
                                                <form action="{{ route('destroy.annonce', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="piece-action-btn danger" title="Supprimer" onclick="return confirm('Supprimer cette pièce ?')">
                                                        <i class="fas fa-trash"></i>
                                                        <span>Supprimer</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('pieceauto.partials.modals', ['item' => $item, 'type_de_pieces' => $type_de_pieces, 'categorie_pieces' => $categorie_pieces, 'sous_categorie_pieces' => $sous_categorie_pieces, 'marques' => $marques])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $annonces->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @else
            <div class="piece-empty">Aucune pièce enregistrée pour le moment.</div>
        @endif
    </div>
</div>

@include('layouts.footer')

<script>
    function normalize(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
    }

    function filterPieces() {
        var search = normalize(document.getElementById('searchPiece').value);
        var type = document.getElementById('filterType').value;
        var marque = document.getElementById('filterMarque').value;
        var categorie = document.getElementById('filterCategorie').value;
        var rows = document.querySelectorAll('#pieceTable tbody tr');

        rows.forEach(function (row) {
            var text = normalize(row.textContent);
            var typeText = row.querySelector('td:nth-child(4)')?.textContent.trim();
            var marqueText = row.querySelector('td:nth-child(5)')?.textContent.trim();
            var categorieText = row.querySelector('td:nth-child(6)')?.textContent.trim();
            var show = true;

            if (search && !text.includes(search)) show = false;
            if (type && typeText !== type) show = false;
            if (marque && marqueText !== marque) show = false;
            if (categorie && categorieText !== categorie) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    ['searchPiece', 'filterType', 'filterMarque', 'filterCategorie'].forEach(function (id) {
        var element = document.getElementById(id);
        if (element) {
            element.addEventListener(id === 'searchPiece' ? 'keyup' : 'change', filterPieces);
        }
    });
</script>
