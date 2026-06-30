<?php

namespace App\Http\Controllers;

use App\Models\Alert;
use App\Models\Autodoc;
use App\Models\Vehicule;
use App\Models\Chauffeur;
use App\Models\GestionnaireDeFlotte;
use App\Models\Type_de_carburant;
use App\Models\Marque;
use App\Models\Type_de_vehicule;
use App\Models\Type_de_piece;
use App\Models\Type_docauto;
use App\Models\Type_alert;
use App\Models\Entretien;
use App\Models\Assistance;
use App\Models\Garage_flotte;
use App\Models\Reparation;
use App\Models\Carburant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AlertController extends Controller
{
    private function referenceOptions(array $tables)
    {
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $labelColumn = Schema::hasColumn($table, 'libelle') ? 'libelle' : (Schema::hasColumn($table, 'libellé') ? 'libellé' : null);

            if (!$labelColumn) {
                continue;
            }

            return DB::table($table)
                ->select('id', DB::raw("`{$labelColumn}` as libelle"))
                ->orderBy('id', 'desc')
                ->get();
        }

        return collect();
    }

    private function referenceLabel(array $tables, $id): ?string
    {
        if (empty($id)) {
            return null;
        }

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $labelColumn = Schema::hasColumn($table, 'libelle') ? 'libelle' : (Schema::hasColumn($table, 'libellé') ? 'libellé' : null);

            if (!$labelColumn) {
                continue;
            }

            $row = DB::table($table)->where('id', $id)->first();

            if ($row) {
                return $row->{$labelColumn};
            }
        }

        return null;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data['title'] ='Alertes services';
        $data['menu'] ='alertes';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Initialiser la requête de base
        $query = Alert::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->with('type_alert', 'gestionnaire_de_flotte', 'vehicule', 'user');

        // Appliquer les filtres si présents
        if ($request->filled('type_alert_id')) {
            $query->where('type_alert_id', $request->type_alert_id);
        }

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        if ($request->filled('date_debut')) {
            $query->where('date_debut', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->where('date_fin', '<=', $request->date_fin);
        }

        if ($request->filled('statut')) {
            $today = Carbon::today();
            switch ($request->statut) {
                case 'expire':
                    $query->where('date_fin', '<', $today);
                    break;
                case 'proche':
                    $query->whereBetween('date_fin', [$today, $today->copy()->addDays(30)]);
                    break;
                case 'valide':
                    $query->where('date_fin', '>', $today->copy()->addDays(30));
                    break;
            }
        }

        // Récupérer les alertes avec les filtres appliqués
        $data['alertes'] = $query->orderBy('date_fin', 'asc')->get();

        // Données pour les filtres
        $data['type_alertes'] = Type_alert::orderBy('id', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->orderBy('id', 'desc')
            ->get();
        $marqueIds = $data['vehicules']->pluck('marque_id')->filter()->unique()->values();
        $data['marques'] = Marque::whereIn('id', $marqueIds)->orderBy('libelle')->get();

        // Conserver les valeurs des filtres pour la vue
        $data['filters'] = $request->only(['type_alert_id', 'vehicule_id', 'date_debut', 'date_fin', 'statut']);

        return view('alerte.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'vehicule_id' => 'required|exists:vehicules,id',
            'type_alert_id' => 'required|exists:type_alerts,id',
            'date_debut' => 'required',
            'date_fin' => 'required',
            'kilometrage' => 'required_if:type_alert_id,2',
        ]);

        if ($validator->fails()) {
            // Retourner les erreurs de validation
            return back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur existe
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable.");
            return back();
        }

        $vehiculeExists = Vehicule::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
            ->where('id', $request->vehicule_id)
            ->exists();

        if (!$vehiculeExists) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Le véhicule sélectionné est introuvable pour ce compte.");
            return back()->withInput();
        }

        DB::beginTransaction();
        try {
            // Création du véhicule
            $alert = new Alert();
            $alert->vehicule_id = $request->vehicule_id;
            $alert->type_alert_id = $request->type_alert_id;
            $alert->date_debut = $request->date_debut;
            $alert->date_fin = $request->date_fin;
            // Le kilométrage n'est requis que si le type d'alerte est 2
            $alert->kilometrage = $request->type_alert_id == 2 ? $request->kilometrage : null;
            $alert->gestionnaire_de_flotte_id = $user->fleetOwnerId();

            $alert->save();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Alert enregistré avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            // Gestion de l'exception
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de l'enregistrement du Alert : " . $e->getMessage());
            return back();
        }
    }

    /**
     * Affiche le formulaire d'édition d'une alerte.
     */
    public function edit($id)
    {
        $data['title'] = 'Modifier l\'alerte';
        $data['menu'] = 'alertes';
        $data['user'] = Auth::user();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Récupérer l'alerte
        $data['alerte'] = Alert::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->findOrFail($id);

        // Données pour les select
        $data['type_alertes'] = Type_alert::orderBy('id', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->orderBy('id', 'desc')
            ->get();

        return view('alerte.edit', $data);
    }

    /**
     * Met à jour une alerte.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'type_alert_id' => 'required|exists:type_alerts,id',
            'vehicule_id' => 'required|exists:vehicules,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'kilometrage' => 'required_if:type_alert_id,2|nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Récupérer l'alerte
            $alerte = Alert::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                ->findOrFail($id);

            // Mise à jour de l'alerte
            $alerte->type_alert_id = $request->type_alert_id;
            $alerte->vehicule_id = $request->vehicule_id;
            $alerte->date_debut = $request->date_debut;
            $alerte->date_fin = $request->date_fin;
            $alerte->kilometrage = $request->type_alert_id == 2 ? $request->kilometrage : null;
            $alerte->save();

            session()->flash('type', 'alert-success');
            session()->flash('message', "L'alerte a été mise à jour avec succès.");
            return redirect()->route('alerte.index');
        } catch (\Exception $e) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la mise à jour de l'alerte.");
            return back()->withInput();
        }
    }

    public function destroy($id)
    {
        $alert = Alert::find($id);

        if (!$alert) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Alert introuvable.");
            return back();
        }

        DB::beginTransaction();
        try {
            // Supprimer le véhicule
            $alert->delete();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Alert supprimé avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la suppression du alert.");
            return back();
        }
    }

    public function getVehiculesByMarque($marqueId)
    {
        try {
            \Log::info('getVehiculesByMarque appelé avec marqueId: ' . $marqueId);

            // Vérifier si la marque existe
            $marque = Marque::find($marqueId);
            if (!$marque) {
                \Log::error('Marque non trouvée: ' . $marqueId);
                return response()->json(['error' => 'Marque non trouvée'], 404);
            }

            // Récupérer l'utilisateur connecté
            $user = auth()->user();
            if (!$user) {
                \Log::error('Utilisateur non authentifié');
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            // Récupérer les véhicules du gestionnaire connecté
            $vehicules = Vehicule::where('marque_id', $marqueId)
                                ->where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                                ->select('id', 'matricule')
                                ->get();

            \Log::info('Nombre de véhicules trouvés: ' . $vehicules->count());
            \Log::info('Véhicules:', $vehicules->toArray());

            if ($vehicules->isEmpty()) {
                return response()->json([], 200);
            }

            return response()->json($vehicules);
        } catch (\Exception $e) {
            \Log::error('Erreur dans getVehiculesByMarque: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Une erreur est survenue lors de la récupération des véhicules'], 500);
        }
    }

    /**
     * Affiche les alertes d'assurance
     */
    public function assurance(Request $request)
    {
        return $this->typeAlertPage($request, 1, 'Alertes Assurance', 'alerte.assurance');
    }

    /**
     * Affiche les alertes de vidange
     */
    public function vidange(Request $request)
    {
        return $this->typeAlertPage($request, 2, 'Alertes Vidange', 'alerte.vidange');
    }

    /**
     * Affiche les alertes de visite technique
     */
    public function visiteTechnique(Request $request)
    {
        return $this->typeAlertPage($request, 3, 'Alertes Visite Technique', 'alerte.visite-technique');
    }

    /**
     * Affiche les alertes de contrôle technique
     */
    public function controleTechnique(Request $request)
    {
        return $this->typeAlertPage($request, 4, 'Alertes Contrôle Technique', 'alerte.controle-technique');
    }

    private function typeAlertPage(Request $request, int $typeAlertId, string $title, string $view)
    {
        $data['title'] = $title;
        $data['menu'] = 'alertes';
        $data['user'] = Auth::user();

        if (empty($data['user'])) {
            return redirect()->back()->with('error', "L'utilisateur est introuvable.");
        }

        $query = Alert::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->where('type_alert_id', $typeAlertId)
            ->with('type_alert', 'gestionnaire_de_flotte', 'vehicule');

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_debut', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_fin', '<=', $request->date_fin);
        }

        if ($request->filled('statut')) {
            $today = Carbon::today();
            if ($request->statut === 'expire') {
                $query->whereDate('date_fin', '<', $today);
            } elseif ($request->statut === 'proche') {
                $query->whereBetween('date_fin', [$today, $today->copy()->addDays(30)]);
            } elseif ($request->statut === 'valide') {
                $query->whereDate('date_fin', '>', $today->copy()->addDays(30));
            }
        }

        $data['alertes'] = $query->orderBy('date_fin', 'asc')->get();
        $data['type_alertes'] = Type_alert::orderBy('id', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['filters'] = $request->only(['vehicule_id', 'date_debut', 'date_fin', 'statut']);

        return view($view, $data);
    }

    public function entretien(Request $request)
    {
        $data['title'] = 'Entretien';
        $data['menu'] = 'entretiens';
        $data['user'] = Auth::user();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $query = Entretien::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->with('vehicule', 'chauffeur');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        $data['entretiens'] = $query->orderByRaw('date_prevue IS NULL, date_prevue asc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['prestataires'] = Garage_flotte::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->where('statut', 1)->orderBy('name')->get();
        $data['type_entretiens'] = $this->referenceOptions(['type_entretiens']);
        $data['filters'] = $request->only(['statut', 'vehicule_id']);

        return view('alerte.entretien', $data);
    }

    public function storeEntretien(Request $request)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'type_entretien_id' => 'required|integer',
            'titre' => 'required|string|max:160',
            'description' => 'nullable|string',
            'date_prevue' => 'nullable|date',
            'date_realisation' => 'nullable|date',
            'kilometrage' => 'nullable|integer|min:0',
            'cout' => 'nullable|numeric|min:0',
            'prestataire_id' => 'nullable|integer',
            'statut' => 'required|in:planifie,en_cours,realise,annule',
            'commentaire' => 'nullable|string',
        ]);

        $validated['type_entretien'] = $this->referenceLabel(['type_entretiens'], $validated['type_entretien_id']);
        unset($validated['type_entretien_id']);

        if (empty($validated['type_entretien'])) {
            return back()->withErrors(['type_entretien_id' => "Le type d'entretien sélectionné est introuvable."])->withInput();
        }

        $validated['prestataire'] = null;
        if (!empty($validated['prestataire_id'])) {
            $prestataire = Garage_flotte::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                ->where('statut', 1)
                ->find($validated['prestataire_id']);
            $validated['prestataire'] = $prestataire?->name;
        }
        unset($validated['prestataire_id']);

        $validated['gestionnaire_de_flotte_id'] = $user->fleetOwnerId();
        Entretien::create($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Entretien enregistré avec succès.");
        return back();
    }

    public function updateEntretien(Request $request, $id)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'type_entretien_id' => 'required|integer',
            'titre' => 'required|string|max:160',
            'description' => 'nullable|string',
            'date_prevue' => 'nullable|date',
            'date_realisation' => 'nullable|date',
            'kilometrage' => 'nullable|integer|min:0',
            'cout' => 'nullable|numeric|min:0',
            'prestataire_id' => 'nullable|integer',
            'statut' => 'required|in:planifie,en_cours,realise,annule',
            'commentaire' => 'nullable|string',
        ]);

        $validated['type_entretien'] = $this->referenceLabel(['type_entretiens'], $validated['type_entretien_id']);
        unset($validated['type_entretien_id']);

        if (empty($validated['type_entretien'])) {
            return back()->withErrors(['type_entretien_id' => "Le type d'entretien sélectionné est introuvable."])->withInput();
        }

        $validated['prestataire'] = null;
        if (!empty($validated['prestataire_id'])) {
            $prestataire = Garage_flotte::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                ->where('statut', 1)
                ->find($validated['prestataire_id']);
            $validated['prestataire'] = $prestataire?->name;
        }
        unset($validated['prestataire_id']);

        $entretien = Entretien::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $entretien->update($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Entretien mis à jour avec succès.");
        return back();
    }

    public function destroyEntretien($id)
    {
        $user = Auth::user();
        $entretien = Entretien::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $entretien->delete();

        session()->flash('type', 'alert-success');
        session()->flash('message', "Entretien supprimé avec succès.");
        return back();
    }

    public function assistance(Request $request)
    {
        $data['title'] = 'Assistance';
        $data['menu'] = 'assistances';
        $data['user'] = Auth::user();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $query = Assistance::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->with('vehicule', 'chauffeur');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('niveau_urgence')) {
            $query->where('niveau_urgence', $request->niveau_urgence);
        }

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        $data['assistances'] = $query->orderBy('created_at', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['prestataires'] = Garage_flotte::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->where('statut', 1)->orderBy('name')->get();
        $data['type_assistances'] = $this->referenceOptions(['type_assistances', 'types_assistances', 'assistances']);
        $data['filters'] = $request->only(['statut', 'niveau_urgence', 'vehicule_id']);

        return view('alerte.assistance', $data);
    }

    public function storeAssistance(Request $request)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'type_assistance_id' => 'required|integer',
            'titre' => 'required|string|max:160',
            'description' => 'nullable|string',
            'lieu' => 'nullable|string|max:190',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'niveau_urgence' => 'required|in:faible,moyen,eleve,critique',
            'prestataire_id' => 'nullable|integer',
            'date_demande' => 'nullable|date',
            'date_intervention' => 'nullable|date',
            'date_cloture' => 'nullable|date',
            'statut' => 'required|in:nouvelle,affectee,en_cours,resolue,annulee',
            'commentaire' => 'nullable|string',
        ]);

        $validated['type_assistance'] = $this->referenceLabel(['type_assistances', 'types_assistances', 'assistances'], $validated['type_assistance_id']);
        unset($validated['type_assistance_id']);

        if (empty($validated['type_assistance'])) {
            return back()->withErrors(['type_assistance_id' => "Le type d'assistance sélectionné est introuvable."])->withInput();
        }

        $validated['prestataire_nom'] = null;
        if (!empty($validated['prestataire_id'])) {
            $prestataire = Garage_flotte::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                ->where('statut', 1)
                ->find($validated['prestataire_id']);
            $validated['prestataire_nom'] = $prestataire?->name;
        }

        $validated['gestionnaire_de_flotte_id'] = $user->fleetOwnerId();
        $validated['date_demande'] = $validated['date_demande'] ?? now();
        Assistance::create($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Demande d'assistance enregistrée avec succès.");
        return back();
    }

    public function updateAssistance(Request $request, $id)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'type_assistance_id' => 'required|integer',
            'titre' => 'required|string|max:160',
            'description' => 'nullable|string',
            'lieu' => 'nullable|string|max:190',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'niveau_urgence' => 'required|in:faible,moyen,eleve,critique',
            'prestataire_id' => 'nullable|integer',
            'date_demande' => 'nullable|date',
            'date_intervention' => 'nullable|date',
            'date_cloture' => 'nullable|date',
            'statut' => 'required|in:nouvelle,affectee,en_cours,resolue,annulee',
            'commentaire' => 'nullable|string',
        ]);

        $validated['type_assistance'] = $this->referenceLabel(['type_assistances', 'types_assistances', 'assistances'], $validated['type_assistance_id']);
        unset($validated['type_assistance_id']);

        if (empty($validated['type_assistance'])) {
            return back()->withErrors(['type_assistance_id' => "Le type d'assistance sélectionné est introuvable."])->withInput();
        }

        $validated['prestataire_nom'] = null;
        if (!empty($validated['prestataire_id'])) {
            $prestataire = Garage_flotte::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                ->where('statut', 1)
                ->find($validated['prestataire_id']);
            $validated['prestataire_nom'] = $prestataire?->name;
        }

        $assistance = Assistance::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $assistance->update($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Demande d'assistance mise à jour avec succès.");
        return back();
    }

    public function destroyAssistance($id)
    {
        $user = Auth::user();
        $assistance = Assistance::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $assistance->delete();

        session()->flash('type', 'alert-success');
        session()->flash('message', "Demande d'assistance supprimée avec succès.");
        return back();
    }

    public function reparation(Request $request)
    {
        $data['title'] = 'Réparations & Suivi';
        $data['menu'] = 'reparations';
        $data['user'] = Auth::user();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $query = Reparation::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->with('vehicule', 'chauffeur', 'prestataire');

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('validation_financiere')) {
            $query->where('validation_financiere', $request->validation_financiere);
        }

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        $data['reparations'] = $query->orderBy('created_at', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['prestataires'] = Garage_flotte::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->where('statut', 1)->orderBy('name')->get();
        $data['assistances'] = Assistance::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('created_at', 'desc')->get();
        $data['filters'] = $request->only(['statut', 'validation_financiere', 'vehicule_id']);

        return view('alerte.reparation', $data);
    }

    public function storeReparation(Request $request)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $this->validateReparation($request);
        $validated = $this->prepareReparationData($validated, $user->fleetOwnerId());
        $validated['gestionnaire_de_flotte_id'] = $user->fleetOwnerId();

        Reparation::create($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Dossier de réparation enregistré avec succès.");
        return back();
    }

    public function updateReparation(Request $request, $id)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $this->validateReparation($request);
        $validated = $this->prepareReparationData($validated, $user->fleetOwnerId());

        $reparation = Reparation::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $reparation->update($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Dossier de réparation mis à jour avec succès.");
        return back();
    }

    public function destroyReparation($id)
    {
        $user = Auth::user();
        $reparation = Reparation::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $reparation->delete();

        session()->flash('type', 'alert-success');
        session()->flash('message', "Dossier de réparation supprimé avec succès.");
        return back();
    }

    private function validateReparation(Request $request): array
    {
        return $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'prestataire_id' => 'nullable|integer',
            'assistance_id' => 'nullable|integer',
            'titre' => 'required|string|max:160',
            'description_panne' => 'nullable|string',
            'diagnostic' => 'nullable|string',
            'proforma_reference' => 'nullable|string|max:100',
            'proforma_montant' => 'nullable|numeric|min:0',
            'validation_financiere' => 'required|in:en_attente,validee,refusee',
            'date_entree' => 'nullable|date',
            'date_sortie_prevue' => 'nullable|date',
            'date_sortie' => 'nullable|date',
            'cout_final' => 'nullable|numeric|min:0',
            'statut' => 'required|in:nouveau,diagnostic,proforma,validation,reparation,termine,annule',
            'commentaire' => 'nullable|string',
        ]);
    }

    private function prepareReparationData(array $validated, int $userId): array
    {
        $validated['prestataire_nom'] = null;

        if (!empty($validated['prestataire_id'])) {
            $prestataire = Garage_flotte::where('gestionnaire_de_flotte_id', $userId)
                ->where('statut', 1)
                ->find($validated['prestataire_id']);
            $validated['prestataire_nom'] = $prestataire?->name;
        }

        return $validated;
    }

    public function carburant(Request $request)
    {
        $data['title'] = 'Carburant & Consommation';
        $data['menu'] = 'carburants';
        $data['user'] = Auth::user();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $query = Carburant::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->with('vehicule', 'chauffeur', 'type_de_carburant');

        if ($request->filled('vehicule_id')) {
            $query->where('vehicule_id', $request->vehicule_id);
        }

        if ($request->filled('type_de_carburant_id')) {
            $query->where('type_de_carburant_id', $request->type_de_carburant_id);
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('date_approvisionnement', '>=', $request->date_debut);
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('date_approvisionnement', '<=', $request->date_fin);
        }

        $data['carburants'] = $query->orderByRaw('date_approvisionnement IS NULL, date_approvisionnement desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->orderBy('id', 'desc')->get();
        $data['type_de_carburants'] = Type_de_carburant::orderBy('id', 'desc')->get();
        $data['filters'] = $request->only(['vehicule_id', 'type_de_carburant_id', 'date_debut', 'date_fin']);

        return view('alerte.carburant', $data);
    }

    public function storeCarburant(Request $request)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $this->prepareCarburantData($this->validateCarburant($request), $user->fleetOwnerId());
        $validated['gestionnaire_de_flotte_id'] = $user->fleetOwnerId();

        Carburant::create($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Approvisionnement carburant enregistré avec succès.");
        return back();
    }

    public function updateCarburant(Request $request, $id)
    {
        $user = Auth::user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $validated = $this->prepareCarburantData($this->validateCarburant($request), $user->fleetOwnerId());

        $carburant = Carburant::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $carburant->update($validated);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Approvisionnement carburant mis à jour avec succès.");
        return back();
    }

    public function destroyCarburant($id)
    {
        $user = Auth::user();
        $carburant = Carburant::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())->findOrFail($id);
        $carburant->delete();

        session()->flash('type', 'alert-success');
        session()->flash('message', "Approvisionnement carburant supprimé avec succès.");
        return back();
    }

    private function validateCarburant(Request $request): array
    {
        return $request->validate([
            'vehicule_id' => 'required|exists:vehicules,id',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'type_de_carburant_id' => 'required|exists:type_de_carburants,id',
            'date_approvisionnement' => 'required|date',
            'kilometrage' => 'nullable|integer|min:0',
            'quantite_litres' => 'required|numeric|min:0',
            'prix_unitaire' => 'required|numeric|min:0',
            'montant_total' => 'nullable|numeric|min:0',
            'station' => 'nullable|string|max:160',
            'reference' => 'nullable|string|max:100',
            'mode_paiement' => 'required|in:espece,carte,virement,mobile_money,autre',
            'commentaire' => 'nullable|string',
        ]);
    }

    private function prepareCarburantData(array $validated, int $userId): array
    {
        $typeCarburant = Type_de_carburant::find($validated['type_de_carburant_id']);
        $validated['type_carburant'] = $typeCarburant?->libelle;

        if (empty($validated['montant_total'])) {
            $validated['montant_total'] = (float) $validated['quantite_litres'] * (float) $validated['prix_unitaire'];
        }

        return $validated;
    }
}
