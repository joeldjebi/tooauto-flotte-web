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
            $vehicleTotal = !empty($vehicule_concessionnaires) ? count($vehicule_concessionnaires) : 0;
            $vehiclePrices = collect($vehicule_concessionnaires ?? [])->pluck('prix')->filter();
            $minPrice = $vehiclePrices->count() ? $vehiclePrices->min() : null;
            $maxPrice = $vehiclePrices->count() ? $vehiclePrices->max() : null;
            $brandTotal = collect($vehicule_concessionnaires ?? [])->map(function ($vehicle) {
                return $vehicle->marque->libelle ?? null;
            })->filter()->unique()->count();
        @endphp

        <div class="dealer-detail-hero">
            <div>
                <span class="dealer-detail-kicker">Concessionnaire</span>
                <h4 class="dealer-detail-title">Véhicules disponibles</h4>
                <p class="dealer-detail-copy">{{ $concessionnaire->name ?? 'Concessionnaire' }} - consultez les véhicules proposés.</p>
            </div>
            <div class="dealer-detail-actions">
                <a href="{{ route('index.concessionnaire') }}" class="dealer-detail-btn">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour</span>
                </a>
                @if(!empty($concessionnaire))
                    <a href="{{ route('rdv.concessionnaire', ['id' => $concessionnaire->id]) }}" class="dealer-detail-btn primary">
                        <i class="fas fa-history"></i>
                        <span>Historique RDV</span>
                    </a>
                @endif
            </div>
        </div>

        <div class="dealer-detail-stats">
            <div class="dealer-detail-stat">
                <span>Véhicules</span>
                <strong>{{ $vehicleTotal }}</strong>
            </div>
            <div class="dealer-detail-stat">
                <span>Marques</span>
                <strong>{{ $brandTotal }}</strong>
            </div>
            <div class="dealer-detail-stat">
                <span>Prix minimum</span>
                <strong>{{ $minPrice ? number_format($minPrice, 0, ',', ' ') : 'N/A' }}</strong>
            </div>
            <div class="dealer-detail-stat">
                <span>Prix maximum</span>
                <strong>{{ $maxPrice ? number_format($maxPrice, 0, ',', ' ') : 'N/A' }}</strong>
            </div>
        </div>

        <div class="dealer-detail-tools">
            <label class="dealer-detail-search" for="dealerVehicleSearch">
                <i class="fas fa-search"></i>
                <input type="search" id="dealerVehicleSearch" placeholder="Rechercher un véhicule, une marque, un modèle...">
            </label>
            <span class="dealer-detail-chip">{{ $vehicleTotal }} véhicule(s) disponible(s)</span>
        </div>

        @if (!empty($vehicule_concessionnaires) && count($vehicule_concessionnaires) > 0)
            <div class="card dealer-detail-table-card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Liste des véhicules</h5>
                    <p class="dealer-detail-mini mb-0">Catalogue du concessionnaire</p>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle dealer-detail-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Photo</th>
                                    <th>Véhicule</th>
                                    <th>Marque</th>
                                    <th>Modèle</th>
                                    <th>Prix</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($vehicule_concessionnaires as $key => $item)
                                    @php
                                        $photos = is_array($item->photos) ? $item->photos : (json_decode($item->photos, true) ?: []);
                                        $firstPhoto = $photos[0] ?? null;
                                        $photoUrl = $firstPhoto ? config('app.concessionnaire_server_url') . $firstPhoto : asset('assets/img/default-car.png');
                                        $description = trim(strip_tags(html_entity_decode($item->description ?? '')));
                                    @endphp
                                    <tr class="dealer-vehicle-row" data-search="{{ strtolower(($item->name ?? '') . ' ' . ($item->marque->libelle ?? '') . ' ' . ($item->modele ?? '') . ' ' . ($item->prix ?? '')) }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td><img src="{{ $photoUrl }}" alt="Photo véhicule" class="dealer-detail-thumb"></td>
                                        <td>
                                            <span class="dealer-detail-name">{{ $item->name }}</span>
                                            <span class="dealer-detail-muted">{{ $description ? \Illuminate\Support\Str::limit($description, 58) : 'Description non renseignée' }}</span>
                                        </td>
                                        <td>{{ $item->marque->libelle ?? 'N/A' }}</td>
                                        <td>{{ $item->modele ?? 'N/A' }}</td>
                                        <td><span class="dealer-detail-price">{{ number_format($item->prix, 0, ',', ' ') }} F CFA</span></td>
                                        <td>
                                            <button class="dealer-detail-action primary" data-bs-toggle="modal" data-bs-target="#show{{ $item->id }}">
                                                <i class="fas fa-eye"></i>
                                                <span>Détails</span>
                                            </button>
                                        </td>
                                    </tr>

                                    <div class="modal fade" id="show{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                            <div class="modal-content dealer-detail-modal">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">{{ $item->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-lg-7">
                                                            <div class="dealer-detail-modal-section">
                                                                <div id="vehicleCarousel{{ $item->id }}" class="carousel slide" data-bs-ride="carousel">
                                                                    <div class="carousel-inner dealer-detail-gallery">
                                                                        @forelse($photos as $index => $photo)
                                                                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                                                <img src="{{ config('app.concessionnaire_server_url') }}{{ $photo }}" class="d-block w-100" alt="Photo véhicule">
                                                                            </div>
                                                                        @empty
                                                                            <div class="carousel-item active">
                                                                                <img src="{{ asset('assets/img/default-car.png') }}" class="d-block w-100" alt="Photo véhicule">
                                                                            </div>
                                                                        @endforelse
                                                                    </div>
                                                                    @if(count($photos) > 1)
                                                                        <button class="carousel-control-prev" type="button" data-bs-target="#vehicleCarousel{{ $item->id }}" data-bs-slide="prev">
                                                                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                                            <span class="visually-hidden">Précédent</span>
                                                                        </button>
                                                                        <button class="carousel-control-next" type="button" data-bs-target="#vehicleCarousel{{ $item->id }}" data-bs-slide="next">
                                                                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                                            <span class="visually-hidden">Suivant</span>
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-5">
                                                            <div class="dealer-detail-modal-section h-100">
                                                                <h6 style="color:#130d0d;font-weight:900;">Détails véhicule</h6>
                                                                <div class="dealer-detail-grid">
                                                                    <div class="dealer-detail-field">
                                                                        <span>Marque</span>
                                                                        <strong>{{ $item->marque->libelle ?? 'N/A' }}</strong>
                                                                    </div>
                                                                    <div class="dealer-detail-field">
                                                                        <span>Modèle</span>
                                                                        <strong>{{ $item->modele ?? 'N/A' }}</strong>
                                                                    </div>
                                                                    <div class="dealer-detail-field">
                                                                        <span>Couleur</span>
                                                                        <strong>{{ $item->couleur_vehicule->libelle ?? 'N/A' }}</strong>
                                                                    </div>
                                                                    <div class="dealer-detail-field">
                                                                        <span>Prix</span>
                                                                        <strong>{{ number_format($item->prix, 0, ',', ' ') }} F CFA</strong>
                                                                    </div>
                                                                </div>
                                                                <hr>
                                                                <h6 style="color:#130d0d;font-weight:900;">Description</h6>
                                                                <p class="mb-0">{{ $description ?: 'Aucune description disponible.' }}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="dealer-detail-empty dealer-detail-no-result" id="dealerVehicleNoResult">Aucun véhicule ne correspond à votre recherche.</div>
        @else
            <div class="dealer-detail-empty">Aucun véhicule enregistré pour ce concessionnaire.</div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var searchInput = document.getElementById('dealerVehicleSearch');
        var rows = document.querySelectorAll('.dealer-vehicle-row');
        var noResult = document.getElementById('dealerVehicleNoResult');

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
