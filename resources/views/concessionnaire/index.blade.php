@include('layouts.header')
@include('layouts.menu')

<style>
    .dealer-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .dealer-hero,
    .dealer-filter-card,
    .dealer-table-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(19, 13, 13, 0.04);
    }

    .dealer-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        padding: 18px;
    }

    .dealer-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 4px;
        text-transform: uppercase;
    }

    .dealer-title {
        color: #130d0d;
        font-size: 24px;
        font-weight: 900;
        letter-spacing: 0;
        margin: 0;
    }

    .dealer-copy {
        color: #64748b;
        font-size: 14px;
        margin: 6px 0 0;
        max-width: 720px;
    }

    .dealer-count {
        align-items: center;
        background: #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-size: 13px;
        font-weight: 900;
        min-height: 42px;
        padding: 10px 14px;
        white-space: nowrap;
    }

    .dealer-filter-card {
        margin-bottom: 18px;
        padding: 14px;
    }

    .dealer-filter-card .form-control {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .dealer-table-card {
        overflow: hidden;
    }

    .dealer-table-card .card-header {
        background: transparent;
        border-bottom: 1px solid #eef2f7;
        padding: 16px 18px;
    }

    .dealer-table-card .card-title {
        color: #130d0d;
        font-size: 16px;
        font-weight: 900;
    }

    .dealer-table {
        margin: 0;
    }

    .dealer-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid #e5eaf1;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .dealer-table tbody td {
        color: #334155;
        font-size: 13px;
        vertical-align: middle;
    }

    .dealer-name {
        color: #130d0d;
        display: block;
        font-size: 14px;
        font-weight: 900;
    }

    .dealer-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 3px;
    }

    .dealer-map-link {
        align-items: center;
        background: rgba(239, 194, 66, 0.2);
        border-radius: 999px;
        color: #130d0d;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        gap: 6px;
        padding: 7px 10px;
        white-space: nowrap;
    }

    .dealer-action-grid {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .dealer-action {
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
        transition: background 0.18s ease, color 0.18s ease, border-color 0.18s ease;
    }

    .dealer-action:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .dealer-action.primary {
        background: #efc242;
        border-color: #efc242;
        color: #130d0d;
    }

    .dealer-action.primary:hover {
        background: #d9ad32;
        border-color: #d9ad32;
        color: #130d0d;
    }

    .dealer-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: #64748b;
        padding: 28px;
        text-align: center;
    }

    .dealer-modal {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .dealer-modal .modal-header {
        background: #130d0d;
        border: 0;
        color: #ffffff;
        padding: 18px 22px;
    }

    .dealer-modal .modal-title {
        font-size: 18px;
        font-weight: 900;
        margin: 0;
    }

    .dealer-modal .btn-close {
        filter: invert(1);
    }

    .dealer-modal-kicker {
        color: #efc242;
        display: block;
        font-size: 11px;
        font-weight: 900;
        margin-bottom: 3px;
        text-transform: uppercase;
    }

    .dealer-modal .modal-body {
        background: #f8fafc;
        padding: 20px 22px;
    }

    .dealer-modal-section {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        padding: 16px;
    }

    .dealer-modal .form-label {
        color: #475569;
        font-size: 12px;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .dealer-modal .form-control {
        border-color: #dbe3ee;
        border-radius: 8px;
        min-height: 42px;
    }

    .dealer-modal .modal-footer {
        background: #ffffff;
        border-top: 1px solid #e5eaf1;
        padding: 14px 22px;
    }

    .dealer-modal .btn-submit {
        background: #efc242;
        border: 0;
        border-radius: 8px;
        color: #130d0d;
        font-weight: 900;
        min-height: 40px;
        padding: 9px 14px;
    }

    .dealer-modal .btn-cancel {
        background: #f1f5f9;
        border: 0;
        border-radius: 8px;
        color: #334155;
        font-weight: 850;
        min-height: 40px;
        padding: 9px 14px;
    }

    @media (max-width: 991px) {
        .dealer-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
        }
    }
</style>

<div class="page-wrapper dealer-page">
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

        <div class="dealer-hero">
            <div>
                <span class="dealer-kicker">Réseau</span>
                <h4 class="dealer-title">Concessionnaires</h4>
                <p class="dealer-copy">Consultez les concessionnaires actifs, leurs véhicules disponibles, vos rendez-vous et vos demandes spécifiques.</p>
            </div>
            <span class="dealer-count">{{ $concessionnaires->total() ?? count($concessionnaires) }} concessionnaire(s)</span>
        </div>

        <div class="dealer-filter-card">
            <div class="row g-2">
                <div class="col-lg-3 col-md-6">
                    <input type="text" id="filterNom" class="form-control" placeholder="Nom">
                </div>
                <div class="col-lg-3 col-md-6">
                    <input type="text" id="filterAdresse" class="form-control" placeholder="Adresse">
                </div>
                <div class="col-lg-3 col-md-6">
                    <input type="text" id="filterContact" class="form-control" placeholder="Contact">
                </div>
                <div class="col-lg-3 col-md-6">
                    <input type="text" id="filterEmail" class="form-control" placeholder="Email">
                </div>
            </div>
        </div>

        @if (!empty($concessionnaires) && count($concessionnaires) > 0)
            <div class="card dealer-table-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des concessionnaires</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dealer-table" id="concessionnaireTable">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Concessionnaire</th>
                                    <th>Adresse</th>
                                    <th>Contact</th>
                                    <th>E-mail</th>
                                    <th>Localisation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($concessionnaires as $key => $item)
                                    <tr>
                                        <td>{{ $concessionnaires->firstItem() ? $concessionnaires->firstItem() + $key : $key + 1 }}</td>
                                        <td>
                                            <span class="dealer-name">{{ $item->name }}</span>
                                            <span class="dealer-muted">ID #{{ $item->id }}</span>
                                        </td>
                                        <td>{{ $item->adresse ?? 'N/A' }}</td>
                                        <td>{{ $item->contact ?? 'N/A' }}</td>
                                        <td>{{ $item->email ?? 'N/A' }}</td>
                                        <td>
                                            @if(!empty($item->adresse_map))
                                                <a class="dealer-map-link" href="{{ $item->adresse_map }}" target="_blank" rel="noopener">
                                                    <i class="fas fa-map-marker-alt"></i>
                                                    <span>Voir la carte</span>
                                                </a>
                                            @else
                                                <span class="dealer-muted">Non renseignée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dealer-action-grid">
                                                <a class="dealer-action primary" href="{{ route('index.concessionnaire-vehicule', ['id' => $item->id ]) }}" title="Voir les véhicules">
                                                    <i class="fas fa-car"></i>
                                                    <span>Véhicules</span>
                                                </a>
                                                <button class="dealer-action" type="button" data-bs-toggle="modal" data-bs-target="#addRDV{{ $item->id }}" title="Prendre rendez-vous">
                                                    <i class="fas fa-calendar-alt"></i>
                                                    <span>RDV</span>
                                                </button>
                                                <a class="dealer-action" href="{{ route('rdv.concessionnaire', ['id' => $item->id ]) }}" title="Historique des rendez-vous">
                                                    <i class="fas fa-history"></i>
                                                    <span>Historique</span>
                                                </a>
                                                <button class="dealer-action" type="button" data-bs-toggle="modal" data-bs-target="#addDemande{{ $item->id }}" title="Créer une demande spécifique">
                                                    <i class="fas fa-tools"></i>
                                                    <span>Demande</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @include('concessionnaire.partials.modals', ['item' => $item])
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $concessionnaires->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        @else
            <div class="dealer-empty">Aucun concessionnaire enregistré pour le moment.</div>
        @endif
    </div>
</div>

@include('layouts.footer')

<script>
    function normalize(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
    }

    function filterConcessionnaires() {
        var nom = normalize(document.getElementById('filterNom').value);
        var adresse = normalize(document.getElementById('filterAdresse').value);
        var contact = normalize(document.getElementById('filterContact').value);
        var email = normalize(document.getElementById('filterEmail').value);
        var rows = document.querySelectorAll('#concessionnaireTable tbody tr');

        rows.forEach(function (row) {
            var nomText = normalize(row.querySelector('td:nth-child(2)')?.textContent);
            var adresseText = normalize(row.querySelector('td:nth-child(3)')?.textContent);
            var contactText = normalize(row.querySelector('td:nth-child(4)')?.textContent);
            var emailText = normalize(row.querySelector('td:nth-child(5)')?.textContent);
            var show = true;

            if (nom && !nomText.includes(nom)) show = false;
            if (adresse && !adresseText.includes(adresse)) show = false;
            if (contact && !contactText.includes(contact)) show = false;
            if (email && !emailText.includes(email)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    ['filterNom', 'filterAdresse', 'filterContact', 'filterEmail'].forEach(function (id) {
        var input = document.getElementById(id);
        if (input) {
            input.addEventListener('keyup', filterConcessionnaires);
        }
    });
</script>
