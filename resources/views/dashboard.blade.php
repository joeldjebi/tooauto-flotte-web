@include('layouts.header')
@include('layouts.menu')

@php
    $dashboardAlertGroups = [
        [
            'key' => 'assurance',
            'title' => 'Assurances',
            'icon' => 'shield',
            'accent' => 'blue',
            'collection' => $alertes_assurance ?? collect(),
            'modal' => 'Assurance',
            'show_mileage' => false,
        ],
        [
            'key' => 'vidange',
            'title' => 'Vidanges',
            'icon' => 'droplet',
            'accent' => 'amber',
            'collection' => $alertes_vidange ?? collect(),
            'modal' => 'Vidange',
            'show_mileage' => true,
        ],
        [
            'key' => 'visite',
            'title' => 'Visites Techniques',
            'icon' => 'tool',
            'accent' => 'cyan',
            'collection' => $alertes_visite ?? collect(),
            'modal' => 'Visite',
            'show_mileage' => false,
        ],
        [
            'key' => 'controle',
            'title' => 'Contrôles Techniques',
            'icon' => 'check-circle',
            'accent' => 'emerald',
            'collection' => $alertes_controle ?? collect(),
            'modal' => 'Controle',
            'show_mileage' => false,
        ],
    ];

    $totalAlertes = collect($dashboardAlertGroups)->sum(fn ($group) => $alertes_count[$group['key']] ?? 0);
    $totalCritiques = collect($dashboardAlertGroups)->sum(fn ($group) => $alertes_critique[$group['key']] ?? 0);
    $dashboardStatusCounts = [
        'expired' => 0,
        'soon' => 0,
        'valid' => 0,
    ];

    foreach ($dashboardAlertGroups as $group) {
        foreach ($group['collection'] as $alerte) {
            if (empty($alerte->date_fin)) {
                continue;
            }

            $daysUntilExpiration = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($alerte->date_fin), false);

            if ($daysUntilExpiration < 0) {
                $dashboardStatusCounts['expired']++;
            } elseif ($daysUntilExpiration <= 30) {
                $dashboardStatusCounts['soon']++;
            } else {
                $dashboardStatusCounts['valid']++;
            }
        }
    }

    $dashboardVehiclePhotoUrl = function ($photo, $vehicle = null) {
        if (!empty($vehicle?->photo_url_map) && !empty($vehicle->photo_url_map[$photo])) {
            return $vehicle->photo_url_map[$photo];
        }

        if (empty($photo)) {
            return asset('assets/img/default-car.png');
        }

        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            return $photo;
        }

        if (file_exists(public_path($photo))) {
            return asset($photo);
        }

        if (($vehicle->provenance_by ?? null) != 1 && ($vehicle->provenance ?? null) !== 'wasabi') {
            return 'https://api-usager.tooauto.com/' . ltrim($photo, '/');
        }

        $wasabiUrl = rtrim((string) config('wasabi.url'), '/');

        return $wasabiUrl !== '' ? $wasabiUrl . '/' . ltrim($photo, '/') : asset('assets/img/default-car.png');
    };
@endphp

<style>
    .fleet-dashboard {
        --fleet-ink: #111827;
        --fleet-muted: #64748b;
        --fleet-soft: #f6f8fb;
        --fleet-line: #e8edf4;
        --fleet-shadow: 0 18px 48px rgba(15, 23, 42, 0.07);
        background: linear-gradient(180deg, #fbfcfe 0%, #f7f9fc 46%, #ffffff 100%);
        border-radius: 8px;
        margin: -6px -4px 0;
        padding: 8px 4px 24px;
    }

    .fleet-hero,
    .fleet-panel,
    .fleet-chart-card,
    .fleet-kpi-card,
    .fleet-alert-card {
        background: rgba(255, 255, 255, 0.94);
        border: 1px solid var(--fleet-line);
        border-radius: 8px;
        box-shadow: var(--fleet-shadow);
    }

    .fleet-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        padding: 24px;
        margin-bottom: 22px;
    }

    .fleet-eyebrow {
        color: #2563eb;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .fleet-title {
        color: var(--fleet-ink);
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
        margin: 0;
    }

    .fleet-subtitle {
        color: var(--fleet-muted);
        font-size: 14px;
        margin: 10px 0 0;
        max-width: 620px;
    }

    .fleet-hero-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(124px, 1fr));
        gap: 10px;
        min-width: 280px;
    }

    .fleet-mini-metric {
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        padding: 14px;
    }

    .fleet-mini-metric span {
        color: var(--fleet-muted);
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .fleet-mini-metric strong {
        color: var(--fleet-ink);
        display: block;
        font-size: 24px;
        line-height: 1;
    }

    .fleet-kpi-card {
        height: 100%;
        overflow: hidden;
        padding: 20px;
        position: relative;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fleet-kpi-card:hover,
    .fleet-alert-card:hover {
        box-shadow: 0 22px 52px rgba(15, 23, 42, 0.1);
        transform: translateY(-2px);
    }

    .fleet-kpi-top {
        align-items: center;
        display: flex;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    .fleet-icon {
        align-items: center;
        border-radius: 8px;
        display: inline-flex;
        height: 46px;
        justify-content: center;
        width: 46px;
    }

    .fleet-icon svg {
        height: 22px;
        width: 22px;
    }

    .fleet-count {
        color: var(--fleet-ink);
        font-size: 34px;
        font-weight: 800;
        letter-spacing: 0;
        line-height: 1;
        margin-bottom: 8px;
    }

    .fleet-kpi-label {
        color: var(--fleet-muted);
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .fleet-ratio {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 999px;
        color: #334155;
        display: inline-flex;
        font-size: 12px;
        font-weight: 700;
        gap: 6px;
        padding: 7px 10px;
    }

    .fleet-progress {
        background: #eef2f7;
        border-radius: 999px;
        height: 7px;
        overflow: hidden;
    }

    .fleet-progress span {
        border-radius: inherit;
        display: block;
        height: 100%;
    }

    .fleet-accent-blue .fleet-icon,
    .fleet-accent-blue .fleet-progress span {
        background: #2563eb;
        color: #ffffff;
    }

    .fleet-accent-amber .fleet-icon,
    .fleet-accent-amber .fleet-progress span {
        background: #f59e0b;
        color: #ffffff;
    }

    .fleet-accent-cyan .fleet-icon,
    .fleet-accent-cyan .fleet-progress span {
        background: #0891b2;
        color: #ffffff;
    }

    .fleet-accent-emerald .fleet-icon,
    .fleet-accent-emerald .fleet-progress span {
        background: #10b981;
        color: #ffffff;
    }

    .fleet-section-header {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin: 30px 0 14px;
    }

    .fleet-section-header h4,
    .fleet-section-header h5 {
        color: var(--fleet-ink);
        font-size: 18px;
        font-weight: 800;
        margin: 0;
    }

    .fleet-section-header p {
        color: var(--fleet-muted);
        font-size: 13px;
        margin: 4px 0 0;
    }

    .fleet-panel {
        overflow: hidden;
    }

    .fleet-chart-card {
        height: 100%;
        padding: 20px;
    }

    .fleet-chart-head {
        align-items: flex-start;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
    }

    .fleet-chart-title {
        color: var(--fleet-ink);
        font-size: 15px;
        font-weight: 800;
        margin: 0;
    }

    .fleet-chart-caption {
        color: var(--fleet-muted);
        font-size: 12px;
        margin: 4px 0 0;
    }

    .fleet-chart-icon {
        align-items: center;
        background: #f8fafc;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        color: #2563eb;
        display: inline-flex;
        height: 38px;
        justify-content: center;
        width: 38px;
    }

    .fleet-chart-icon svg {
        height: 18px;
        width: 18px;
    }

    .fleet-chart-wrap {
        height: 260px;
        position: relative;
    }

    .fleet-chart-wrap.compact {
        height: 220px;
    }

    .fleet-table {
        margin: 0;
    }

    .fleet-table thead th {
        background: #f8fafc;
        border-bottom: 1px solid var(--fleet-line);
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        padding: 14px 16px;
        text-transform: uppercase;
    }

    .fleet-table tbody td {
        border-color: #eef2f7;
        color: #334155;
        padding: 14px 16px;
        vertical-align: middle;
    }

    .fleet-table tbody tr:hover {
        background: #fbfdff;
    }

    .fleet-soft-badge {
        border-radius: 999px;
        display: inline-flex;
        font-size: 12px;
        font-weight: 800;
        padding: 7px 10px;
    }

    .fleet-soft-badge.danger {
        background: #fff1f2;
        color: #be123c;
    }

    .fleet-soft-badge.warning {
        background: #fffbeb;
        color: #b45309;
    }

    .fleet-soft-badge.success {
        background: #ecfdf5;
        color: #047857;
    }

    .fleet-action-btn {
        border-color: #d7e2f0;
        border-radius: 999px;
        color: #2563eb;
        font-weight: 700;
        padding: 7px 12px;
    }

    .fleet-alert-card {
        cursor: pointer;
        height: 100%;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .fleet-vehicle-img {
        aspect-ratio: 4 / 3;
        background: #f1f5f9;
        cursor: pointer;
        height: 100%;
        min-height: 164px;
        object-fit: cover;
        width: 100%;
    }

    .fleet-alert-body {
        padding: 18px;
    }

    .fleet-vehicle-title {
        color: var(--fleet-ink);
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .fleet-vehicle-model {
        color: var(--fleet-muted);
        font-size: 13px;
        margin-bottom: 14px;
    }

    .fleet-detail-grid {
        display: grid;
        gap: 9px;
        margin-bottom: 14px;
    }

    .fleet-detail {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 12px;
    }

    .fleet-detail span {
        color: var(--fleet-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .fleet-detail strong {
        color: #1f2937;
        font-size: 13px;
        font-weight: 800;
        text-align: right;
    }

    .fleet-detail-modal .modal-content {
        border: 0;
        border-radius: 8px;
        overflow: hidden;
    }

    .fleet-detail-modal .modal-header {
        background: #130d0d;
        border: 0;
        color: #ffffff;
        padding: 18px 22px;
    }

    .fleet-detail-modal .btn-close {
        filter: invert(1);
    }

    .fleet-detail-modal .modal-body {
        background: #f8fafc;
        padding: 20px;
    }

    .fleet-detail-modal .modal-footer {
        background: #ffffff;
        border-top: 1px solid var(--fleet-line);
        padding: 14px 20px;
    }

    .fleet-modal-close-btn {
        align-items: center;
        background: #130d0d;
        border: 1px solid #130d0d;
        border-radius: 8px;
        color: #ffffff;
        display: inline-flex;
        font-weight: 850;
        gap: 8px;
        min-height: 40px;
        padding: 9px 14px;
    }

    .fleet-modal-close-btn:hover {
        background: #efc242;
        border-color: #efc242;
        color: #130d0d;
    }

    .fleet-detail-photo {
        background: #ffffff;
        border: 1px solid var(--fleet-line);
        border-radius: 8px;
        overflow: hidden;
    }

    .fleet-detail-info {
        background: #ffffff;
        border: 1px solid var(--fleet-line);
        border-radius: 8px;
        height: 100%;
        padding: 16px;
    }

    .fleet-detail-info h6 {
        color: #130d0d;
        font-size: 13px;
        font-weight: 900;
        margin: 0 0 12px;
        text-transform: uppercase;
    }

    .fleet-detail-row {
        border-bottom: 1px solid #eef2f7;
        display: flex;
        gap: 12px;
        justify-content: space-between;
        padding: 9px 0;
    }

    .fleet-detail-row:last-child {
        border-bottom: 0;
    }

    .fleet-detail-row span {
        color: var(--fleet-muted);
        font-size: 12px;
        font-weight: 700;
    }

    .fleet-detail-row strong {
        color: #1f2937;
        font-size: 13px;
        font-weight: 850;
        text-align: right;
    }

    .fleet-empty {
        background: #ffffff;
        border: 1px dashed #d9e2ef;
        border-radius: 8px;
        color: var(--fleet-muted);
        padding: 22px;
        text-align: center;
    }

    @media (max-width: 991.98px) {
        .fleet-hero {
            align-items: stretch;
            flex-direction: column;
        }

        .fleet-hero-metrics {
            min-width: 0;
        }
    }

    @media (max-width: 575.98px) {
        .fleet-hero {
            padding: 18px;
        }

        .fleet-title {
            font-size: 23px;
        }

        .fleet-hero-metrics {
            grid-template-columns: 1fr;
        }

        .fleet-section-header {
            align-items: flex-start;
            flex-direction: column;
        }
    }
</style>

<div class="page-wrapper">
    <div class="content">
        <div class="fleet-dashboard">
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

            <div class="fleet-hero">
                <div>
                    <div class="fleet-eyebrow">Pilotage flotte</div>
                    <h1 class="fleet-title">Tableau de bord des alertes</h1>
                    <p class="fleet-subtitle">Une vue claire des échéances importantes, des documents à surveiller et des véhicules à traiter en priorité.</p>
                </div>
                <div class="fleet-hero-metrics">
                    <div class="fleet-mini-metric">
                        <span>Total alertes</span>
                        <strong>{{ $totalAlertes }}</strong>
                    </div>
                    <div class="fleet-mini-metric">
                        <span>À surveiller</span>
                        <strong>{{ $totalCritiques }}</strong>
                    </div>
                </div>
            </div>

            <div class="row">
                @foreach($dashboardAlertGroups as $group)
                    @php
                        $total = $alertes_count[$group['key']] ?? 0;
                        $critical = $alertes_critique[$group['key']] ?? 0;
                        $progress = $total > 0 ? min(100, round(($critical / $total) * 100)) : 0;
                    @endphp
                    <div class="col-xl-3 col-sm-6 col-12 mb-3">
                        <div class="fleet-kpi-card fleet-accent-{{ $group['accent'] }}">
                            <div class="fleet-kpi-top">
                                <span class="fleet-icon"><i data-feather="{{ $group['icon'] }}"></i></span>
                                <span class="fleet-ratio" data-bs-toggle="tooltip" data-bs-placement="top" title="Alertes expirées ou à moins de 30 jours / total">
                                    {{ $critical }} / {{ $total }}
                                </span>
                            </div>
                            <div class="fleet-count">{{ $total }}</div>
                            <div class="fleet-kpi-label">{{ $group['title'] }}</div>
                            <div class="fleet-progress">
                                <span style="width: {{ $progress }}%"></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="fleet-section-header">
                <div>
                    <h4>Analyse visuelle</h4>
                    <p>Répartition des alertes et niveau de criticité par catégorie.</p>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-lg-6 col-12 mb-4">
                    <div class="fleet-chart-card">
                        <div class="fleet-chart-head">
                            <div>
                                <h5 class="fleet-chart-title">Répartition par type</h5>
                                <p class="fleet-chart-caption">Volume total des alertes enregistrées.</p>
                            </div>
                            <span class="fleet-chart-icon"><i data-feather="pie-chart"></i></span>
                        </div>
                        <div class="fleet-chart-wrap compact">
                            <canvas id="alertTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5 col-lg-6 col-12 mb-4">
                    <div class="fleet-chart-card">
                        <div class="fleet-chart-head">
                            <div>
                                <h5 class="fleet-chart-title">Total vs critique</h5>
                                <p class="fleet-chart-caption">Comparaison des alertes à surveiller.</p>
                            </div>
                            <span class="fleet-chart-icon"><i data-feather="bar-chart-2"></i></span>
                        </div>
                        <div class="fleet-chart-wrap">
                            <canvas id="alertCriticalChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-12 mb-4">
                    <div class="fleet-chart-card">
                        <div class="fleet-chart-head">
                            <div>
                                <h5 class="fleet-chart-title">Statut global</h5>
                                <p class="fleet-chart-caption">Vue santé de la flotte.</p>
                            </div>
                            <span class="fleet-chart-icon"><i data-feather="activity"></i></span>
                        </div>
                        <div class="fleet-chart-wrap compact">
                            <canvas id="alertStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fleet-section-header">
                <div>
                    <h4>Échéances prioritaires</h4>
                    <p>Les 10 alertes les plus proches de l'expiration.</p>
                </div>
            </div>

            <div class="fleet-panel">
                <div class="table-responsive">
                    <table class="table fleet-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Type</th>
                                <th>Véhicule</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                <th>Kilométrage</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertes_proches as $key => $alerte)
                                @php
                                    $today = \Carbon\Carbon::today();
                                    $dateFin = \Carbon\Carbon::parse($alerte->date_fin);
                                    $daysUntilExpiration = $today->diffInDays($dateFin, false);
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $alerte->type_alert->libelle ?? '' }}</td>
                                    <td><strong>{{ $alerte->vehicule->matricule ?? '' }}</strong></td>
                                    <td>{{ $alerte->date_debut }}</td>
                                    <td>{{ $alerte->date_fin }}</td>
                                    <td>{{ $alerte->kilometrage ?? 'N/A' }}</td>
                                    <td>
                                        @if($daysUntilExpiration < 0)
                                            <span class="fleet-soft-badge danger">Expiré</span>
                                        @elseif($daysUntilExpiration <= 30)
                                            <span class="fleet-soft-badge warning">Expire bientôt ({{ $daysUntilExpiration }}j)</span>
                                        @else
                                            <span class="fleet-soft-badge success">Valide ({{ $daysUntilExpiration }}j)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary fleet-action-btn" data-bs-toggle="modal" data-bs-target="#updateAlerte{{ $alerte->id }}">
                                            Mettre à jour
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="updateAlerte{{ $alerte->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('alerte.update', $alerte->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Mettre à jour l'alerte</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label>Type d'alerte</label>
                                                        <input type="text" class="form-control" value="{{ $alerte->type_alert->libelle ?? '' }}" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Véhicule</label>
                                                        <input type="text" class="form-control" value="{{ $alerte->vehicule->matricule ?? '' }}" disabled>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Date de début</label>
                                                        <input type="date" name="date_debut" class="form-control" value="{{ $alerte->date_debut }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label>Date de fin</label>
                                                        <input type="date" name="date_fin" class="form-control" value="{{ $alerte->date_fin }}" required>
                                                    </div>
                                                    @if($alerte->type_alert_id == 2)
                                                        <div class="mb-3">
                                                            <label>Kilométrage</label>
                                                            <input type="number" name="kilometrage" class="form-control" value="{{ $alerte->kilometrage }}" min="0" required>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="fleet-empty">Aucune alerte proche de l'expiration.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="fleet-section-header">
                <div>
                    <h4>Alertes par type</h4>
                    <p>Lecture rapide par catégorie et par véhicule.</p>
                </div>
            </div>

            @foreach($dashboardAlertGroups as $group)
                @php
                    $total = $alertes_count[$group['key']] ?? 0;
                    $critical = $alertes_critique[$group['key']] ?? 0;
                @endphp
                <div class="fleet-section-header">
                    <div>
                        <h5>{{ $group['title'] }}</h5>
                    </div>
                    <span class="fleet-ratio" data-bs-toggle="tooltip" data-bs-placement="top" title="Alertes expirées ou à moins de 30 jours / total">
                        {{ $critical }} / {{ $total }}
                    </span>
                </div>

                <div class="row">
                    @forelse($group['collection'] as $alerte)
                        @php
                            $photos = json_decode($alerte->vehicule->photos ?? '[]', true) ?: [];
                            $mainPhoto = isset($photos[0]) ? $dashboardVehiclePhotoUrl($photos[0], $alerte->vehicule) : asset('assets/img/default-car.png');
                            $today = \Carbon\Carbon::today();
                            $dateFin = \Carbon\Carbon::parse($alerte->date_fin);
                            $daysUntilExpiration = $today->diffInDays($dateFin, false);
                        @endphp
                        <div class="col-xl-4 col-md-6 col-12 mb-4">
                            <div class="fleet-alert-card" data-bs-toggle="modal" data-bs-target="#alertDetailsModal{{ $group['modal'] }}{{ $alerte->id }}">
                                <div class="row g-0 h-100">
                                    <div class="col-md-5">
                                        <img src="{{ $mainPhoto }}" alt="Photo véhicule" class="fleet-vehicle-img">
                                    </div>
                                    <div class="col-md-7">
                                        <div class="fleet-alert-body">
                                            <div class="fleet-vehicle-title">{{ $alerte->vehicule->matricule ?? 'N/A' }}</div>
                                            <div class="fleet-vehicle-model">{{ $alerte->vehicule->modele ?? 'Modèle non renseigné' }}</div>

                                            <div class="fleet-detail-grid">
                                                @if($group['show_mileage'])
                                                    <div class="fleet-detail">
                                                        <span>Kilométrage</span>
                                                        <strong>{{ $alerte->kilometrage ?? 'N/A' }}</strong>
                                                    </div>
                                                @endif
                                                <div class="fleet-detail">
                                                    <span>Date début</span>
                                                    <strong>{{ $alerte->date_debut }}</strong>
                                                </div>
                                                <div class="fleet-detail">
                                                    <span>Date fin</span>
                                                    <strong>{{ $alerte->date_fin }}</strong>
                                                </div>
                                            </div>

                                            @if($daysUntilExpiration < 0)
                                                <span class="fleet-soft-badge danger">Expiré</span>
                                            @elseif($daysUntilExpiration <= 30)
                                                <span class="fleet-soft-badge warning">Expire bientôt ({{ $daysUntilExpiration }}j)</span>
                                            @else
                                                <span class="fleet-soft-badge success">Valide ({{ $daysUntilExpiration }}j)</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade fleet-detail-modal" id="alertDetailsModal{{ $group['modal'] }}{{ $alerte->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">{{ $group['title'] }} - {{ $alerte->vehicule->matricule ?? 'Véhicule non renseigné' }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row g-3">
                                            <div class="col-lg-6">
                                                <div class="fleet-detail-photo">
                                                    <div id="carouselPhotos{{ $group['modal'] }}{{ $alerte->id }}" class="carousel slide" data-bs-ride="carousel">
                                                        <div class="carousel-inner">
                                                            @forelse($photos as $k => $photo)
                                                                <div class="carousel-item {{ $k === 0 ? 'active' : '' }}">
                                                                    <img src="{{ $dashboardVehiclePhotoUrl($photo, $alerte->vehicule) }}" class="d-block w-100" style="height:420px;object-fit:contain;background:#ffffff;" alt="Photo véhicule">
                                                                </div>
                                                            @empty
                                                                <div class="carousel-item active">
                                                                    <img src="{{ asset('assets/img/default-car.png') }}" class="d-block w-100" style="height:420px;object-fit:contain;background:#ffffff;" alt="Photo véhicule">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                        @if(count($photos) > 1)
                                                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselPhotos{{ $group['modal'] }}{{ $alerte->id }}" data-bs-slide="prev">
                                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Précédent</span>
                                                            </button>
                                                            <button class="carousel-control-next" type="button" data-bs-target="#carouselPhotos{{ $group['modal'] }}{{ $alerte->id }}" data-bs-slide="next">
                                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                <span class="visually-hidden">Suivant</span>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="fleet-detail-info">
                                                    <h6>Détails véhicule</h6>
                                                    <div class="fleet-detail-row">
                                                        <span>Matricule</span>
                                                        <strong>{{ $alerte->vehicule->matricule ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Modèle</span>
                                                        <strong>{{ $alerte->vehicule->modele ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Marque</span>
                                                        <strong>{{ $alerte->vehicule->marque->libelle ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Type</span>
                                                        <strong>{{ $alerte->vehicule->type_de_vehicule->libelle ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Énergie</span>
                                                        <strong>{{ $alerte->vehicule->type_de_carburant->libelle ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Carte grise</span>
                                                        <strong>{{ $alerte->vehicule->carte_grise ?? 'N/A' }}</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3">
                                                <div class="fleet-detail-info">
                                                    <h6>Détails alerte</h6>
                                                    <div class="fleet-detail-row">
                                                        <span>Type</span>
                                                        <strong>{{ $alerte->type_alert->libelle ?? $group['title'] }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Date début</span>
                                                        <strong>{{ $alerte->date_debut ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Date fin</span>
                                                        <strong>{{ $alerte->date_fin ?? 'N/A' }}</strong>
                                                    </div>
                                                    <div class="fleet-detail-row">
                                                        <span>Jours restants</span>
                                                        <strong>{{ $daysUntilExpiration }} jour(s)</strong>
                                                    </div>
                                                    @if($group['show_mileage'])
                                                        <div class="fleet-detail-row">
                                                            <span>Kilométrage</span>
                                                            <strong>{{ $alerte->kilometrage ? number_format($alerte->kilometrage, 0, ',', ' ') . ' km' : 'N/A' }}</strong>
                                                        </div>
                                                    @endif
                                                    <div class="fleet-detail-row">
                                                        <span>Statut</span>
                                                        <strong>
                                                            @if($daysUntilExpiration < 0)
                                                                Expiré
                                                            @elseif($daysUntilExpiration <= 30)
                                                                Expire bientôt
                                                            @else
                                                                Valide
                                                            @endif
                                                        </strong>
                                                    </div>
                                                    <div class="mt-3">
                                                        <a href="{{ route('alerte.edit', $alerte->id) }}" class="btn btn-primary w-100">Modifier l'alerte</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="fleet-modal-close-btn" data-bs-dismiss="modal">
                                            <i class="fas fa-times"></i>
                                            Fermer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 mb-4">
                            <div class="fleet-empty">Aucune alerte pour cette catégorie.</div>
                        </div>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>
</div>

<script src="{{ asset('assets/plugins/chartjs/chart.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.feather) {
            feather.replace();
        }

        if (window.bootstrap) {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        if (window.Chart) {
            Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
            Chart.defaults.color = '#64748b';

            var chartLabels = @json(collect($dashboardAlertGroups)->pluck('title')->values());
            var chartTotals = @json(collect($dashboardAlertGroups)->map(fn ($group) => $alertes_count[$group['key']] ?? 0)->values());
            var chartCritical = @json(collect($dashboardAlertGroups)->map(fn ($group) => $alertes_critique[$group['key']] ?? 0)->values());
            var chartColors = ['#2563eb', '#f59e0b', '#0891b2', '#10b981'];

            new Chart(document.getElementById('alertTypeChart'), {
                type: 'doughnut',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        data: chartTotals,
                        backgroundColor: chartColors,
                        borderColor: '#ffffff',
                        borderWidth: 4,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                padding: 16
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('alertCriticalChart'), {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Total',
                            data: chartTotals,
                            backgroundColor: '#dbeafe',
                            borderRadius: 8,
                            maxBarThickness: 34
                        },
                        {
                            label: 'Critique',
                            data: chartCritical,
                            backgroundColor: '#2563eb',
                            borderRadius: 8,
                            maxBarThickness: 34
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 10,
                                boxHeight: 10,
                                usePointStyle: true,
                                padding: 16
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            }
                        },
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#eef2f7'
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('alertStatusChart'), {
                type: 'bar',
                data: {
                    labels: ['Expiré', 'Bientôt', 'Valide'],
                    datasets: [{
                        data: [
                            {{ $dashboardStatusCounts['expired'] }},
                            {{ $dashboardStatusCounts['soon'] }},
                            {{ $dashboardStatusCounts['valid'] }}
                        ],
                        backgroundColor: ['#fb7185', '#fbbf24', '#34d399'],
                        borderRadius: 8,
                        maxBarThickness: 34
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            },
                            grid: {
                                color: '#eef2f7'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    });
</script>

@include('layouts.footer')
