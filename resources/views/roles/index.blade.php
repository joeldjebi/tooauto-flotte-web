@include('layouts.header')
@include('layouts.menu')

<style>
    .role-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
        padding-bottom: 24px;
    }

    .role-hero,
    .role-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(19, 13, 13, 0.05);
    }

    .role-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 20px;
        position: relative;
    }

    .role-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .role-kicker {
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .role-title {
        color: #130d0d;
        font-size: 26px;
        font-weight: 950;
        margin: 4px 0 6px;
    }

    .role-copy {
        color: #64748b;
        margin: 0;
    }

    .role-btn {
        align-items: center;
        background: #efc242;
        border: 1px solid #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 900;
        gap: 8px;
        min-height: 42px;
        padding: 10px 14px;
    }

    .role-btn.secondary {
        background: #ffffff;
        border-color: #dbe3ee;
        color: #334155;
    }

    .role-table {
        margin: 0;
    }

    .role-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .role-name {
        color: #130d0d;
        display: block;
        font-weight: 950;
    }

    .role-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 2px;
    }

    .role-pill {
        background: #f8fafc;
        border: 1px solid #e5eaf1;
        border-radius: 999px;
        color: #334155;
        display: inline-flex;
        font-size: 12px;
        font-weight: 850;
        margin: 2px;
        padding: 6px 10px;
    }

    .role-status {
        border-radius: 999px;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        padding: 6px 10px;
    }

    .role-status.active {
        background: #ecfdf5;
        color: #047857;
    }

    .role-status.inactive {
        background: #fff1f2;
        color: #be123c;
    }

    .role-actions {
        display: inline-flex;
        gap: 6px;
    }

    .role-icon {
        align-items: center;
        background: #ffffff;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        color: #334155;
        display: inline-flex;
        height: 34px;
        justify-content: center;
        width: 34px;
    }

    .role-icon:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .role-icon.danger:hover {
        background: #dc2626;
        border-color: #dc2626;
    }

    .role-feature-box {
        background: #f8fafc;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        max-height: 360px;
        overflow: auto;
        padding: 14px;
    }

    .role-feature-group + .role-feature-group {
        border-top: 1px solid #e5eaf1;
        margin-top: 12px;
        padding-top: 12px;
    }

    .role-feature-title {
        color: #130d0d;
        display: block;
        font-size: 13px;
        font-weight: 950;
        margin-bottom: 8px;
    }

    .role-feature-grid {
        display: grid;
        gap: 8px;
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .role-feature-check {
        align-items: center;
        background: #ffffff;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        display: flex;
        gap: 8px;
        min-height: 38px;
        padding: 8px 10px;
    }

    @media (max-width: 767px) {
        .role-hero {
            align-items: flex-start;
            flex-direction: column;
        }

        .role-feature-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="page-wrapper role-page">
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

        <div class="role-hero">
            <div>
                <span class="role-kicker">Accès utilisateurs</span>
                <h1 class="role-title">Rôles & permissions</h1>
                <p class="role-copy">Créez un rôle, rattachez-lui les menus autorisés, puis sélectionnez ce rôle lors de la création d'un utilisateur.</p>
            </div>
            <button class="role-btn" type="button" data-bs-toggle="modal" data-bs-target="#addRole">
                <i class="fas fa-plus"></i>
                Ajouter un rôle
            </button>
        </div>

        @if (!empty($roles) && count($roles) > 0)
            <div class="role-card">
                <div class="table-responsive">
                    <table class="table role-table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Rôle</th>
                                <th>Permissions</th>
                                <th>Utilisateurs</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $key => $role)
                                @php $selectedFeatures = $role->feature_keys ?? []; @endphp
                                <tr>
                                    <td>{{ $roles->firstItem() + $key }}</td>
                                    <td>
                                        <span class="role-name">{{ $role->libelle }}</span>
                                        <span class="role-muted">{{ $role->description ?: 'Aucune description' }}</span>
                                    </td>
                                    <td>
                                        @forelse(collect($role->feature_labels ?? [])->take(4) as $featureLabel)
                                            <span class="role-pill">{{ $featureLabel }}</span>
                                        @empty
                                            <span class="role-muted">Aucune permission</span>
                                        @endforelse
                                        @if(count($role->feature_labels ?? []) > 4)
                                            <span class="role-pill">+{{ count($role->feature_labels ?? []) - 4 }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $role->admin_users_count }}</td>
                                    <td>
                                        <span class="role-status {{ $role->statut == 1 ? 'active' : 'inactive' }}">
                                            {{ $role->statut == 1 ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="role-actions">
                                            <button class="role-icon" type="button" data-bs-toggle="modal" data-bs-target="#editRole{{ $role->id }}" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="role-icon danger" type="submit" onclick="return confirm('Supprimer ce rôle ?')" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editRole{{ $role->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background:#130d0d;color:#fff;">
                                                <h5 class="modal-title">Modifier le rôle</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                                            </div>
                                            <form action="{{ route('roles.update', $role->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    @include('roles.partials.form', ['role' => $role, 'selectedFeatures' => $selectedFeatures])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="role-btn secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <button class="role-btn" type="submit">Enregistrer</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center p-3">
                    {{ $roles->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="role-card p-4 text-center text-muted">Aucun rôle enregistré pour le moment.</div>
        @endif
    </div>
</div>

<div class="modal fade" id="addRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#130d0d;color:#fff;">
                <h5 class="modal-title">Ajouter un rôle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
            </div>
            <form action="{{ route('roles.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('roles.partials.form', ['role' => null, 'selectedFeatures' => old('menu_features', [])])
                </div>
                <div class="modal-footer">
                    <button type="button" class="role-btn secondary" data-bs-dismiss="modal">Fermer</button>
                    <button class="role-btn" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')
