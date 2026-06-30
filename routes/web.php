<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChauffeurController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\AutodocController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\FleetRoleController;
use App\Http\Controllers\AdminUserController;


Route::group(['middleware' => ['auth']], function (){

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');

    Route::get('/profil', [DashboardController::class, 'profil'])->name('profil.index');
    Route::post('/profil/update', [DashboardController::class, 'updateProfil'])->name('profil.update');
    Route::get('/password', [DashboardController::class, 'password'])->name('password.index');
    Route::post('/password/update', [DashboardController::class, 'updatepassword'])->name('password.update');
    Route::get('/documentation', [DashboardController::class, 'documentation'])->name('documentation.index');

    Route::get('/index/annonce', [DashboardController::class, 'indexPieceAuto'])->name('index.annonce');
    Route::get('/add/annonce', [DashboardController::class, 'addPieceAuto'])->name('add.annonce');
    Route::get('/edit/annonce-{id}', [DashboardController::class, 'editPieceAuto'])->name('edit.annonce');
    Route::post('/store/annonce', [DashboardController::class, 'storePieceAuto'])->name('store.annonce');
    Route::post('/update/annonce/{id}', [DashboardController::class, 'updatePieceAuto'])->name('update.annonce');
    Route::delete('/destroy/annonce/{id}', [DashboardController::class, 'destroyPieceAuto'])->name('destroy.annonce');

    Route::get('/index/garage', [DashboardController::class, 'indexGarage'])->name('index.garage');
    Route::post('/store/garage/', [DashboardController::class, 'storeGarage'])->name('store.garage');
    Route::post('/update/garage/{id}', [DashboardController::class, 'updateGarage'])->name('update.garage');
    Route::delete('/destroy/garage/{id}', [DashboardController::class, 'destroyGarage'])->name('destroy.garage');

    Route::get('/index/article', [DashboardController::class, 'indexArticle'])->name('index.article');
    Route::post('/store/article/', [DashboardController::class, 'storeArticle'])->name('store.article');
    Route::post('/update/article/{id}', [DashboardController::class, 'updateArticle'])->name('update.article');
    Route::delete('/destroy/article/{id}', [DashboardController::class, 'destroyArticle'])->name('destroy.article');

    Route::get('/index/concessionnaire', [DashboardController::class, 'indexConcessionnaire'])->name('index.concessionnaire');
    Route::get('/index/offre-concessionnaire', [DashboardController::class, 'indexOffreConcessionnaire'])->name('index.offre-concessionnaire');
    Route::get('/rdv/concessionnaire-{id}', [DashboardController::class, 'indexConcessionnaireHistoriqueRdv'])->name('rdv.concessionnaire');
    Route::get('/index/concessionnaire-{id}', [DashboardController::class, 'indexConcessionnaireVehicule'])->name('index.concessionnaire-vehicule');
    Route::post('/store/concessionnaire-rdv', [DashboardController::class, 'storeRdvConcessionnaire'])->name('store.concessionnaire-rdv');
    Route::post('/concessionnaire-demande', [DashboardController::class, 'storeDemandeConcessionnaire'])->name('store.concessionnaire-demande');
    Route::get('/destroy/concessionnaire-rdv/{id}', [DashboardController::class, 'destroyRdvConcessionnaire'])->name('destroy.concessionnaire-rdv');

    Route::put('/update/concessionnaire-demande/{id}', [DashboardController::class, 'updateDemandeConcessionnaire'])->name('update.concessionnaire-demande');
    Route::get('/destroy/concessionnaire-demande/{id}', [DashboardController::class, 'destroyDemandeConcessionnaire'])->name('destroy.concessionnaire-demande');
    Route::get('/index/demande-concessionnaire', [DashboardController::class, 'indexDemandeConcessionnaire'])->name('index.demande-concessionnaire');

    Route::get('/liste-des-chauffeurs', [ChauffeurController::class, 'index'])->name('chauffeur.index');
    Route::post('/store-chauffeurs', [ChauffeurController::class, 'store'])->name('chauffeur.store');
    Route::post('/update-chauffeurs/{id}', [ChauffeurController::class, 'update'])->name('chauffeur.update');
    Route::delete('chauffeurs/{id}', [ChauffeurController::class, 'destroy'])->name('chauffeur.destroy');

    Route::get('/liste-des-vehicules', [VehiculeController::class, 'index'])->name('vehicule.index');
    Route::get('/add-vehicules', [VehiculeController::class, 'addVehicule'])->name('vehicule.add');
    Route::get('/edit-vehicules/{id}', [VehiculeController::class, 'editVehicule'])->name('vehicule.edit');
    Route::post('/store-vehicules', [VehiculeController::class, 'store'])->name('vehicule.store');
    Route::post('/update-vehicules/{id}', [VehiculeController::class, 'update'])->name('vehicule.update');
    Route::delete('vehicules/{id}', [VehiculeController::class, 'destroy'])->name('vehicule.destroy');
    Route::post('/vehicules/import-excel', [VehiculeController::class, 'importExcel'])->name('vehicule.importExcel');

    Route::get('/chauffeurs-by-fonction/{fonction_id}', [VehiculeController::class, 'getChauffeursByFonction'])->name('chauffeurs.byfonction');

    Route::get('/liste-des-autodocs', [AutodocController::class, 'index'])->name('autodoc.index');
    Route::get('/add-autodocs', [AutodocController::class, 'add'])->name('autodoc.add');
    Route::post('/store-autodocs', [AutodocController::class, 'store'])->name('autodoc.store');
    Route::post('/update-autodocs/{id}', [AutodocController::class, 'update'])->name('autodoc.update');
    Route::delete('autodocs/{id}', [AutodocController::class, 'destroy'])->name('autodoc.destroy');

    // Redirection de l'ancienne URL vers la nouvelle
    Route::get('/liste-des-alertes', function () {
        return redirect()->route('alerte.index');
    });

    // Routes pour les alertes
    Route::prefix('alerte')->group(function () {
        Route::get('/vehicules/{marqueId}', [AlertController::class, 'getVehiculesByMarque']);
        Route::get('/entretien', [AlertController::class, 'entretien'])->name('alerte.entretien');
        Route::post('/entretien', [AlertController::class, 'storeEntretien'])->name('alerte.entretien.store');
        Route::put('/entretien/{id}', [AlertController::class, 'updateEntretien'])->name('alerte.entretien.update');
        Route::delete('/entretien/{id}', [AlertController::class, 'destroyEntretien'])->name('alerte.entretien.destroy');
        Route::get('/assistance', [AlertController::class, 'assistance'])->name('alerte.assistance');
        Route::post('/assistance', [AlertController::class, 'storeAssistance'])->name('alerte.assistance.store');
        Route::put('/assistance/{id}', [AlertController::class, 'updateAssistance'])->name('alerte.assistance.update');
        Route::delete('/assistance/{id}', [AlertController::class, 'destroyAssistance'])->name('alerte.assistance.destroy');
        Route::get('/reparation', [AlertController::class, 'reparation'])->name('alerte.reparation');
        Route::post('/reparation', [AlertController::class, 'storeReparation'])->name('alerte.reparation.store');
        Route::put('/reparation/{id}', [AlertController::class, 'updateReparation'])->name('alerte.reparation.update');
        Route::delete('/reparation/{id}', [AlertController::class, 'destroyReparation'])->name('alerte.reparation.destroy');
        Route::get('/carburant', [AlertController::class, 'carburant'])->name('alerte.carburant');
        Route::post('/carburant', [AlertController::class, 'storeCarburant'])->name('alerte.carburant.store');
        Route::put('/carburant/{id}', [AlertController::class, 'updateCarburant'])->name('alerte.carburant.update');
        Route::delete('/carburant/{id}', [AlertController::class, 'destroyCarburant'])->name('alerte.carburant.destroy');
        Route::get('/', [AlertController::class, 'index'])->name('alerte.index');
        Route::post('/', [AlertController::class, 'store'])->name('alerte.store');
        Route::get('/edit-{id}', [AlertController::class, 'edit'])->name('alerte.edit');
        Route::put('/{id}', [AlertController::class, 'update'])->name('alerte.update');
        Route::delete('/{id}', [AlertController::class, 'destroy'])->name('alerte.destroy');
        Route::get('/assurance', [AlertController::class, 'assurance'])->name('alerte.assurance');
        Route::get('/vidange', [AlertController::class, 'vidange'])->name('alerte.vidange');
        Route::get('/visite-technique', [AlertController::class, 'visiteTechnique'])->name('alerte.visite-technique');
        Route::get('/controle-technique', [AlertController::class, 'controleTechnique'])->name('alerte.controle-technique');
        Route::get('/vehicules/{marqueId}', [AlertController::class, 'getVehiculesByMarque'])->name('alerte.vehicules.by.marque');
    });

    Route::get('/liste-des-fonctions', [DashboardController::class, 'indexFonction'])->name('fonction.index');
    Route::post('/store-fonctions', [DashboardController::class, 'storeFonction'])->name('fonction.store');
    Route::post('/update-fonctions/{id}', [DashboardController::class, 'updateFonction'])->name('fonction.update');
    Route::delete('fonctions/{id}', [DashboardController::class, 'destroyFonction'])->name('fonction.destroy');

    Route::get('/liste-des-roles', [FleetRoleController::class, 'index'])->name('roles.index');
    Route::post('/store-roles', [FleetRoleController::class, 'store'])->name('roles.store');
    Route::post('/update-roles/{id}', [FleetRoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [FleetRoleController::class, 'destroy'])->name('roles.destroy');

    Route::get('/liste-des-admin-users', [AdminUserController::class, 'index'])->name('admin-users.index');
    Route::post('/store-admin-users', [AdminUserController::class, 'store'])->name('admin-users.store');
    Route::post('/update-admin-users/{id}', [AdminUserController::class, 'update'])->name('admin-users.update');
    Route::delete('/admin-users/{id}', [AdminUserController::class, 'destroy'])->name('admin-users.destroy');

    // Routes pour les véhicules
    Route::prefix('vehicule')->group(function () {
        Route::get('/', [VehiculeController::class, 'index'])->name('vehicule.index');
        Route::get('/add', [VehiculeController::class, 'addVehicule'])->name('vehicule.add');
        Route::post('/', [VehiculeController::class, 'store'])->name('vehicule.store');
        Route::get('/edit-{id}', [VehiculeController::class, 'editVehicule'])->name('vehicule.edit');
        Route::put('/{id}', [VehiculeController::class, 'update'])->name('vehicule.update');
        Route::delete('/{id}', [VehiculeController::class, 'destroy'])->name('vehicule.destroy');
        Route::post('/import-excel', [VehiculeController::class, 'importExcel'])->name('vehicule.importExcel');
        Route::get('/download-template', [VehiculeController::class, 'downloadTemplate'])->name('vehicule.downloadTemplate');
        Route::get('/chauffeurs-by-fonction/{fonction_id}', [VehiculeController::class, 'getChauffeursByFonction'])->name('vehicule.chauffeurs.by.fonction');
    });

    Route::get('/annonce-sent', [DashboardController::class, 'annonceSent'])->name('annonce.sent');

});

Route::get('/', [AuthController::class, 'showlogin'])->name('login');

Route::get('/login', [AuthController::class, 'showlogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('logins');

Route::get('/register', [AuthController::class, 'showregister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('registers');

// Mot de passe oublié - Affichage du formulaire
Route::get('/password/forget', [AuthController::class, 'showpasswordforget'])->name('password.forget');

// Mot de passe oublié - Envoi OTP
Route::post('/password/forget', [AuthController::class, 'postPasswordForget'])->name('post-password.forget');

// Saisie OTP
Route::get('/otp', function() { return view('auth.otp'); })->name('auth.otp');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('auth.otp.verify');

// Réinitialisation du mot de passe
Route::get('/password/reset', function() { return view('auth.password_reset'); })->name('password.reset.form');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
