@include('layouts.header')
@include('layouts.menu')

@php
    $statuts = [
        'nouvelle' => 'Nouvelle',
        'affectee' => 'Affectée',
        'en_cours' => 'En cours',
        'resolue' => 'Résolue',
        'annulee' => 'Annulée',
    ];
    $urgences = [
        'faible' => 'Faible',
        'moyen' => 'Moyen',
        'eleve' => 'Élevé',
        'critique' => 'Critique',
    ];
    $statutBadges = [
        'nouvelle' => 'primary',
        'affectee' => 'info',
        'en_cours' => 'warning',
        'resolue' => 'success',
        'annulee' => 'secondary',
    ];
    $urgenceBadges = [
        'faible' => 'secondary',
        'moyen' => 'info',
        'eleve' => 'warning',
        'critique' => 'danger',
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
                <h4 class="fleet-module-title">Assistance</h4>
                <p class="fleet-module-copy">Centralisez les demandes terrain, qualifiez l'urgence et suivez les interventions jusqu'à résolution.</p>
            </div>
            <button class="fleet-module-action" data-bs-toggle="modal" data-bs-target="#addAssistance">
                <i class="fas fa-plus"></i>
                <span>Nouvelle assistance</span>
            </button>
        </div>

        <div class="fleet-stat-grid">
            <div class="fleet-stat-card">
                <span>Total</span>
                <strong>{{ $assistances->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Nouvelles</span>
                <strong>{{ $assistances->where('statut', 'nouvelle')->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>En cours</span>
                <strong>{{ $assistances->where('statut', 'en_cours')->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Critiques</span>
                <strong>{{ $assistances->where('niveau_urgence', 'critique')->count() }}</strong>
            </div>
        </div>

        <div class="fleet-filter-card">
            <form method="GET" action="{{ route('alerte.assistance') }}" class="row g-2 align-items-center">
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
                    <select name="niveau_urgence" class="form-control">
                        <option value="">Toutes les urgences</option>
                        @foreach($urgences as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['niveau_urgence'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 text-md-end">
                    <button class="btn btn-primary" type="submit">Filtrer</button>
                    <a href="{{ route('alerte.assistance') }}" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <div class="card fleet-table-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Liste des demandes d'assistance</h5>
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
                                <th>Lieu</th>
                                <th>Urgence</th>
                                <th>Prestataire</th>
                                <th>Statut</th>
                                <th>Date demande</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assistances as $key => $assistance)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><span class="fleet-vehicle-pill">{{ $assistance->vehicule->matricule ?? 'N/A' }}</span></td>
                                    <td>{{ trim(($assistance->chauffeur->nom ?? '') . ' ' . ($assistance->chauffeur->prenoms ?? '')) ?: 'N/A' }}</td>
                                    <td>{{ $assistance->type_assistance }}</td>
                                    <td><strong>{{ $assistance->titre }}</strong></td>
                                    <td>{{ $assistance->lieu ?? 'N/A' }}</td>
                                    <td><span class="badge bg-{{ $urgenceBadges[$assistance->niveau_urgence] ?? 'secondary' }}">{{ $urgences[$assistance->niveau_urgence] ?? $assistance->niveau_urgence }}</span></td>
                                    <td>{{ $assistance->prestataire_nom ?? 'N/A' }}</td>
                                    <td><span class="badge bg-{{ $statutBadges[$assistance->statut] ?? 'secondary' }}">{{ $statuts[$assistance->statut] ?? $assistance->statut }}</span></td>
                                    <td>{{ $assistance->date_demande ?? 'N/A' }}</td>
                                    <td>
                                        <div class="fleet-actions">
                                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAssistance{{ $assistance->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" action="{{ route('alerte.assistance.destroy', $assistance->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette demande ?')" type="submit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @include('alerte.partials.assistance-form', ['assistance' => $assistance, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'prestataires' => $prestataires, 'type_assistances' => $type_assistances, 'statuts' => $statuts, 'urgences' => $urgences])
                            @empty
                                <tr>
                                    <td colspan="11" class="fleet-empty-state">Aucune demande d'assistance enregistrée pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@include('alerte.partials.assistance-form', ['assistance' => null, 'vehicules' => $vehicules, 'chauffeurs' => $chauffeurs, 'prestataires' => $prestataires, 'type_assistances' => $type_assistances, 'statuts' => $statuts, 'urgences' => $urgences])
@include('layouts.footer')
