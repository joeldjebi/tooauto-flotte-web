@include('layouts.header')
@include('layouts.menu')

@php
    $statuts = [
        'planifie' => 'Planifié',
        'en_cours' => 'En cours',
        'realise' => 'Réalisé',
        'annule' => 'Annulé',
    ];
    $statutBadges = [
        'planifie' => 'primary',
        'en_cours' => 'warning',
        'realise' => 'success',
        'annule' => 'secondary',
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
                <h4 class="fleet-module-title">Entretien</h4>
                <p class="fleet-module-copy">Planifiez, suivez et archivez les opérations d'entretien pour chaque véhicule de la flotte.</p>
            </div>
            <button class="fleet-module-action" data-bs-toggle="modal" data-bs-target="#addEntretien">
                <i class="fas fa-plus"></i>
                <span>Nouvel entretien</span>
            </button>
        </div>

        <div class="fleet-stat-grid">
            <div class="fleet-stat-card">
                <span>Total</span>
                <strong>{{ $entretiens->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Planifiés</span>
                <strong>{{ $entretiens->where('statut', 'planifie')->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>En cours</span>
                <strong>{{ $entretiens->where('statut', 'en_cours')->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Réalisés</span>
                <strong>{{ $entretiens->where('statut', 'realise')->count() }}</strong>
            </div>
        </div>

        <div class="fleet-filter-card">
            <form method="GET" action="{{ route('alerte.entretien') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="vehicule_id" class="form-control">
                        <option value="">Tous les véhicules</option>
                        @foreach($vehicules as $vehicule)
                            <option value="{{ $vehicule->id }}" @selected(($filters['vehicule_id'] ?? '') == $vehicule->id)>{{ $vehicule->matricule }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="statut" class="form-control">
                        <option value="">Tous les statuts</option>
                        @foreach($statuts as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['statut'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-primary" type="submit">Filtrer</button>
                    <a href="{{ route('alerte.entretien') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="card fleet-table-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Liste des entretiens</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle fleet-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Véhicule</th>
                                <th>Utilisateur</th>
                                <th>Type</th>
                                <th>Titre</th>
                                <th>Date prévue</th>
                                <th>Réalisation</th>
                                <th>Prestataire</th>
                                <th>Coût</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($entretiens as $key => $entretien)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="fleet-vehicle-pill">{{ $entretien->vehicule->matricule ?? 'N/A' }}</span></td>
                                    <td>{{ trim(($entretien->chauffeur->nom ?? '') . ' ' . ($entretien->chauffeur->prenoms ?? '')) ?: 'N/A' }}</td>
                                    <td>{{ $entretien->type_entretien }}</td>
                                    <td><strong>{{ $entretien->titre }}</strong></td>
                                    <td>{{ $entretien->date_prevue ?? 'N/A' }}</td>
                                    <td>{{ $entretien->date_realisation ?? 'N/A' }}</td>
                                    <td>{{ $entretien->prestataire ?? 'N/A' }}</td>
                                    <td>{{ $entretien->cout ? number_format($entretien->cout, 0, ',', ' ') : 'N/A' }}</td>
                                    <td><span class="badge bg-{{ $statutBadges[$entretien->statut] ?? 'secondary' }}">{{ $statuts[$entretien->statut] ?? $entretien->statut }}</span></td>
                                    <td>
                                        <div class="fleet-actions">
                                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editEntretien{{ $entretien->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('alerte.entretien.destroy', $entretien->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cet entretien ?')" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @include('alerte.partials.entretien-form', ['entretien' => $entretien, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'prestataires' => $prestataires, 'type_entretiens' => $type_entretiens, 'statuts' => $statuts])
                            @empty
                                <tr>
                                    <td colspan="11" class="fleet-empty-state">Aucun entretien enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('alerte.partials.entretien-form', ['entretien' => null, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'prestataires' => $prestataires, 'type_entretiens' => $type_entretiens, 'statuts' => $statuts])
@include('layouts.footer')
