@include('layouts.header')
@include('layouts.menu')

<style>
    .function-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .function-hero,
    .function-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(19, 13, 13, 0.05);
    }

    .function-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 20px;
        position: relative;
    }

    .function-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .function-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .function-title {
        color: #130d0d;
        font-size: 26px;
        font-weight: 950;
        margin: 4px 0 6px;
    }

    .function-copy {
        color: #64748b;
        margin: 0;
    }

    .function-btn {
        align-items: center;
        background: #efc242;
        border: 1px solid #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        min-height: 42px;
        padding: 10px 14px;
    }

    .function-btn.secondary {
        background: #ffffff;
        border-color: #dbe3ee;
        color: #334155;
    }

    .function-tools {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;
    }

    .function-search {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        display: flex;
        gap: 10px;
        max-width: 430px;
        padding: 0 12px;
        width: 100%;
    }

    .function-search input {
        border: 0;
        min-height: 42px;
        outline: 0;
        width: 100%;
    }

    .function-table {
        margin: 0;
    }

    .function-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .function-name {
        color: #130d0d;
        display: block;
        font-weight: 950;
    }

    .function-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 2px;
    }

    .function-actions {
        display: inline-flex;
        gap: 6px;
    }

    .function-icon {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        color: #334155;
        display: inline-flex;
        height: 34px;
        justify-content: center;
        width: 34px;
    }

    .function-icon:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .function-icon.danger:hover {
        background: #dc2626;
        border-color: #dc2626;
    }

    .function-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: #64748b;
        padding: 28px;
        text-align: center;
    }

    @media (max-width: 767px) {
        .function-hero {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="page-wrapper function-page">
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

        <div class="function-hero">
            <div>
                <span class="function-kicker">Organisation</span>
                <h1 class="function-title">Fonctions</h1>
                <p class="function-copy">Gérez les titres métier des utilisateurs. Les permissions sont gérées séparément dans les rôles.</p>
            </div>
            <button class="function-btn" type="button" data-bs-toggle="modal" data-bs-target="#addFonction">
                <i class="fas fa-plus"></i>
                Ajouter une fonction
            </button>
        </div>

        <div class="function-tools">
            <label class="function-search" for="searchFonction">
                <i class="fas fa-search"></i>
                <input type="search" id="searchFonction" placeholder="Rechercher une fonction...">
            </label>
        </div>

        @if (!empty($fonctions) && count($fonctions) > 0)
            <div class="function-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle function-table" id="fonctionTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Libellé</th>
                                <th>Créée le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fonctions as $key => $item)
                                <tr>
                                    <td>{{ $fonctions->firstItem() + $key }}</td>
                                    <td>
                                        <span class="function-name">{{ $item->libelle }}</span>
                                        <span class="function-muted">Fonction utilisateur</span>
                                    </td>
                                    <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                    <td>
                                        <div class="function-actions">
                                            <button class="function-icon" type="button" data-bs-toggle="modal" data-bs-target="#edit{{ $item->id }}" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('fonction.destroy', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="function-icon danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette fonction ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background:#130d0d;color:#fff;">
                                                <h5 class="modal-title">Modifier une fonction</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                                            </div>
                                            <form action="{{ route('fonction.update', ['id' => $item->id]) }}" method="post">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Libellé</label>
                                                        <input type="text" class="form-control" name="libelle" value="{{ $item->libelle }}" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="function-btn secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <button class="function-btn" type="submit">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    {{ $fonctions->links('pagination::bootstrap-4') }}
                </div>
            </div>
            <div class="function-empty mt-3" id="functionNoResult" style="display:none;">Aucune fonction ne correspond à votre recherche.</div>
        @else
            <div class="function-empty">Aucune fonction enregistrée pour le moment.</div>
        @endif
    </div>
</div>

<div class="modal fade" id="addFonction" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#130d0d;color:#fff;">
                <h5 class="modal-title">Ajouter une fonction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
            </div>
            <form action="{{ route('fonction.store') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Libellé</label>
                        <input type="text" class="form-control" name="libelle" value="{{ old('libelle') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="function-btn secondary" data-bs-dismiss="modal">Fermer</button>
                    <button class="function-btn" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('searchFonction');
        var rows = document.querySelectorAll('#fonctionTable tbody tr');
        var noResult = document.getElementById('functionNoResult');

        if (!input || !rows.length) {
            return;
        }

        input.addEventListener('input', function () {
            var term = this.value.trim().toLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var match = row.textContent.toLowerCase().indexOf(term) !== -1;
                row.style.display = match ? '' : 'none';
                visible += match ? 1 : 0;
            });

            if (noResult) {
                noResult.style.display = visible ? 'none' : 'block';
            }
        });
    });
</script>

@include('layouts.footer')
