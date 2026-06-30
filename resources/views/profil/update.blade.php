@include('layouts.header')
@include('layouts.menu')

@php
    $profilePhoto = $profile_photo_url ?? asset('assets/img/profiles/avatar-01.jpg');
    $roleLabel = 'Utilisateur';

    if (!empty($user)) {
        if (method_exists($user, 'isMainAdmin') && $user->isMainAdmin()) {
            $roleLabel = 'Super Admin';
        } elseif (!empty($user->fleet_role_id) && \Illuminate\Support\Facades\Schema::hasTable('fleet_roles')) {
            $roleLabel = $user->fleet_role->libelle ?? 'User admin';
        } elseif (in_array((string) ($user->role ?? ''), ['02', '2'], true)) {
            $roleLabel = 'User admin';
        }
    }
@endphp

<style>
    .profile-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .profile-hero,
    .profile-card,
    .profile-side-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(19, 13, 13, 0.05);
    }

    .profile-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 20px;
        position: relative;
    }

    .profile-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .profile-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .profile-title {
        color: #130d0d;
        font-size: 26px;
        font-weight: 950;
        margin: 4px 0 6px;
    }

    .profile-copy {
        color: #64748b;
        margin: 0;
    }

    .profile-card {
        padding: 22px;
    }

    .profile-section-title {
        color: #130d0d;
        font-size: 17px;
        font-weight: 950;
        margin-bottom: 18px;
    }

    .profile-field {
        margin-bottom: 16px;
    }

    .profile-field label {
        color: #130d0d;
        font-size: 13px;
        font-weight: 900;
        margin-bottom: 7px;
    }

    .profile-field .form-control {
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        min-height: 44px;
    }

    .profile-side-card {
        padding: 22px;
        position: sticky;
        top: 90px;
    }

    .profile-avatar {
        border: 4px solid #efc242;
        border-radius: 50%;
        height: 110px;
        object-fit: cover;
        width: 110px;
    }

    .profile-name {
        color: #130d0d;
        font-size: 18px;
        font-weight: 950;
        margin: 12px 0 4px;
    }

    .profile-role {
        background: #130d0d;
        border-radius: 999px;
        color: #ffffff;
        display: inline-flex;
        font-size: 12px;
        font-weight: 900;
        padding: 7px 11px;
    }

    .profile-meta {
        border-top: 1px solid #e5eaf1;
        margin-top: 18px;
        padding-top: 16px;
    }

    .profile-meta-row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .profile-meta-row span {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
    }

    .profile-meta-row strong {
        color: #130d0d;
        font-size: 13px;
        font-weight: 900;
        text-align: right;
    }

    .profile-btn {
        align-items: center;
        background: #efc242;
        border: 1px solid #efc242;
        border-radius: 8px;
        color: #130d0d;
        display: inline-flex;
        font-weight: 950;
        gap: 8px;
        min-height: 42px;
        padding: 10px 14px;
    }

    .profile-btn:hover {
        background: #d8ab28;
        border-color: #d8ab28;
        color: #130d0d;
    }

    @media (max-width: 991px) {
        .profile-side-card {
            position: static;
        }
    }
</style>

<div class="page-wrapper profile-page">
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

        <div class="profile-hero">
            <div>
                <span class="profile-kicker">Paramètres</span>
                <h1 class="profile-title">Mon profil</h1>
                <p class="profile-copy">Mettez à jour vos informations de compte backoffice.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="profile-card">
                    <h2 class="profile-section-title">Informations personnelles</h2>
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="profile-field">
                                    <label for="nom">Nom</label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom', $user->nom ?? '') }}" required>
                                    @error('nom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-field">
                                    <label for="prenoms">Prénoms</label>
                                    <input type="text" class="form-control @error('prenom') is-invalid @enderror" id="prenoms" name="prenom" value="{{ old('prenom', $user->prenoms ?? '') }}" required>
                                    @error('prenom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-field">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" required>
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="profile-field">
                                    <label for="telephone">Téléphone</label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror" id="telephone" name="telephone" value="{{ old('telephone', $user->mobile ?? '') }}" required>
                                    @error('telephone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="profile-field">
                                    <label for="adresse">Adresse</label>
                                    <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="3">{{ old('adresse', $user->adresse ?? '') }}</textarea>
                                    @error('adresse')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="profile-field">
                                    <label for="photo">Photo de profil</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                    @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="profile-btn">
                            <i class="fas fa-save"></i>
                            Mettre à jour le profil
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <aside class="profile-side-card text-center">
                    <img src="{{ $profilePhoto }}" alt="Photo de profil" class="profile-avatar" onerror="this.onerror=null;this.src='{{ asset('assets/img/profiles/avatar-01.jpg') }}'">
                    <h3 class="profile-name">{{ $user->prenoms ?? '' }} {{ $user->nom ?? '' }}</h3>
                    <span class="profile-role">{{ $roleLabel }}</span>

                    <div class="profile-meta text-start">
                        <div class="profile-meta-row">
                            <span>Mobile</span>
                            <strong>{{ $user->mobile ?? 'N/A' }}</strong>
                        </div>
                        <div class="profile-meta-row">
                            <span>Email</span>
                            <strong>{{ $user->email ?? 'N/A' }}</strong>
                        </div>
                        <div class="profile-meta-row">
                            <span>Compte</span>
                            <strong>{{ method_exists($user, 'isMainAdmin') && $user->isMainAdmin() ? 'Principal' : 'Secondaire' }}</strong>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')
