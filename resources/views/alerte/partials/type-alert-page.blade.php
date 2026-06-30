@include('layouts.header')
@include('layouts.menu')

@php
    $today = \Carbon\Carbon::today();
    $expiredCount = $alertes->filter(fn ($alerte) => \Carbon\Carbon::parse($alerte->date_fin)->lt($today))->count();
    $nearCount = $alertes->filter(function ($alerte) use ($today) {
        $dateFin = \Carbon\Carbon::parse($alerte->date_fin);
        return $dateFin->gte($today) && $dateFin->lte($today->copy()->addDays(30));
    })->count();
    $validCount = $alertes->count() - $expiredCount - $nearCount;
@endphp
@include('alerte.partials.module-styles')

<div class="page-wrapper fleet-module-page">
    <div class="content">
        @include('layouts.fileariane')

        @if(session()->has('message'))
            <div class="alert {{ session()->get('type') }}" style="padding: 10px">{{ session()->get('message') }}</div>
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

        <div class="fleet-module-hero">
            <div>
                <span class="fleet-module-kicker">Alertes</span>
                <h4 class="fleet-module-title">{{ $pageTitle }}</h4>
                <p class="fleet-module-copy">{{ $pageDescription }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('alerte.index') }}" class="btn btn-outline-secondary">Toutes les alertes</a>
                <button class="fleet-module-action" data-bs-toggle="modal" data-bs-target="#addTypeAlert">
                    <i class="fas fa-plus"></i>
                    <span>Nouvelle alerte</span>
                </button>
            </div>
        </div>

        <div class="fleet-stat-grid">
            <div class="fleet-stat-card">
                <span>Total</span>
                <strong>{{ $alertes->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Valides</span>
                <strong>{{ $validCount }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Expire bientôt</span>
                <strong>{{ $nearCount }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Expirées</span>
                <strong>{{ $expiredCount }}</strong>
            </div>
        </div>

        <div class="fleet-filter-card">
            <form method="GET" action="{{ url()->current() }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="vehicule_id" class="form-control">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicules as $vehicule)
                            <option value="{{ $vehicule->id }}" @selected(($filters['vehicule_id'] ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control" value="{{ $filters['date_debut'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control" value="{{ $filters['date_fin'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-control">
                        <option value="">Tous les statuts</option>
                        <option value="expire" @selected(($filters['statut'] ?? '') === 'expire')>Expiré</option>
                        <option value="proche" @selected(($filters['statut'] ?? '') === 'proche')>Expire bientôt</option>
                        <option value="valide" @selected(($filters['statut'] ?? '') === 'valide')>Valide</option>
                    </select>
                </div>
                <div class="col-md-2 text-md-end">
                    <button class="btn btn-primary" type="submit">Filtrer</button>
                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="card fleet-table-card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $listTitle }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle fleet-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Véhicule</th>
                                <th>Date début</th>
                                <th>Date fin</th>
                                @if($showKilometrage)
                                    <th>Kilométrage</th>
                                @endif
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alertes as $key => $alerte)
                                @php
                                    $dateFin = \Carbon\Carbon::parse($alerte->date_fin);
                                    $daysUntilExpiration = $today->diffInDays($dateFin, false);
                                @endphp
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="fleet-vehicle-pill">{{ $alerte->vehicule->matricule ?? 'N/A' }}</span></td>
                                    <td>{{ $alerte->date_debut }}</td>
                                    <td>{{ $alerte->date_fin }}</td>
                                    @if($showKilometrage)
                                        <td>{{ $alerte->kilometrage ? number_format($alerte->kilometrage, 0, ',', ' ') . ' km' : 'N/A' }}</td>
                                    @endif
                                    <td>
                                        @if($daysUntilExpiration < 0)
                                            <span class="badge bg-danger">Expiré</span>
                                        @elseif($daysUntilExpiration <= 30)
                                            <span class="badge bg-warning">Expire bientôt ({{ $daysUntilExpiration }} jours)</span>
                                        @else
                                            <span class="badge bg-success">Valide ({{ $daysUntilExpiration }} jours)</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fleet-actions">
                                            <a href="{{ route('alerte.edit', $alerte->id) }}" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('alerte.destroy', $alerte->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette alerte ?')" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $showKilometrage ? 7 : 6 }}" class="fleet-empty-state">{{ $emptyText }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('alerte.partials.type-alert-form', ['alertTypeId' => $alertTypeId, 'showKilometrage' => $showKilometrage, 'vehicules' => $vehicules])
@include('layouts.footer')
