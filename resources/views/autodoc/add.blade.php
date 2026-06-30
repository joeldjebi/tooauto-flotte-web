@include('layouts.header')
@include('layouts.menu')

<style>
    .autodoc-create-page {
        --doc-ink: #111827;
        --doc-muted: #64748b;
        --doc-line: #e8edf4;
        --doc-shadow: 0 18px 48px rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #fbfcfe 0%, #f7f9fc 48%, #ffffff 100%);
        border-radius: 8px;
        margin: -6px -4px 0;
        padding: 8px 4px 24px;
    }

    .autodoc-create-hero,
    .autodoc-create-card {
        background: rgba(255, 255, 255, 0.96);
        border: 1px solid var(--doc-line);
        border-radius: 8px;
        box-shadow: var(--doc-shadow);
    }

    .autodoc-create-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 20px;
        padding: 24px;
    }

    .autodoc-create-title {
        color: var(--doc-ink);
        font-size: 28px;
        font-weight: 800;
        margin: 0;
    }

    .autodoc-create-subtitle {
        color: var(--doc-muted);
        font-size: 14px;
        margin: 10px 0 0;
    }

    .autodoc-create-card {
        padding: 24px;
    }

    .autodoc-create-page label {
        color: #334155;
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 7px;
    }

    .autodoc-create-page .form-control {
        background: #fbfdff;
        border: 1px solid #dfe7f1;
        border-radius: 8px;
        min-height: 44px;
    }

    .autodoc-create-upload {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 16px;
    }

    .autodoc-create-btn {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        font-weight: 800;
        gap: 8px;
        min-height: 44px;
        padding: 10px 16px;
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="autodoc-create-page">
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

            <div class="autodoc-create-hero">
                <div>
                    <div class="text-primary fw-bold text-uppercase small mb-2">Documents auto</div>
                    <h1 class="autodoc-create-title">Ajouter un document</h1>
                    <p class="autodoc-create-subtitle">Associez un document à un véhicule. Les fichiers seront stockés sur Wasabi.</p>
                </div>
                <a href="{{ route('autodoc.index') }}" class="btn btn-light autodoc-create-btn">
                    <i data-feather="arrow-left"></i>
                    Retour
                </a>
            </div>

            <div class="autodoc-create-card">
                <form action="{{ route('autodoc.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="vehicule_id">Véhicule</label>
                                <select class="form-control selectpicker" data-live-search="true" name="vehicule_id" id="vehicule_id" required>
                                    <option value="">Sélectionnez un véhicule</option>
                                    @foreach ($vehicules as $item)
                                        <option value="{{ $item->id }}">{{ $item->matricule }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="type_docauto_id">Type de document</label>
                                <select class="form-control selectpicker" data-live-search="true" name="type_docauto_id" id="type_docauto_id" required>
                                    <option value="">Sélectionnez un type</option>
                                    @foreach ($type_docautos as $item)
                                        <option value="{{ $item->id }}">{{ $item->libelle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="images">Fichiers</label>
                                <div class="autodoc-create-upload">
                                    <input type="file" class="form-control" name="images[]" id="images" multiple accept="image/*,.pdf,.heic,.heif" required>
                                    <div class="text-muted small mt-2">JPG, PNG, PDF, HEIC/HEIF. Maximum 4 fichiers, 5 Mo chacun.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('autodoc.index') }}" class="btn btn-light autodoc-create-btn">Annuler</a>
                        <button class="btn btn-primary autodoc-create-btn" type="submit">
                            <i data-feather="save"></i>
                            Enregistrer
                        </button>
                    </div>
                </form>
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
    });
</script>
