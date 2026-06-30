<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <title>Créer un compte - FLOTTE PRO</title>

        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/img/favicon.png') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/fontawesome.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/plugins/fontawesome/css/all.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

        <style>
            :root {
                --auth-gold: #efc242;
                --auth-ink: #130d0d;
                --auth-muted: #64748b;
                --auth-line: #e5eaf1;
            }

            body.account-page {
                background: #f6f8fb;
                color: var(--auth-ink);
                min-height: 100vh;
            }

            .auth-shell {
                align-items: stretch;
                display: grid;
                grid-template-columns: minmax(0, 0.95fr) minmax(420px, 560px);
                min-height: 100vh;
            }

            .auth-panel {
                align-items: center;
                background: var(--auth-ink);
                color: #ffffff;
                display: flex;
                padding: 48px;
                position: relative;
                overflow: hidden;
            }

            .auth-panel::before {
                background: var(--auth-gold);
                bottom: 0;
                content: "";
                left: 0;
                position: absolute;
                top: 0;
                width: 8px;
            }

            .auth-logo-mark {
                align-items: center;
                background: #ffffff;
                border-radius: 8px;
                display: inline-flex;
                height: 74px;
                justify-content: center;
                margin-bottom: 28px;
                padding: 12px;
                width: 74px;
            }

            .auth-logo-mark img {
                max-height: 50px;
                max-width: 50px;
            }

            .auth-panel h1 {
                color: #ffffff;
                font-size: 40px;
                font-weight: 950;
                line-height: 1.08;
                margin: 0 0 14px;
            }

            .auth-panel p {
                color: #d1d5db;
                font-size: 16px;
                line-height: 1.65;
                margin: 0;
                max-width: 540px;
            }

            .auth-card-wrap {
                align-items: center;
                display: flex;
                justify-content: center;
                padding: 32px;
            }

            .auth-card {
                background: #ffffff;
                border: 1px solid var(--auth-line);
                border-radius: 8px;
                box-shadow: 0 20px 50px rgba(19, 13, 13, 0.08);
                max-width: 560px;
                padding: 34px;
                width: 100%;
            }

            .auth-kicker {
                color: var(--auth-muted);
                display: block;
                font-size: 12px;
                font-weight: 900;
                text-transform: uppercase;
            }

            .auth-title {
                color: var(--auth-ink);
                font-size: 28px;
                font-weight: 950;
                margin: 6px 0 6px;
            }

            .auth-subtitle {
                color: var(--auth-muted);
                font-size: 14px;
                margin: 0 0 22px;
            }

            .auth-grid {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .auth-field {
                margin-bottom: 14px;
            }

            .auth-grid .auth-field {
                margin-bottom: 0;
            }

            .auth-field.full {
                grid-column: 1 / -1;
            }

            .auth-field label {
                color: var(--auth-ink);
                font-size: 13px;
                font-weight: 900;
                margin-bottom: 7px;
            }

            .auth-input-group {
                align-items: center;
                border: 1px solid #dbe3ee;
                border-radius: 8px;
                display: flex;
                min-height: 48px;
                overflow: hidden;
            }

            .auth-prefix,
            .auth-icon-btn {
                align-items: center;
                background: #f8fafc;
                border: 0;
                color: var(--auth-muted);
                display: inline-flex;
                height: 48px;
                justify-content: center;
                padding: 0 14px;
            }

            .auth-input-group input {
                border: 0;
                color: var(--auth-ink);
                flex: 1;
                height: 48px;
                outline: 0;
                padding: 0 14px;
                width: 100%;
            }

            .auth-check {
                align-items: flex-start;
                color: var(--auth-muted);
                display: flex;
                font-size: 13px;
                gap: 9px;
                line-height: 1.45;
                margin: 18px 0 20px;
            }

            .auth-link {
                color: var(--auth-ink);
                font-weight: 900;
            }

            .auth-submit {
                background: var(--auth-gold);
                border: 1px solid var(--auth-gold);
                border-radius: 8px;
                color: var(--auth-ink);
                font-weight: 950;
                min-height: 48px;
                width: 100%;
            }

            .auth-submit:disabled {
                cursor: not-allowed;
                opacity: 0.55;
            }

            .auth-submit:hover:not(:disabled) {
                background: #d8ab28;
                border-color: #d8ab28;
                color: var(--auth-ink);
            }

            .auth-switch {
                color: var(--auth-muted);
                font-size: 14px;
                margin: 22px 0 0;
                text-align: center;
            }

            .auth-alert ul {
                margin-bottom: 0;
                padding-left: 18px;
            }

            @media (max-width: 991px) {
                .auth-shell {
                    grid-template-columns: 1fr;
                }

                .auth-panel {
                    min-height: 280px;
                    padding: 32px;
                }

                .auth-panel h1 {
                    font-size: 32px;
                }

                .auth-card-wrap {
                    padding: 24px 16px;
                }
            }

            @media (max-width: 575px) {
                .auth-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>
    <body class="account-page">
        <main class="auth-shell">
            <section class="auth-panel">
                <div>
                    <span class="auth-logo-mark">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="TOOauto">
                    </span>
                    <h1>Créer votre espace flotte</h1>
                    <p>Activez votre compte principal pour gérer vos véhicules, vos alertes, vos rôles admin et vos opérations depuis FLOTTE PRO.</p>
                </div>
            </section>

            <section class="auth-card-wrap">
                <form class="auth-card" action="{{ route('registers') }}" method="POST">
                    @csrf

                    <span class="auth-kicker">Inscription</span>
                    <h2 class="auth-title">Créer un compte</h2>
                    <p class="auth-subtitle">Ce compte sera le compte principal de votre backoffice.</p>

                    @if(session()->has("message"))
                        <div class="alert {{ session()->get('type') }} auth-alert">{{ session()->get('message') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger auth-alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="auth-grid">
                        <div class="auth-field">
                            <label for="nom">Nom</label>
                            <div class="auth-input-group">
                                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required autocomplete="family-name">
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="prenoms">Prénoms</label>
                            <div class="auth-input-group">
                                <input type="text" id="prenoms" name="prenoms" value="{{ old('prenoms') }}" required autocomplete="given-name">
                            </div>
                        </div>

                        <div class="auth-field full">
                            <label for="mobile">Numéro de téléphone</label>
                            <div class="auth-input-group">
                                <span class="auth-prefix">+225</span>
                                <input type="text" id="mobile" name="mobile" value="{{ old('mobile') }}" required autocomplete="username">
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="password">Mot de passe</label>
                            <div class="auth-input-group">
                                <input type="password" id="password" name="password" required autocomplete="new-password">
                                <button class="auth-icon-btn" type="button" data-toggle-password="password" aria-label="Afficher le mot de passe">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="auth-field">
                            <label for="password_confirmation">Confirmation</label>
                            <div class="auth-input-group">
                                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                                <button class="auth-icon-btn" type="button" data-toggle-password="password_confirmation" aria-label="Afficher la confirmation">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <label class="auth-check">
                        <input type="checkbox" name="cgu" id="cgu-checkbox" required>
                        <span>J'accepte les <a href="#" class="auth-link">conditions générales et la confidentialité</a>.</span>
                    </label>

                    <button class="btn auth-submit" type="submit" id="submit-button" disabled>Créer le compte</button>

                    <p class="auth-switch">Vous avez déjà un compte ? <a class="auth-link" href="{{ route('login') }}">Se connecter</a></p>
                </form>
            </section>
        </main>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        var input = document.getElementById(this.dataset.togglePassword);
                        var icon = this.querySelector('i');

                        if (!input) return;

                        input.type = input.type === 'password' ? 'text' : 'password';
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    });
                });

                var checkbox = document.getElementById('cgu-checkbox');
                var submitButton = document.getElementById('submit-button');

                if (checkbox && submitButton) {
                    checkbox.addEventListener('change', function () {
                        submitButton.disabled = !checkbox.checked;
                    });
                }
            });
        </script>
    </body>
</html>
