@include('layouts.header')
@include('layouts.menu')

@php
    $statuts = [
        'nouveau' => 'Nouveau',
        'diagnostic' => 'Diagnostic',
        'proforma' => 'Proforma',
        'validation' => 'Validation',
        'reparation' => 'Réparation',
        'termine' => 'Terminé',
        'annule' => 'Annulé',
    ];
    $validations = [
        'en_attente' => 'En attente',
        'validee' => 'Validée',
        'refusee' => 'Refusée',
    ];
    $statutBadges = [
        'nouveau' => 'primary',
        'diagnostic' => 'info',
        'proforma' => 'warning',
        'validation' => 'warning',
        'reparation' => 'dark',
        'termine' => 'success',
        'annule' => 'secondary',
    ];
    $validationBadges = [
        'en_attente' => 'warning',
        'validee' => 'success',
        'refusee' => 'danger',
    ];
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
                <h4 class="fleet-module-title">Réparations & Suivi</h4>
                <p class="fleet-module-copy">Suivez chaque panne depuis le diagnostic jusqu'à la validation financière et au retour en service.</p>
            </div>
            <button class="fleet-module-action" data-bs-toggle="modal" data-bs-target="#addReparation">
                <i class="fas fa-plus"></i>
                <span>Nouveau dossier</span>
            </button>
        </div>

        <div class="fleet-stat-grid">
            <div class="fleet-stat-card">
                <span>Total</span>
                <strong>{{ $reparations->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>En réparation</span>
                <strong>{{ $reparations->where('statut', 'reparation')->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Validation</span>
                <strong>{{ $reparations->where('validation_financiere', 'en_attente')->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Terminés</span>
                <strong>{{ $reparations->where('statut', 'termine')->count() }}</strong>
            </div>
        </div>

        <div class="fleet-filter-card">
            <form method="GET" action="{{ route('alerte.reparation') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <select name="vehicule_id" class="form-control">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicules as $vehicule)
                            <option value="{{ $vehicule->id }}" @selected(($filters['vehicule_id'] ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-control">
                        <option value="">Tous les statuts</option>
                        @foreach($statuts as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="validation_financiere" class="form-control">
                        <option value="">Toutes les validations</option>
                        @foreach($validations as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['validation_financiere'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 text-md-end">
                    <button class="btn btn-primary" type="submit">Filtrer</button>
                    <a href="{{ route('alerte.reparation') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="card fleet-table-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Liste des dossiers de réparation</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle fleet-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Véhicule</th>
                                <th>Utilisateur</th>
                                <th>Titre</th>
                                <th>Prestataire</th>
                                <th>Proforma</th>
                                <th>Validation</th>
                                <th>Coût final</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reparations as $key => $reparation)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="fleet-vehicle-pill">{{ $reparation->vehicule->matricule ?? 'N/A' }}</span></td>
                                    <td>{{ trim(($reparation->chauffeur->nom ?? '') . ' ' . ($reparation->chauffeur->prenoms ?? '')) ?: 'N/A' }}</td>
                                    <td><strong>{{ $reparation->titre }}</strong></td>
                                    <td>{{ $reparation->prestataire_nom ?? 'N/A' }}</td>
                                    <td>{{ $reparation->proforma_montant ? number_format($reparation->proforma_montant, 0, ',', ' ') : 'N/A' }}</td>
                                    <td><span class="badge bg-{{ $validationBadges[$reparation->validation_financiere] ?? 'secondary' }}">{{ $validations[$reparation->validation_financiere] ?? $reparation->validation_financiere }}</span></td>
                                    <td>{{ $reparation->cout_final ? number_format($reparation->cout_final, 0, ',', ' ') : 'N/A' }}</td>
                                    <td><span class="badge bg-{{ $statutBadges[$reparation->statut] ?? 'secondary' }}">{{ $statuts[$reparation->statut] ?? $reparation->statut }}</span></td>
                                    <td>
                                        <div class="fleet-actions">
                                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editReparation{{ $reparation->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('alerte.reparation.destroy', $reparation->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer ce dossier ?')" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @include('alerte.partials.reparation-form', ['reparation' => $reparation, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'prestataires' => $prestataires, 'assistances' => $assistances, 'statuts' => $statuts, 'validations' => $validations])
                            @empty
                                <tr>
                                    <td colspan="10" class="fleet-empty-state">Aucun dossier de réparation enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('alerte.partials.reparation-form', ['reparation' => null, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'prestataires' => $prestataires, 'assistances' => $assistances, 'statuts' => $statuts, 'validations' => $validations])
@include('layouts.footer')
