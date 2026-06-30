@include('layouts.header')
@include('layouts.menu')
<div class="page-wrapper">
    <div class="content">
        @include('layouts.fileariane')
        @if(session()->has("message"))
            <div style="padding: 10px" class="alert {{session()->get('type')}}">{{ session()->get('message') }} </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row mb-3 align-items-center">
            <div class="col-md-6">
                <h4 class="card-title mb-0"><i class="fas fa-box-open me-2"></i>Liste des articles</h4>
            </div>
            <div class="col-md-6 text-end">
                <a href="#" class="btn btn-success btn-lg px-4" data-bs-toggle="modal" data-bs-target="#addPieceAuto">
                    <i class="fas fa-plus me-1"></i> Ajouter un article
                </a>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="text" id="filterNom" class="form-control" placeholder="Filtrer par nom d'article...">
            </div>
            <div class="col-md-3">
                <input type="number" id="filterPrix" class="form-control" placeholder="Filtrer par prix...">
            </div>
            <div class="col-md-3 ms-auto">
                <input type="text" id="searchArticle" class="form-control" placeholder="Recherche générale...">
            </div>
        </div>

        @if (!empty($articles) && count($articles) > 0)
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="articleTable">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>#</th>
                                            <th>Image</th>
                                            <th>Nom de l'article</th>
                                            <th>Prix</th>
                                            <th>Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($articles->isNotEmpty())
                                            @foreach ($articles as $key => $item)
                                            @php
                                                $fullDescription = html_entity_decode($item->description);
                                                $truncatedDescription = \Illuminate\Support\Str::limit($fullDescription, 50, '...');
                                            @endphp
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    @if (!empty($item->image))
                                                        <img src="/images/article/{{ $item->image }}" alt="Photo article" class="rounded border" style="width:40px;height:40px;object-fit:cover;cursor:pointer" data-bs-toggle="modal" data-bs-target="#view{{ $item->id }}">
                                                    @else
                                                        <span class="text-muted">Aucune</span>
                                                    @endif
                                                </td>
                                                <td>{{ $item->libelle }}</td>
                                                <td>{{ $item->amount ?? "" }} F CFA</td>
                                                <td>{{ $truncatedDescription }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#view{{ $item->id }}" title="Détails">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit{{ $item->id }}" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <form action="{{ route('destroy.article', $item->id) }}" method="POST" style="display:inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @include('article.partials.modals', ['item' => $item])
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3">
                                @if($articles->hasPages())
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            @if($articles->onFirstPage())
                                                <li class="page-item disabled"><a class="page-link" href="#">Précédent</a></li>
                                            @else
                                                <li class="page-item"><a class="page-link" href="{{ $articles->previousPageUrl() }}">Précédent</a></li>
                                            @endif
                                            @foreach(range(1, $articles->lastPage()) as $page)
                                                <li class="page-item {{ $articles->currentPage() == $page ? 'active' : '' }}">
                                                    <a class="page-link" href="{{ $articles->url($page) }}">{{ $page }}</a>
                                                </li>
                                            @endforeach
                                            @if($articles->hasMorePages())
                                                <li class="page-item"><a class="page-link" href="{{ $articles->nextPageUrl() }}">Suivant</a></li>
                                            @else
                                                <li class="page-item disabled"><a class="page-link" href="#">Suivant</a></li>
                                            @endif
                                        </ul>
                                    </nav>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <p>Aucun article enregistrer pour le moment !</p>
        @endif

    <div class="modal fade" id="addPieceAuto" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un article</h5>
                    <button type="button" class="close" data-bs-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('store.article') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="libelle" class="form-label">Nom de l'article</label>
                                    <div class="input-group">
                                          <input type="text" class="form-control" name="libelle">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Prix de l'article (F CFA)</label>
                                    <input type="number" class="form-control" name="amount">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Photo de l'article</label>
                                    <input type="file" class="form-control" name="image">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="">Description</label>
                                    <textarea class="form-control" name="description"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer text-center">
                            <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermé</button>
                            <button class="btn btn-submit">Enregistré</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')

{{-- JS pour la recherche rapide et les filtres --}}
<script>
    function normalize(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
    }
    function filterArticles() {
        let search = normalize(document.getElementById('searchArticle').value);
        let nom = normalize(document.getElementById('filterNom').value);
        let prix = document.getElementById('filterPrix').value;
        let rows = document.querySelectorAll('#articleTable tbody tr');
        rows.forEach(row => {
            let text = normalize(row.textContent);
            let nomText = row.querySelector('td:nth-child(3)')?.textContent.trim().toLowerCase();
            let prixText = row.querySelector('td:nth-child(4)')?.textContent.replace(/\D/g, '');
            let show = true;
            if (search && !text.includes(search)) show = false;
            if (nom && (!nomText || !nomText.includes(nom))) show = false;
            if (prix && (!prixText || prixText != prix)) show = false;
            row.style.display = show ? '' : 'none';
        });
    }
    document.getElementById('searchArticle').addEventListener('keyup', filterArticles);
    document.getElementById('filterNom').addEventListener('keyup', filterArticles);
    document.getElementById('filterPrix').addEventListener('keyup', filterArticles);
</script>
