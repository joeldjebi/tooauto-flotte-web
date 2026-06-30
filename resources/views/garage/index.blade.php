@include('layouts.header')
@include('layouts.menu')
@include('alerte.partials.module-styles')

<div class="page-wrapper fleet-module-page">
    <div class="content">
        @include('layouts.fileariane')

        @if(session()->has("message"))
            <div style="padding: 10px" class="alert {{session()->get('type')}}">{{ session()->get('message') }}</div>
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
                <span class="fleet-module-kicker">Réseau</span>
                <h4 class="fleet-module-title">Prestataires</h4>
                <p class="fleet-module-copy">Centralisez les garages, ateliers et partenaires qui interviennent sur les véhicules de la flotte.</p>
            </div>
            <button class="fleet-module-action" data-bs-toggle="modal" data-bs-target="#addPrestataire">
                <i class="fas fa-plus"></i>
                <span>Nouveau prestataire</span>
            </button>
        </div>

        <div class="fleet-stat-grid">
            <div class="fleet-stat-card">
                <span>Total</span>
                <strong>{{ $garage_flottes->total() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Affichés</span>
                <strong>{{ $garage_flottes->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Contacts</span>
                <strong>{{ $garage_flottes->filter(fn($item) => !empty($item->contact))->count() }}</strong>
            </div>
            <div class="fleet-stat-card">
                <span>Localisés</span>
                <strong>{{ $garage_flottes->filter(fn($item) => !empty($item->adresse_map))->count() }}</strong>
            </div>
        </div>

        <div class="fleet-filter-card">
            <div class="row g-2 align-items-center">
                <div class="col-md-4">
                    <input type="text" id="filterNom" class="form-control" placeholder="Filtrer par nom...">
                </div>
                <div class="col-md-4">
                    <input type="text" id="filterContact" class="form-control" placeholder="Filtrer par contact...">
                </div>
                <div class="col-md-4">
                    <input type="text" id="searchGarage" class="form-control" placeholder="Recherche générale...">
                </div>
            </div>
        </div>

        <div class="card fleet-table-card">
            <div class="card-header">
                <h5 class="card-title mb-0">Liste des prestataires</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle fleet-table" id="garageTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Prestataire</th>
                                <th>Adresse</th>
                                <th>Carte</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($garage_flottes as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td>{{ $item->adresse ?? 'N/A' }}</td>
                                    <td>
                                        @if(!empty($item->adresse_map))
                                            <a class="btn btn-outline-dark btn-sm" href="{{ $item->adresse_map }}" target="_blank">
                                                <i class="fas fa-map-marker-alt"></i> Ouvrir
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->contact ?? 'N/A' }}</td>
                                    <td>
                                        <div class="fleet-actions">
                                            <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#edit{{ $item->id }}" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('destroy.garage', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer" onclick="return confirm('Supprimer ce prestataire ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @include('garage.partials.modals', ['item' => $item])
                            @empty
                                <tr>
                                    <td colspan="6" class="fleet-empty-state">Aucun prestataire enregistré pour le moment.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $garage_flottes->links('pagination::bootstrap-4') }}
        </div>
    </div>

    <div class="modal fade" id="addPrestataire" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content fleet-module-modal">
                <form action="{{ route('store.garage') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter un prestataire</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="fleet-form-section mb-0">
                            <span class="fleet-form-section-title">Informations</span>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Nom du prestataire *</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Adresse</label>
                                    <input type="text" class="form-control" name="adresse">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Adresse map</label>
                                    <input type="text" class="form-control" name="adresse_map">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Numéro de téléphone</label>
                                    <input type="text" class="form-control" name="contact">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-submit">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    function normalize(str) {
        return (str || '').toLowerCase().normalize('NFD').replace(/\p{Diacritic}/gu, '');
    }

    function filterGarages() {
        const search = normalize(document.getElementById('searchGarage').value);
        const nom = normalize(document.getElementById('filterNom').value);
        const contact = normalize(document.getElementById('filterContact').value);
        const rows = document.querySelectorAll('#garageTable tbody tr');

        rows.forEach(row => {
            const text = normalize(row.textContent);
            const nomText = normalize(row.querySelector('td:nth-child(2)')?.textContent || '');
            const contactText = normalize(row.querySelector('td:nth-child(5)')?.textContent || '');
            let show = true;

            if (search && !text.includes(search)) show = false;
            if (nom && !nomText.includes(nom)) show = false;
            if (contact && !contactText.includes(contact)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    document.getElementById('searchGarage').addEventListener('keyup', filterGarages);
    document.getElementById('filterNom').addEventListener('keyup', filterGarages);
    document.getElementById('filterContact').addEventListener('keyup', filterGarages);
</script>
