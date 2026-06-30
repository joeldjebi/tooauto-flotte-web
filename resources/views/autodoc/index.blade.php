@php
    $docFileUrl = function ($file, $autodoc = null) {
        if (!empty($autodoc?->file_url_map) && !empty($autodoc->file_url_map[$file])) {
            return $autodoc->file_url_map[$file];
        }

        if (empty($file)) {
            return '#';
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        if (file_exists(public_path($file))) {
            return asset($file);
        }

        $wasabiUrl = rtrim((string) config('wasabi.url'), '/');

        return $wasabiUrl !== '' ? $wasabiUrl . '/' . ltrim($file, '/') : '#';
    };

    $docFileMeta = function ($file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        return [
            'extension' => $extension ?: 'file',
            'is_pdf' => $extension === 'pdf',
            'is_image' => in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic', 'heif']),
        ];
    };
@endphp

@include('layouts.header')
@include('layouts.menu')

<style>
    .autodoc-page {
        --doc-ink: #111827;
        --doc-muted: #64748b;
        --doc-line: #e8edf4;
        --doc-shadow: 0 18px 48px rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #fbfcfe 0%, #f7f9fc 48%, #ffffff 100%);
        border-radius: 8px;
        margin: -6px -4px 0;
        padding: 8px 4px 24px;
    }

    .autodoc-hero,
    .autodoc-filter-panel,
    .autodoc-table-panel {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--doc-line);
        border-radius: 8px;
        box-shadow: var(--doc-shadow);
    }

    .autodoc-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding: 24px;
    }

    .autodoc-eyebrow {
        color: #2563eb;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .autodoc-title {
        color: var(--doc-ink);
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
    }

    .autodoc-subtitle {
        color: var(--doc-muted);
        font-size: 14px;
        margin: 10px 0 0;
    }

    .autodoc-btn-primary,
    .autodoc-btn-secondary {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
    }

    .autodoc-btn-primary {
        background: #2563eb;
        border: 1px solid #2563eb;
        color: #ffffff;
    }

    .autodoc-btn-secondary {
        background: #ffffff;
        border: 1px solid #dfe7f1;
        color: #334155;
    }

    .autodoc-filter-panel {
        margin-bottom: 20px;
        padding: 18px;
    }

    .autodoc-filter-title {
        align-items: center;
        color: var(--doc-ink);
        display: flex;
        font-size: 16px;
        font-weight: 800;
        gap: 8px;
        margin-bottom: 16px;
    }

    .autodoc-page label {
        color: #334155;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .autodoc-page .form-control,
    .autodoc-page .form-select {
        background: #fbfdff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        min-height: 42px;
    }

    .autodoc-table-panel {
        overflow: hidden;
    }

    .autodoc-table-head {
        align-items: center;
        border-bottom: 1px solid var(--doc-line);
        display: flex;
        justify-content: space-between;
        gap: 12px;
        padding: 18px 20px;
    }

    .autodoc-table-title {
        color: var(--doc-ink);
        font-size: 17px;
        font-weight: 800;
        margin: 0;
    }

    .autodoc-count-pill,
    .autodoc-soft-badge {
        border-radius: 999px;
        display: inline-flex;
        font-size: 12px;
        font-weight: 800;
        padding: 7px 10px;
        white-space: nowrap;
    }

    .autodoc-count-pill {
        background: #eff6ff;
        color: #2563eb;
    }

    .autodoc-soft-badge {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        color: #475569;
    }

    .autodoc-table {
        margin: 0;
    }

    .autodoc-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--doc-line);
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        padding: 13px 14px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .autodoc-table tbody td {
        border-color: #eef2f7;
        color: #334155;
        font-size: 13px;
        padding: 14px;
        vertical-align: middle;
    }

    .autodoc-file-stack {
        align-items: center;
        display: flex;
    }

    .autodoc-file-thumb {
        align-items: center;
        background: #f1f5f9;
        border: 2px solid #ffffff;
        border-radius: 8px;
        box-shadow: 0 7px 18px rgba(15, 23, 42, 0.12);
        color: #475569;
        display: inline-flex;
        height: 44px;
        justify-content: center;
        margin-right: -10px;
        overflow: hidden;
        width: 44px;
    }

    .autodoc-file-thumb img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }

    .autodoc-file-thumb.pdf {
        background: #fff1f2;
        color: #be123c;
    }

    .autodoc-actions {
        align-items: center;
        display: flex;
        gap: 6px;
    }

    .autodoc-icon-btn {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        color: #475569;
        display: inline-flex;
        height: 36px;
        justify-content: center;
        width: 36px;
    }

    .autodoc-icon-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #2563eb;
    }

    .autodoc-icon-btn.danger:hover {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #be123c;
    }

    .autodoc-upload {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 14px;
    }

    .autodoc-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: var(--doc-muted);
        padding: 30px;
        text-align: center;
    }

    .autodoc-modal .modal-content {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .autodoc-modal .modal-header {
        background: #f8fafc;
        border-bottom: 1px solid #e8edf4;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="autodoc-page">
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

            <div class="autodoc-hero">
                <div>
                    <div class="autodoc-eyebrow">Documents auto</div>
                    <h1 class="autodoc-title">Documents des véhicules</h1>
                    <p class="autodoc-subtitle">Centralisez les cartes, attestations et pièces de vos véhicules. Les fichiers sont stockés dans Wasabi.</p>
                </div>
                <button type="button" class="btn autodoc-btn-primary" data-bs-toggle="modal" data-bs-target="#addAutodoc">
                    <i data-feather="file-plus"></i>
                    Nouveau document
                </button>
            </div>

            <div class="autodoc-filter-panel">
                <div class="autodoc-filter-title">
                    <i data-feather="sliders"></i>
                    Filtres rapides
                </div>
                <div class="row">
                    <div class="col-md-4 col-12">
                        <label for="filterType">Type de document</label>
                        <select id="filterType" class="form-select">
                            <option value="">Tous les types de document</option>
                            @foreach($type_docautos as $type)
                                <option value="{{ $type->libelle }}">{{ $type->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="filterVehicule">Véhicule</label>
                        <select id="filterVehicule" class="form-select">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicules as $vehicule)
                                <option value="{{ $vehicule->matricule }}">{{ $vehicule->matricule }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 col-12">
                        <label for="searchAutodoc">Recherche</label>
                        <input type="text" id="searchAutodoc" class="form-control" placeholder="Matricule, type...">
                    </div>
                </div>
            </div>

            @if (!empty($autodocs) && count($autodocs) > 0)
                <div class="autodoc-table-panel">
                    <div class="autodoc-table-head">
                        <h3 class="autodoc-table-title">Documents enregistrés</h3>
                        <span class="autodoc-count-pill">{{ $autodocs->total() }} document(s)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table autodoc-table" id="autodocTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Fichiers</th>
                                    <th>Véhicule</th>
                                    <th>Type de document</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($autodocs as $key => $item)
                                    @php $imagePaths = json_decode($item->images, true) ?: []; @endphp
                                    <tr>
                                        <td>{{ $autodocs->firstItem() + $key }}</td>
                                        <td>
                                            @if(count($imagePaths) > 0)
                                                <div class="autodoc-file-stack">
                                                    @foreach(array_slice($imagePaths, 0, 3) as $photo)
                                                        @php
                                                            $meta = $docFileMeta($photo);
                                                            $url = $docFileUrl($photo, $item);
                                                        @endphp
                                                        <a href="{{ $url }}" target="_blank" class="autodoc-file-thumb {{ $meta['is_pdf'] ? 'pdf' : '' }}" title="Ouvrir {{ strtoupper($meta['extension']) }}">
                                                            @if($meta['is_image'])
                                                                <img src="{{ $url }}" alt="Document" onerror="this.style.display='none';this.parentElement.innerHTML='<i data-feather=&quot;file&quot;></i>';">
                                                            @elseif($meta['is_pdf'])
                                                                <i data-feather="file-text"></i>
                                                            @else
                                                                <i data-feather="file"></i>
                                                            @endif
                                                        </a>
                                                    @endforeach
                                                    @if(count($imagePaths) > 3)
                                                        <span class="autodoc-file-thumb">+{{ count($imagePaths) - 3 }}</span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted">Aucun</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $item->vehicule->matricule ?? '-' }}</strong></td>
                                        <td><span class="autodoc-soft-badge">{{ $item->type_docauto->libelle ?? '-' }}</span></td>
                                        <td>
                                            <div class="autodoc-actions">
                                                <button type="button" class="autodoc-icon-btn" data-bs-toggle="modal" data-bs-target="#show{{ $item->id }}" title="Détails">
                                                    <i data-feather="eye"></i>
                                                </button>
                                                <button type="button" class="autodoc-icon-btn" data-bs-toggle="modal" data-bs-target="#edit{{ $item->id }}" title="Modifier">
                                                    <i data-feather="edit-2"></i>
                                                </button>
                                                <form action="{{ route('autodoc.destroy', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="autodoc-icon-btn danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                                        <i data-feather="trash-2"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('autodoc.partials.modals', ['item' => $item, 'type_docautos' => $type_docautos, 'vehicules' => $vehicules, 'docFileUrl' => $docFileUrl, 'docFileMeta' => $docFileMeta])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center p-3">
                        {{ $autodocs->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="autodoc-empty">Aucun document auto enregistré pour le moment.</div>
            @endif
        </div>
    </div>

    <div class="modal fade autodoc-modal" id="addAutodoc" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title">Ajouter un document</h5>
                        <div class="text-muted small">PDF et images, maximum 4 fichiers. Stockage Wasabi.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="{{ route('autodoc.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="vehicule_id">Véhicule</label>
                                    <select class="form-control" name="vehicule_id" id="vehicule_id" required>
                                        <option value="">Sélectionnez un véhicule</option>
                                        @foreach ($vehicules as $vehicule)
                                            <option value="{{ $vehicule->id }}">{{ $vehicule->matricule }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label for="type_docauto_id">Type de document</label>
                                    <select class="form-control" name="type_docauto_id" id="type_docauto_id" required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach ($type_docautos as $type)
                                            <option value="{{ $type->id }}">{{ $type->libelle }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="images">Fichiers</label>
                                    <div class="autodoc-upload">
                                        <input type="file" class="form-control" name="images[]" id="images" multiple accept="image/*,.pdf,.heic,.heif" required>
                                        <div class="text-muted small mt-2">Formats acceptés: JPG, PNG, PDF, HEIC/HEIF. 5 Mo max par fichier.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn autodoc-btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button class="btn autodoc-btn-primary" type="submit">
                                <i data-feather="save"></i>
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather) {
            feather.replace();
        }

        function normalize(str) {
            return (str || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
        }

        function filterAutodocs() {
            var search = normalize(document.getElementById('searchAutodoc').value);
            var type = document.getElementById('filterType').value;
            var vehicule = document.getElementById('filterVehicule').value;

            document.querySelectorAll('#autodocTable tbody tr').forEach(function (row) {
                var text = normalize(row.textContent);
                var typeText = row.querySelector('td:nth-child(4)')?.textContent.trim();
                var vehiculeText = row.querySelector('td:nth-child(3)')?.textContent.trim();
                var show = true;

                if (search && !text.includes(search)) show = false;
                if (type && typeText !== type) show = false;
                if (vehicule && vehiculeText !== vehicule) show = false;

                row.style.display = show ? '' : 'none';
            });
        }

        ['filterType', 'filterVehicule'].forEach(function (id) {
            var element = document.getElementById(id);
            if (element) {
                element.addEventListener('change', filterAutodocs);
            }
        });

        var search = document.getElementById('searchAutodoc');
        if (search) {
            search.addEventListener('keyup', filterAutodocs);
        }
    });
</script>
