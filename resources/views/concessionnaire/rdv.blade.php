@include('layouts.header')
@include('layouts.menu')
@include('concessionnaire.partials.page-styles')

<div class="page-wrapper dealer-detail-page">
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

        @php
            $rdvCollection = collect($rdv_concessionnaires->items() ?? []);
            $statusLabels = [
                0 => ['label' => 'En attente', 'class' => 'waiting'],
                1 => ['label' => 'Accepté', 'class' => 'success'],
                2 => ['label' => 'Annulé', 'class' => 'danger'],
                3 => ['label' => 'Indisponible', 'class' => 'info'],
            ];
            $totalRdv = method_exists($rdv_concessionnaires, 'total') ? $rdv_concessionnaires->total() : $rdvCollection->count();
            $waitingRdv = $rdvCollection->where('statut', 0)->count();
            $acceptedRdv = $rdvCollection->where('statut', 1)->count();
            $closedRdv = $rdvCollection->whereIn('statut', [2, 3])->count();
        @endphp

        <div class="dealer-detail-hero">
            <div>
                <span class="dealer-detail-kicker">Concessionnaire</span>
                <h4 class="dealer-detail-title">Historique des rendez-vous</h4>
                <p class="dealer-detail-copy">{{ $concessionnaire->name ?? 'Concessionnaire' }} - suivez les demandes de rendez-vous envoyées.</p>
            </div>
            <div class="dealer-detail-actions">
                <a href="{{ route('index.concessionnaire') }}" class="dealer-detail-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                @if(!empty($concessionnaire))
                    <a href="{{ route('index.concessionnaire-vehicule', ['id' => $concessionnaire->id]) }}" class="dealer-detail-btn primary">
                        <i class="fas fa-car"></i>
                        <span>Véhicules</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="dealer-detail-stats">
            <div class="dealer-detail-stat">
                <span>Total RDV</span>
                <strong>{{ $totalRdv }}</strong>
            </div>
            <div class="dealer-detail-stat">
                <span>En attente</span>
                <strong>{{ $waitingRdv }}</strong>
            </div>
            <div class="dealer-detail-stat">
                <span>Acceptés</span>
                <strong>{{ $acceptedRdv }}</strong>
            </div>
            <div class="dealer-detail-stat">
                <span>Clôturés</span>
                <strong>{{ $closedRdv }}</strong>
            </div>
        </div>

        <div class="dealer-detail-tools">
            <label class="dealer-detail-search" for="dealerRdvSearch">
                <i class="fas fa-search"></i>
                <input type="search" id="dealerRdvSearch" placeholder="Rechercher par jour, heure, statut, contact...">
            </label>
            <span class="dealer-detail-chip">{{ $totalRdv }} rendez-vous</span>
        </div>

        @if (!empty($rdv_concessionnaires) && count($rdv_concessionnaires) > 0)
            <div class="card dealer-detail-table-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des rendez-vous</h5>
                    <p class="dealer-detail-mini mb-0">Suivi des demandes envoyées</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dealer-detail-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Jour et heure</th>
                                    <th>Statut</th>
                                    <th>Contact</th>
                                    <th>Demande créée le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rdv_concessionnaires as $key => $item)
                                    @php
                                        $status = $statusLabels[$item->statut] ?? ['label' => 'Inconnu', 'class' => 'info'];
                                        $searchText = strtolower(($item->jour ?? '') . ' ' . ($item->heure ?? '') . ' ' . $status['label'] . ' ' . ($item->gestionnaire_de_flotte->mobile ?? '') . ' ' . ($item->created_at ?? ''));
                                    @endphp
                                    <tr class="dealer-rdv-row" data-search="{{ $searchText }}">
                                        <td>{{ $rdv_concessionnaires->firstItem() ? $rdv_concessionnaires->firstItem() + $key : $key + 1 }}</td>
                                        <td>
                                            <span class="dealer-detail-name">{{ $item->jour }}</span>
                                            <span class="dealer-detail-muted">{{ $item->heure }}</span>
                                        </td>
                                        <td><span class="dealer-detail-pill {{ $status['class'] }}">{{ $status['label'] }}</span></td>
                                        <td>{{ $item->gestionnaire_de_flotte->mobile ?? 'N/A' }}</td>
                                        <td>{{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>
                                            <div class="dealer-detail-row-actions">
                                                @if ($item->statut == 1 && $item->reponse_concessionnaire != null)
                                                    <button class="dealer-detail-action primary" data-bs-toggle="modal" data-bs-target="#view{{ $item->id }}">
                                                        <i class="fas fa-envelope-open-text"></i>
                                                        <span>Message</span>
                                                    </button>
                                                @else
                                                    <button class="dealer-detail-action" type="button" disabled>
                                                        <i class="fas fa-envelope"></i>
                                                        <span>Aucun message</span>
                                                    </button>
                                                @endif
                                                <a class="dealer-detail-action danger" href="{{ route('destroy.concessionnaire-rdv', ['id' => $item->id]) }}" onclick="return confirm('Supprimer ce rendez-vous ?')">
                                                    <i class="fas fa-trash"></i>
                                                    <span>Supprimer</span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="view{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content dealer-detail-modal">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Réponse du concessionnaire</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="dealer-detail-modal-section">
                                                        <div class="dealer-detail-grid mb-3">
                                                            <div class="dealer-detail-field">
                                                                <span>Date souhaitée</span>
                                                                <strong>{{ $item->jour ?? 'N/A' }}</strong>
                                                            </div>
                                                            <div class="dealer-detail-field">
                                                                <span>Heure</span>
                                                                <strong>{{ $item->heure ?? 'N/A' }}</strong>
                                                            </div>
                                                            <div class="dealer-detail-field">
                                                                <span>Statut</span>
                                                                <strong>{{ $status['label'] }}</strong>
                                                            </div>
                                                            <div class="dealer-detail-field">
                                                                <span>Contact</span>
                                                                <strong>{{ $item->gestionnaire_de_flotte->mobile ?? 'N/A' }}</strong>
                                                            </div>
                                                        </div>
                                                        <h6 style="color:#130d0d;font-weight:900;">Message</h6>
                                                        {!! html_entity_decode($item->reponse_concessionnaire) !!}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $rdv_concessionnaires->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
            <div class="dealer-detail-empty dealer-detail-no-result" id="dealerRdvNoResult">Aucun rendez-vous ne correspond à votre recherche.</div>
        @else
            <div class="dealer-detail-empty">Aucun rendez-vous enregistré pour ce concessionnaire.</div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('dealerRdvSearch');
        var rows = document.querySelectorAll('.dealer-rdv-row');
        var noResult = document.getElementById('dealerRdvNoResult');

        if (!searchInput || !rows.length) {
            return;
        }

        searchInput.addEventListener('input', function () {
            var term = this.value.trim().toLowerCase();
            var visible = 0;

            rows.forEach(function (row) {
                var match = row.dataset.search.indexOf(term) !== -1;
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
