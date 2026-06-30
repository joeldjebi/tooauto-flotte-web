@include('layouts.header')
@include('layouts.menu')

<style>
    .password-page {
        background: #f6f8fb;
        min-height: calc(100vh - 70px);
    }

    .password-hero,
    .password-card,
    .password-side-card {
        background: #ffffff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 14px 34px rgba(19, 13, 13, 0.05);
    }

    .password-hero {
        align-items: center;
        display: flex;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        overflow: hidden;
        padding: 20px;
        position: relative;
    }

    .password-hero::before {
        background: #efc242;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        top: 0;
        width: 5px;
    }

    .password-kicker {
        color: #64748b;
        display: block;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .password-title {
        color: #130d0d;
        font-size: 26px;
        font-weight: 950;
        margin: 4px 0 6px;
    }

    .password-copy {
        color: #64748b;
        margin: 0;
    }

    .password-card,
    .password-side-card {
        padding: 22px;
    }

    .password-section-title {
        color: #130d0d;
        font-size: 17px;
        font-weight: 950;
        margin-bottom: 18px;
    }

    .password-field {
        margin-bottom: 16px;
    }

    .password-field label {
        color: #130d0d;
        font-size: 13px;
        font-weight: 900;
        margin-bottom: 7px;
    }

    .password-input {
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        display: flex;
        min-height: 46px;
        overflow: hidden;
    }

    .password-input .form-control {
        border: 0;
        min-height: 46px;
    }

    .password-toggle {
        align-items: center;
        background: #f8fafc;
        border: 0;
        border-left: 1px solid #e5eaf1;
        color: #64748b;
        display: inline-flex;
        justify-content: center;
        width: 48px;
    }

    .password-btn {
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

    .password-btn:hover {
        background: #d8ab28;
        border-color: #d8ab28;
        color: #130d0d;
    }

    .password-rule-list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .password-rule-list li {
        align-items: flex-start;
        border-bottom: 1px solid #edf2f7;
        color: #334155;
        display: flex;
        font-size: 13px;
        gap: 10px;
        padding: 11px 0;
    }

    .password-rule-list li:last-child {
        border-bottom: 0;
    }

    .password-rule-list i {
        color: #efc242;
        margin-top: 2px;
    }

    .password-alert {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 8px;
        color: #92400e;
        font-size: 13px;
        margin-bottom: 18px;
        padding: 12px 14px;
    }
</style>

<div class="page-wrapper password-page">
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

        <div class="password-hero">
            <div>
                <span class="password-kicker">Sécurité</span>
                <h1 class="password-title">Mot de passe</h1>
                <p class="password-copy">Renforcez l'accès à votre backoffice avec un mot de passe robuste.</p>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="password-card">
                    <h2 class="password-section-title">Changer le mot de passe</h2>
                    <div class="password-alert">
                        <i class="fas fa-info-circle me-1"></i>
                        Après validation, utilisez le nouveau mot de passe à la prochaine connexion.
                    </div>

                    <form action="{{ route('password.update') }}" method="POST">
                        @csrf

                        <div class="password-field">
                            <label for="current_password">Mot de passe actuel</label>
                            <div class="password-input">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password" required autocomplete="current-password">
                                <button class="password-toggle" type="button" data-toggle-password="current_password" aria-label="Afficher le mot de passe actuel">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="password-field">
                                    <label for="new_password">Nouveau mot de passe</label>
                                    <div class="password-input">
                                        <input type="password" class="form-control @error('new_password') is-invalid @enderror" id="new_password" name="new_password" required autocomplete="new-password">
                                        <button class="password-toggle" type="button" data-toggle-password="new_password" aria-label="Afficher le nouveau mot de passe">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('new_password')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="password-field">
                                    <label for="new_password_confirmation">Confirmation</label>
                                    <div class="password-input">
                                        <input type="password" class="form-control @error('new_password_confirmation') is-invalid @enderror" id="new_password_confirmation" name="new_password_confirmation" required autocomplete="new-password">
                                        <button class="password-toggle" type="button" data-toggle-password="new_password_confirmation" aria-label="Afficher la confirmation">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    @error('new_password_confirmation')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="password-btn">
                            <i class="fas fa-key"></i>
                            Changer le mot de passe
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <aside class="password-side-card">
                    <h2 class="password-section-title">Règles de sécurité</h2>
                    <ul class="password-rule-list">
                        <li><i class="fas fa-check-circle"></i><span>Au moins 8 caractères.</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Une majuscule et une minuscule.</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Un chiffre et un caractère spécial.</span></li>
                        <li><i class="fas fa-check-circle"></i><span>Évitez les mots de passe déjà utilisés ailleurs.</span></li>
                    </ul>
                </aside>
            </div>
        </div>
    </div>
</div>

@include('layouts.footer')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(this.dataset.togglePassword);
                var icon = this.querySelector('i');

                if (!input) {
                    return;
                }

                input.type = input.type === 'password' ? 'text' : 'password';
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        });
    });
</script>
