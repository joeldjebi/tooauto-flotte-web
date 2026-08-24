
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
		<meta name="description" content="POS - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern,  html5, responsive">
		<meta name="author" content="Dreamguys - Bootstrap Admin Template">
		<meta name="robots" content="noindex, nofollow">
		<title>Tableau de bord</title>

		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">

		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">

		<!-- Datetimepicker CSS -->
		<link rel="stylesheet" href="../assets/css/bootstrap-datetimepicker.min.css">

		<!-- animation CSS -->
        <link rel="stylesheet" href="../assets/css/animate.css">

		<!-- Select2 CSS -->
		<link rel="stylesheet" href="../assets/plugins/select2/css/select2.min.css">

		<!-- Datatable CSS -->
		<link rel="stylesheet" href="../assets/css/dataTables.bootstrap4.min.css">

        <!-- Fontawesome CSS -->
		<link rel="stylesheet" href="../assets/plugins/fontawesome/css/fontawesome.min.css">
		<link rel="stylesheet" href="../assets/plugins/fontawesome/css/all.min.css">

		<!-- Feather CSS -->
		<link rel="stylesheet" href="../assets/plugins/icons/feather/feather.css">

		<!-- Wizard CSS -->
		<link rel="stylesheet" href="../assets/plugins/twitter-bootstrap-wizard/form-wizard.css">

		<!-- Main CSS -->
        <link rel="stylesheet" href="../assets/css/style.css">
		<link rel="stylesheet" href="../assets/css/main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>


		<link rel="stylesheet"
			href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/css/bootstrap-select.css" />

		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.1/js/bootstrap-select.min.js"></script>
		<link rel="stylesheet" href="https://cdn.datatables.net/2.1.2/css/dataTables.dataTables.css">
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.1.0/css/buttons.dataTables.css">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
		<style>
			div#global-loader {
				display: none;
			}

			#global-loader.tooauto-crud-loader {
				align-items: center;
				background: rgba(15, 23, 42, 0.32);
				backdrop-filter: blur(3px);
				inset: 0;
				justify-content: center;
				position: fixed;
				z-index: 99999;
			}

			#global-loader.tooauto-crud-loader.is-active {
				display: flex;
			}

			.tooauto-loader-panel {
				align-items: center;
				background: #ffffff;
				border: 1px solid rgba(226, 232, 240, 0.95);
				border-radius: 8px;
				box-shadow: 0 20px 50px rgba(15, 23, 42, 0.18);
				display: flex;
				gap: 14px;
				min-width: 260px;
				padding: 18px 20px;
			}

			.tooauto-loader-spinner {
				animation: tooautoSpin 0.75s linear infinite;
				border: 3px solid #e2e8f0;
				border-top-color: #efc242;
				border-radius: 50%;
				height: 34px;
				width: 34px;
			}

			.tooauto-loader-title {
				color: #111827;
				display: block;
				font-size: 14px;
				font-weight: 800;
				line-height: 1.15;
			}

			.tooauto-loader-copy {
				color: #64748b;
				display: block;
				font-size: 12px;
				font-weight: 700;
				margin-top: 3px;
			}

			.tooauto-submit-loading {
				cursor: wait !important;
				opacity: 0.82;
				pointer-events: none;
			}

			.tooauto-submit-spinner {
				animation: tooautoSpin 0.75s linear infinite;
				border: 2px solid currentColor;
				border-right-color: transparent;
				border-radius: 50%;
				display: inline-block;
				height: 1em;
				margin-right: 8px;
				vertical-align: -0.12em;
				width: 1em;
			}

			@keyframes tooautoSpin {
				to {
					transform: rotate(360deg);
				}
			}
		</style>

	</head>
	<body>
		@php
			$connectedUser = auth()->user();
			$connectedRoleLabel = 'Utilisateur';

			if ($connectedUser) {
				if (method_exists($connectedUser, 'isMainAdmin') && $connectedUser->isMainAdmin()) {
					$connectedRoleLabel = 'Super Admin';
				} elseif (!empty($connectedUser->fleet_role_id) && \Illuminate\Support\Facades\Schema::hasTable('fleet_roles')) {
					$connectedRoleLabel = $connectedUser->fleet_role->libelle ?? 'User admin';
				} elseif (in_array((string) ($connectedUser->role ?? ''), ['02', '2'], true)) {
					$connectedRoleLabel = 'User admin';
				}
			}
		@endphp
		<div id="global-loader" class="tooauto-crud-loader" aria-hidden="true">
			<div class="tooauto-loader-panel" role="status" aria-live="polite">
				<span class="tooauto-loader-spinner" aria-hidden="true"></span>
				<span>
					<span class="tooauto-loader-title">Traitement en cours</span>
					<span class="tooauto-loader-copy">Veuillez patienter quelques instants</span>
				</span>
			</div>
		</div>
		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Header -->
			<div class="header">

				<!-- Logo -->
				 <div class="header-left active">
					<a href="index.html" class="logo logo-normal">
						<img src="../assets/img/logo.png"  alt="">
					</a>
					<a href="index.html" class="logo logo-white">
						<img src="../assets/img/logo-white.png"  alt="">
					</a>
					<a href="index.html" class="logo-small">
						<img src="../assets/img/logo-small.png"  alt="">
					</a>
					<a id="toggle_btn" href="javascript:void(0);">
						<i data-feather="chevrons-left" class="feather-16"></i>
					</a>
				</div>
				<!-- /Logo -->

				<a id="mobile_btn" class="mobile_btn" href="#sidebar">
					<span class="bar-icon">
						<span></span>
						<span></span>
						<span></span>
					</span>
				</a>

				<!-- Header Menu -->
				<ul class="nav user-menu">

					<!-- Search -->
					<li class="nav-item nav-searchinputs">
						<div class="top-nav-search">
						</div>
					</li>
					{{-- @if ($daysRemaining > 0)
                        {{ $daysRemaining }} jour(s)
                    @else
                        <span class="text-danger">Abonnement expiré</span>
                    @endif --}}
					<!-- /Search -->
					<li class="nav-item dropdown has-arrow main-drop">
						<a href="javascript:void(0);" class="dropdown-toggle nav-link userset" data-bs-toggle="dropdown">
							<span class="user-info">
								@if (!empty($etablissement))
									<span class="user-letter">
										<img src="/etablissement/logo/{{ $etablissement->logo }}" alt="" class="img-fluid">
									</span>
								@endif
								@if (!empty($connectedUser))
								<span class="user-detail">
									<span class="user-name">{{ $connectedUser->prenoms }} {{ $connectedUser->nom }}</span>
									<span class="user-role">{{ $connectedRoleLabel }}</span>
								</span>
								@endif
							</span>
						</a>
						<div class="dropdown-menu menu-drop-user">
							<div class="profilename">
								<div class="profileset">
									@if (!empty($etablissement))
										<span class="user-img">
											<img src="/etablissement/logo/{{ $etablissement->logo }}" alt="">
											<span class="status online"></span>
										</span>
									@endif
									<div class="profilesets">
										@if (!empty($connectedUser))
											<h6>{{ $connectedUser->prenoms }} {{ $connectedUser->nom }}</h6>
											<h5>{{ $connectedRoleLabel }}</h5>
										@endif
									</div>
								</div>
								<hr class="m-0">

								<hr class="m-0">
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									@csrf
								</form>
								<a class="dropdown-item logout pb-0" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
									<img src="../assets/img/icons/log-out.svg" class="me-2" alt="img">Déconexion</a>
							</div>
						</div>
					</li>
				</ul>
				<!-- /Header Menu -->

				<!-- Mobile Menu -->
				<div class="dropdown mobile-user-menu">
					<a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v mt-2"></i></a>
					<div class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" href="{{ route('profil.index') }}"><i class="me-2"  data-feather="user"></i>Mon Profil</a>
						<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
							@csrf
						</form>
						<a class="dropdown-item logout pb-0" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><img src="../assets/img/icons/log-out.svg" class="me-2" alt="img">Déconnexion</a>
					</div>
				</div>
				<!-- /Mobile Menu -->
			</div>
			<!-- Header -->
