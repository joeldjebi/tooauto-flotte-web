@include('layouts.header')
@include('layouts.menu')

@php
    $modesPaiement = [
        'espece' => 'Espèce',
        'carte' => 'Carte',
        'virement' => 'Virement',
        'mobile_money' => 'Mobile money',
        'autre' => 'Autre',
    ];
    $totalLitres = $carburants->sum('quantite_litres');
    $totalMontant = $carburants->sum('montant_total');
    $coutMoyen = $totalLitres > 0 ? $totalMontant / $totalLitres : 0;
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
                <span class="fleet-module-kicker">Services flotte</span>
                <h4 class="fleet-module-title">Carburant & Consommation</h4>
                <p class="fleet-module-copy">Centralisez les pleins, les coûts carburant et les kilométrages pour mieux suivre la consommation de chaque véhicule.</p>
            </div>
            <button class="fleet-module-action" data-bs-toggle="modal" data-bs-target="#addCarburant">
                <i class="fas fa-plus"></i>
                <span>Nouveau plein</span>
            </button>
        </div>

        <div class="fleet-stat-grid">
            <div class="fleet-stat-card">
                <span>Total pleins</span>
                <strong>{{ $carburants->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Litres</span>
                <strong>{{ number_format($totalLitres, 1, ',', ' ') }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Dépenses</span>
                <strong>{{ number_format($totalMontant, 0, ',', ' ') }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Coût/L</span>
                <strong>{{ number_format($coutMoyen, 0, ',', ' ') }}</strong>
            </div>
        </div>

        <div class="fleet-filter-card">
            <form method="GET" action="{{ route('alerte.carburant') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="vehicule_id" class="form-control">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicules as $vehicule)
                            <option value="{{ $vehicule->id }}" @selected(($filters['vehicule_id'] ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="type_de_carburant_id" class="form-control">
                        <option value="">Tous les carburants</option>
                        @foreach($type_de_carburants as $type)
                            <option value="{{ $type->id }}" @selected(($filters['type_de_carburant_id'] ?? '') == $type->id)>{{ $type->libelle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_debut" class="form-control" value="{{ $filters['date_debut'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_fin" class="form-control" value="{{ $filters['date_fin'] ?? '' }}">
                </div>
                <div class="col-md-2 text-md-end">
                    <button class="btn btn-primary" type="submit">Filtrer</button>
                    <a href="{{ route('alerte.carburant') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>

        <div class="card fleet-table-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Liste des approvisionnements</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle fleet-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Véhicule</th>
                                <th>Utilisateur</th>
                                <th>Carburant</th>
                                <th>Litres</th>
                                <th>Prix/L</th>
                                <th>Montant</th>
                                <th>Km</th>
                                <th>Station</th>
                                <th>Paiement</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($carburants as $key => $carburant)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $carburant->date_approvisionnement ?? 'N/A' }}</td>
                                    <td><span class="fleet-vehicle-pill">{{ $carburant->vehicule->matricule ?? 'N/A' }}</span></td>
                                    <td>{{ trim(($carburant->chauffeur->nom ?? '') . ' ' . ($carburant->chauffeur->prenoms ?? '')) ?: 'N/A' }}</td>
                                    <td>{{ $carburant->type_carburant ?? $carburant->type_de_carburant->libelle ?? 'N/A' }}</td>
                                    <td>{{ number_format($carburant->quantite_litres, 2, ',', ' ') }}</td>
                                    <td>{{ number_format($carburant->prix_unitaire, 0, ',', ' ') }}</td>
                                    <td><strong>{{ number_format($carburant->montant_total, 0, ',', ' ') }}</strong></td>
                                    <td>{{ $carburant->kilometrage ? number_format($carburant->kilometrage, 0, ',', ' ') : 'N/A' }}</td>
                                    <td>{{ $carburant->station ?? 'N/A' }}</td>
                                    <td>{{ $modesPaiement[$carburant->mode_paiement] ?? $carburant->mode_paiement }}</td>
                                    <td>
                                        <div class="fleet-actions">
                                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editCarburant{{ $carburant->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('alerte.carburant.destroy', $carburant->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cet approvisionnement ?')" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @include('alerte.partials.carburant-form', ['carburant' => $carburant, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'type_de_carburants' => $type_de_carburants, 'modesPaiement' => $modesPaiement])
                            @empty
                                <tr>
                                    <td colspan="12" class="fleet-empty-state">Aucun approvisionnement carburant enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('alerte.partials.carburant-form', ['carburant' => null, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'type_de_carburants' => $type_de_carburants, 'modesPaiement' => $modesPaiement])
@include('layouts.footer')
