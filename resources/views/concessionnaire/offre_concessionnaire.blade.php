@include('layouts.header')
@include('layouts.menu')

<style>
    .offer-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .offer-hero,
    .offer-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(19, 13, 13, 0.05);
    }

    .offer-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 20px;
        position: relative;
    }

    .offer-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .offer-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .offer-title {
        color: #130d0d;
        font-size: 26px;
        font-weight: 950;
        margin: 4px 0 6px;
    }

    .offer-copy {
        color: #64748b;
        margin: 0;
    }

    .offer-count {
        background: #130d0d;
        border-radius: 999px;
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
        padding: 9px 13px;
        white-space: nowrap;
    }

    .offer-tools {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;
    }

    .offer-search {
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

    .offer-search input {
        border: 0;
        min-height: 42px;
        outline: 0;
        width: 100%;
    }

    .offer-table {
        margin: 0;
    }

    .offer-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .offer-name {
        color: #130d0d;
        display: block;
        font-weight: 950;
    }

    .offer-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 3px;
    }

    .offer-file {
        align-items: center;
        background: #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        min-height: 36px;
        padding: 8px 10px;
    }

    .offer-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: #64748b;
        padding: 28px;
        text-align: center;
    }

    @media (max-width: 767px) {
        .offer-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
        }

        .offer-tools {
            justify-content: stretch;
        }

        .offer-search {
            max-width: none;
        }
    }
</style>

<div class="page-wrapper offer-page">
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

        <div class="offer-hero">
            <div>
                <span class="offer-kicker">Réseau concessionnaire</span>
                <h1 class="offer-title">Offres reçues</h1>
                <p class="offer-copy">Consultez les fichiers transmis par les concessionnaires et leurs informations de contact.</p>
            </div>
            <span class="offer-count">{{ !empty($offres) ? count($offres) : 0 }} offre(s)</span>
        </div>

        <div class="offer-tools">
            <label class="offer-search" for="offerSearch">
                <i class="fas fa-search"></i>
                <input type="search" id="offerSearch" placeholder="Rechercher concessionnaire, contact, email...">
            </label>
        </div>

        @if (!empty($offres) && count($offres) > 0)
            <div class="offer-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle offer-table" id="offerTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Fichier</th>
                                <th>Concessionnaire</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Adresse</th>
                                <th>Date d'envoi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($offres as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <a class="offer-file" href="{{ config('app.concessionnaire_server_url') }}{{ $item->fichier }}" target="_blank">
                                            <i class="fas fa-file-download"></i>
                                            Voir l'offre
                                        </a>
                                    </td>
                                    <td>
                                        <span class="offer-name">{{ $item->concessionnaire->name ?? 'N/A' }}</span>
                                        <span class="offer-muted">ID #{{ $item->concessionnaire_id }}</span>
                                    </td>
                                    <td>{{ $item->concessionnaire->contact ?? 'N/A' }}</td>
                                    <td>{{ $item->concessionnaire->email ?? 'N/A' }}</td>
                                    <td>{{ $item->concessionnaire->adresse ?? 'N/A' }}</td>
                                    <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="offer-empty mt-3" id="offerNoResult" style="display:none;">Aucune offre ne correspond à votre recherche.</div>
        @else
            <div class="offer-empty">Aucune offre enregistrée pour le moment.</div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('offerSearch');
        var rows = document.querySelectorAll('#offerTable tbody tr');
        var noResult = document.getElementById('offerNoResult');

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
