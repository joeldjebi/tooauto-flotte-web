@include('layouts.header')
@include('layouts.menu')

<style>
    .admin-user-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .admin-user-hero,
    .admin-user-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(19, 13, 13, 0.05);
    }

    .admin-user-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 20px;
        position: relative;
    }

    .admin-user-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .admin-user-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .admin-user-title {
        color: #130d0d;
        font-size: 26px;
        font-weight: 950;
        margin: 4px 0 6px;
    }

    .admin-user-copy {
        color: #64748b;
        margin: 0;
    }

    .admin-user-btn {
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

    .admin-user-btn.secondary {
        background: #ffffff;
        border-color: #dbe3ee;
        color: #334155;
    }

    .admin-user-table {
        margin: 0;
    }

    .admin-user-table thead th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .admin-user-name {
        color: #130d0d;
        display: block;
        font-weight: 950;
    }

    .admin-user-muted {
        color: #64748b;
        display: block;
        font-size: 12px;
        margin-top: 2px;
    }

    .admin-user-pill {
        border-radius: 999px;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        padding: 6px 10px;
    }

    .admin-user-pill.success {
        background: #ecfdf5;
        color: #047857;
    }

    .admin-user-pill.danger {
        background: #fff1f2;
        color: #be123c;
    }

    .admin-user-actions {
        display: inline-flex;
        gap: 6px;
    }

    .admin-user-icon {
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

    .admin-user-icon:hover {
        background: #130d0d;
        border-color: #130d0d;
        color: #ffffff;
    }

    .admin-user-icon.danger:hover {
        background: #dc2626;
        border-color: #dc2626;
    }
</style>

<div class="page-wrapper admin-user-page">
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

        <div class="admin-user-hero">
            <div>
                <span class="admin-user-kicker">Backoffice</span>
                <h1 class="admin-user-title">Users admin</h1>
                <p class="admin-user-copy">Créez les comptes backoffice et rattachez chaque user à un rôle.</p>
            </div>
            <button class="admin-user-btn" type="button" data-bs-toggle="modal" data-bs-target="#addAdminUser">
                <i class="fas fa-plus"></i>
                Ajouter un user admin
            </button>
        </div>

        @if (!empty($adminUsers) && count($adminUsers) > 0)
            <div class="admin-user-card">
                <div class="table-responsive">
                    <table class="table table-hover align-middle admin-user-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($adminUsers as $key => $adminUser)
                                <tr>
                                    <td>{{ $adminUsers->firstItem() + $key }}</td>
                                    <td>
                                        <span class="admin-user-name">{{ $adminUser->nom }} {{ $adminUser->prenoms }}</span>
                                        <span class="admin-user-muted">Créé le {{ $adminUser->created_at ? \Carbon\Carbon::parse($adminUser->created_at)->format('d/m/Y') : 'N/A' }}</span>
                                    </td>
                                    <td>{{ $adminUser->mobile }}</td>
                                    <td>{{ $adminUser->email ?: 'N/A' }}</td>
                                    <td>{{ $adminUser->fleet_role->libelle ?? 'N/A' }}</td>
                                    <td>
                                        <span class="admin-user-pill {{ (int) ($adminUser->statut ?? 1) === 1 ? 'success' : 'danger' }}">
                                            {{ (int) ($adminUser->statut ?? 1) === 1 ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="admin-user-actions">
                                            <button class="admin-user-icon" type="button" data-bs-toggle="modal" data-bs-target="#editAdminUser{{ $adminUser->id }}" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('admin-users.destroy', $adminUser->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="admin-user-icon danger" type="submit" onclick="return confirm('Supprimer ce user admin ?')" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editAdminUser{{ $adminUser->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background:#130d0d;color:#fff;">
                                                <h5 class="modal-title">Modifier le user admin</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
                                            </div>
                                            <form action="{{ route('admin-users.update', $adminUser->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    @include('admin-users.partials.form', ['adminUser' => $adminUser])
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="admin-user-btn secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <button class="admin-user-btn" type="submit">Enregistrer</button>
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
                    {{ $adminUsers->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @else
            <div class="admin-user-card p-4 text-center text-muted">Aucun user admin enregistré pour le moment.</div>
        @endif
    </div>
</div>

<div class="modal fade" id="addAdminUser" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background:#130d0d;color:#fff;">
                <h5 class="modal-title">Ajouter un user admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1);"></button>
            </div>
            <form action="{{ route('admin-users.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @include('admin-users.partials.form', ['adminUser' => null])
                </div>
                <div class="modal-footer">
                    <button type="button" class="admin-user-btn secondary" data-bs-dismiss="modal">Fermer</button>
                    <button class="admin-user-btn" type="submit">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@include('layouts.footer')
