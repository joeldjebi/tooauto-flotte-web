<?php

namespace App\Http\Controllers;

use App\Models\Vehicule;
use App\Models\Chauffeur;
use App\Models\Couleur_vehicule;
use App\Models\Fonction;
use App\Models\GestionnaireDeFlotte;
use App\Models\Type_de_carburant;
use App\Models\Marque;
use App\Models\Type_de_vehicule;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class VehiculeController extends Controller
{
    public function __construct(
        protected WasabiService $wasabiService
    ) {
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['title'] ='Liste des véhicules';
        $data['menu'] ='véhicules';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Récupérer les établissements triés par ID décroissant
        $data['vehicules'] = Vehicule::where(['gestionnaire_de_flotte_id' => $this->currentFleetOwnerId()])
        ->with('chauffeur','chauffeur.fonction', 'type_de_vehicule', 'marque', 'type_de_carburant', 'gestionnaire_de_flotte')
        ->orderBy('id', 'desc')->paginate(15);

        $data['vehicules']->getCollection()->transform(function ($vehicule) {
            $vehicule->photo_url_map = $this->resolveVehiclePhotoUrlMap($vehicule);

            return $vehicule;
        });

        // dd($data['vehicules']);

        $data['type_de_vehicules'] = Type_de_vehicule::orderBy('id', 'desc')->get();
        $data['type_de_carburants'] = Type_de_carburant::orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['couleur_vehicules'] = Couleur_vehicule::orderBy('id', 'desc')->get();

        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
        ->where('statut', 1)
        ->get();

        $data['fonctions'] = Fonction::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->get();

        // dd($data['type_de_carburants']);

        return view('vehicule.index', $data);

    }
    /**
     * Display a listing of the resource.
     */
    public function addVehicule()
    {
        $data['title'] ='Ajouter un véhicule';
        $data['menu'] ='véhicules';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $data['type_de_vehicules'] = Type_de_vehicule::orderBy('id', 'desc')->get();
        $data['type_de_carburants'] = Type_de_carburant::orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['couleur_vehicules'] = Couleur_vehicule::orderBy('id', 'desc')->get();

        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
        ->where('statut', 1)
        ->get();

        $data['fonctions'] = Fonction::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->get();

        // dd($data['type_de_carburants']);

        return view('vehicule.add', $data);

    }

    /**
     * Display a listing of the resource.
     */
    public function editVehicule($id)
    {
        $data['title'] = 'Modifier un véhicule';
        $data['menu'] = 'véhicules';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        // Récupérer le véhicule à modifier avec sa relation chauffeur
        $data['vehicule'] = Vehicule::with('chauffeur')
            ->where('id', $id)
            ->where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->first();

        if (!$data['vehicule']) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        // Récupérer les données nécessaires pour les selects
        $data['type_de_vehicules'] = Type_de_vehicule::orderBy('id', 'desc')->get();
        $data['type_de_carburants'] = Type_de_carburant::orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['couleur_vehicules'] = Couleur_vehicule::orderBy('id', 'desc')->get();
        $data['fonctions'] = Fonction::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->get();
        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
            ->where('statut', '!=', 2)
            ->get();

        // Récupérer les photos existantes
        $data['imagePaths'] = [];
        if ($data['vehicule']->photos) {
            $data['imagePaths'] = json_decode($data['vehicule']->photos, true) ?? [];
        }
        $data['vehicule']->photo_url_map = $this->resolveVehiclePhotoUrlMap($data['vehicule']);

        return view('vehicule.edit', $data);
    }

    protected function resolveVehiclePhotoUrlMap(Vehicule $vehicule)
    {
        $photos = json_decode($vehicule->photos, true) ?? [];
        $urls = [];

        foreach ($photos as $photo) {
            $urls[$photo] = $this->resolveVehiclePhotoUrl($photo, $vehicule);
        }

        return $urls;
    }

    protected function resolveVehiclePhotoUrl($photo, Vehicule $vehicule)
    {
        if (empty($photo)) {
            return asset('assets/img/default-car.png');
        }

        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            return $photo;
        }

        if (file_exists(public_path($photo))) {
            return asset($photo);
        }

        if (($vehicule->provenance_by ?? null) != 1 && ($vehicule->provenance ?? null) !== 'wasabi') {
            return 'https://api-usager.tooauto.com/' . ltrim($photo, '/');
        }

        return $this->wasabiService->temporaryUrl($photo) ?: ($this->wasabiService->publicUrl($photo) ?: asset('assets/img/default-car.png'));
    }

    public function getChauffeursByFonction($fonction_id)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Non authentifié'], 401);
            }

            $chauffeurs = Chauffeur::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                ->where('fonction_id', $fonction_id)
                ->where('statut', 1)
                ->orderBy('nom')
                ->get(['id', 'nom', 'prenoms', 'mobile'])
                ->map(function ($chauffeur) {
                    return [
                        'id' => $chauffeur->id,
                        'nom' => (string) $chauffeur->nom,
                        'prenoms' => (string) $chauffeur->prenoms,
                        'mobile' => (string) $chauffeur->mobile,
                    ];
                })
                ->values();

            return response()->json($chauffeurs);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Erreur interne: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|unique:vehicules',
            'carte_grise' => 'required|string|unique:vehicules',
            'photos' => 'required|array|size:4', // Vérifie que 4 fichiers sont fournis
            'photos.*' => 'file|image|max:2048', // Chaque fichier doit être une image de max 2 Mo
            'type_de_vehicule_id' => 'required|exists:type_de_vehicules,id',
            'marque_id' => 'required|exists:marques,id',
            'type_de_carburant_id' => 'required|exists:type_de_carburants,id',
            'modele' => 'required|string|max:50',
            'chauffeur_id' => 'nullable|exists:chauffeurs,id',
            'couleur_vehicule_id' => 'required|exists:couleur_vehicules,id',
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

        DB::beginTransaction();
        $uploadedPhotos = [];
        try {
            // Création du véhicule
            $vehicule = new Vehicule();
            $vehicule->matricule = $request->matricule;
            $vehicule->carte_grise = $request->carte_grise;
            $vehicule->type_de_vehicule_id = $request->type_de_vehicule_id;
            $vehicule->marque_id = $request->marque_id;
            $vehicule->type_de_carburant_id = $request->type_de_carburant_id;
            $vehicule->couleur_vehicule_id = $request->couleur_vehicule_id;
            $vehicule->user_id = $request->chauffeur_id;
            $vehicule->modele = $request->modele;
            $vehicule->gestionnaire_de_flotte_id = $user->fleetOwnerId();
            $vehicule->provenance_by = 1;
            $vehicule->provenance = 'wasabi';

            $photos = $request->file('photos');
            $photosPaths = [];

            if (!empty($photos)) {
                $count = 0;

                foreach ($photos as $image) {
                    if ($count >= 4) {
                        break;
                    }

                    $path = $this->wasabiService->uploadVehicleImage($image);
                    $photosPaths[] = $path;
                    $uploadedPhotos[] = $path;

                    $count++;
                }

                // Stocker les chemins des photos en format JSON
                $vehicule->photos = json_encode($photosPaths);
            }

            $vehicule->save();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Véhicule enregistré avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedPhotos as $photo) {
                $this->wasabiService->deleteFile($photo);
            }

            // Gestion de l'exception
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de l'enregistrement du véhicule : " . $e->getMessage());
            return back();
        }
    }


    public function update(Request $request, $id)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'matricule' => 'required|string|unique:vehicules,matricule,' . $id,
            'carte_grise' => 'required|string|unique:vehicules,carte_grise,' . $id,
            'photos' => 'nullable|array|max:4', // Maximum 4 nouvelles photos
            'photos.*' => 'file|image|max:2048', // Chaque fichier doit être une image de max 2 Mo
            'type_de_vehicule_id' => 'required|exists:type_de_vehicules,id',
            'marque_id' => 'required|exists:marques,id',
            'type_de_carburant_id' => 'required|exists:type_de_carburants,id',
            'couleur_vehicule_id' => 'required|exists:couleur_vehicules,id',
            'modele' => 'required|string|max:50',
            'chauffeur_id' => 'required|exists:chauffeurs,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validation personnalisée : vérifier qu'il y a au moins une photo au total
        $vehicule = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);
        if (!$vehicule) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        $currentPhotos = json_decode($vehicule->photos, true) ?? [];
        $deletedPhotos = $request->deleted_photos ? json_decode($request->deleted_photos, true) ?? [] : [];
        $newPhotos = $request->hasFile('photos') ? $request->file('photos') : [];
        
        // Calculer le nombre total de photos après modification
        $remainingPhotos = array_filter($currentPhotos, function($photo) use ($deletedPhotos) {
            return !in_array($photo, $deletedPhotos);
        });
        $totalPhotos = count($remainingPhotos) + count($newPhotos);
        
        if ($totalPhotos === 0) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Le véhicule doit avoir au moins une photo.");
            return back();
        }
        
        if ($totalPhotos > 4) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Un véhicule ne peut pas avoir plus de 4 photos au total.");
            return back();
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur existe
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable.");
            return back();
        }

        DB::beginTransaction();
        $uploadedPhotos = [];
        try {
            // Mettre à jour les champs du véhicule
            $vehicule->matricule = $request->matricule;
            $vehicule->carte_grise = $request->carte_grise;
            $vehicule->type_de_vehicule_id = $request->type_de_vehicule_id;
            $vehicule->marque_id = $request->marque_id;
            $vehicule->type_de_carburant_id = $request->type_de_carburant_id;
            $vehicule->couleur_vehicule_id = $request->couleur_vehicule_id;
            $vehicule->chauffeur_id = $request->chauffeur_id;
            $vehicule->user_id = $request->chauffeur_id;
            $vehicule->modele = $request->modele;
            $vehicule->provenance_by = 1;
            $vehicule->provenance = 'wasabi';

            // Gestion des photos
            $currentPhotos = json_decode($vehicule->photos, true) ?? [];
            $finalPhotos = $currentPhotos;

            // Supprimer les photos marquées pour suppression
            if ($request->deleted_photos) {
                $deletedPhotos = json_decode($request->deleted_photos, true) ?? [];
                foreach ($deletedPhotos as $deletedPhoto) {
                    if (file_exists(public_path($deletedPhoto))) {
                        unlink(public_path($deletedPhoto));
                    } else {
                        $this->wasabiService->deleteFile($deletedPhoto);
                    }
                    // Retirer de la liste des photos
                    $finalPhotos = array_filter($finalPhotos, function($photo) use ($deletedPhoto) {
                        return $photo !== $deletedPhoto;
                    });
                }
            }

            // Ajouter les nouvelles photos si elles existent
            if ($request->hasFile('photos')) {
                $photos = $request->file('photos');

                // Ajouter les nouvelles photos
                foreach ($photos as $image) {
                    $path = $this->wasabiService->uploadVehicleImage($image);
                    $finalPhotos[] = $path;
                    $uploadedPhotos[] = $path;
                }
            }

            // Mettre à jour le champ photos avec la liste finale
            $vehicule->photos = json_encode(array_values($finalPhotos));

            $vehicule->save();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Véhicule mis à jour avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedPhotos as $photo) {
                $this->wasabiService->deleteFile($photo);
            }

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la mise à jour du véhicule : " . $e->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        $vehicule = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);

        if (!$vehicule) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        DB::beginTransaction();
        try {
            // Supprimer les photos du véhicule
            $photos = json_decode($vehicule->photos, true);
            if ($photos) {
                foreach ($photos as $photo) {
                    if (file_exists(public_path($photo))) {
                        unlink(public_path($photo)); // Supprimer le fichier photo
                    } else {
                        $this->wasabiService->deleteFile($photo);
                    }
                }
            }

            // Supprimer le véhicule
            $vehicule->delete();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Véhicule supprimé avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la suppression du véhicule.");
            return back();
        }
    }

    /**
     * Importer des véhicules à partir d'un fichier Excel
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls',
        ]);

        $user = auth()->user();
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable.");
            return back();
        }
        
        // SÉCURITÉ: Vérifier que l'utilisateur est bien un gestionnaire de flotte
        if (empty($user->fleetOwnerId())) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "ID utilisateur invalide.");
            return back();
        }
        
        \Log::info("Import Excel initié par le gestionnaire de flotte ID: " . $user->fleetOwnerId() . " (" . $user->email . ")");

        $file = $request->file('excel_file');
        $spreadsheet = IOFactory::load($file->getRealPath());
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        // Log pour déboguer
        \Log::info('Nombre total de lignes dans le fichier Excel: ' . count($rows));
        \Log::info('Première ligne (en-têtes): ', $rows[1] ?? []);

        $header = array_map('strtolower', array_map('trim', $rows[1]));
        unset($rows[1]); // Supprimer la ligne d'en-tête
        
        // Log pour déboguer
        \Log::info('Nombre de lignes de données après suppression de l\'en-tête: ' . count($rows));
        \Log::info('En-têtes détectés: ', $header);

        $imported = 0;
        $updated = 0;
        $errors = [];
        $skipped = 0;
        $created = [
            'type_de_vehicule' => [],
            'marque' => [],
            'type_de_carburant' => [],
            'couleur_vehicule' => []
        ];

        foreach ($rows as $rowIndex => $row) {
            // Log pour chaque ligne traitée
            \Log::info("Traitement de la ligne $rowIndex: ", $row);
            
            // Vérifier si la ligne n'est pas vide
            if (empty(array_filter($row))) {
                \Log::info("Ligne $rowIndex ignorée car vide");
                $skipped++;
                continue;
            }
            
            $data = array_combine($header, $row);

            // Vérification des champs obligatoires
            if (empty($data['matricule']) || empty($data['carte_grise']) || empty($data['modele']) || empty($data['mobile'])) {
                $errorMsg = "Ligne $rowIndex ignorée (matricule, carte grise, modèle ou mobile manquant) - Matricule: " . ($data['matricule'] ?? 'vide') . ", Carte grise: " . ($data['carte_grise'] ?? 'vide') . ", Modèle: " . ($data['modele'] ?? 'vide') . ", Mobile: " . ($data['mobile'] ?? 'vide');
                $errors[] = $errorMsg;
                \Log::warning($errorMsg);
                continue;
            }

            // Vérifier unicité et gérer les doublons avec vérification de propriété
            $existingVehicule = Vehicule::where('matricule', $data['matricule'])->orWhere('carte_grise', $data['carte_grise'])->first();
            
            if ($existingVehicule) {
                // Vérifier que le véhicule existant appartient au gestionnaire connecté
                if ($existingVehicule->gestionnaire_de_flotte_id != $user->fleetOwnerId()) {
                    $errorMsg = "Ligne $rowIndex ignorée (véhicule appartient à un autre gestionnaire) : " . ($data['matricule'] ?? '');
                    $errors[] = $errorMsg;
                    \Log::warning($errorMsg . " - Gestionnaire propriétaire: " . $existingVehicule->gestionnaire_de_flotte_id . ", Utilisateur connecté: " . $user->fleetOwnerId());
                    continue;
                }
                
                // Option 1: Mettre à jour le véhicule existant (seulement s'il appartient au bon gestionnaire)
                $updateMode = true; // Vous pouvez rendre cela configurable
                
                if ($updateMode) {
                    \Log::info("Mise à jour du véhicule existant ligne $rowIndex: " . $data['matricule'] . " (appartient au gestionnaire " . $user->fleetOwnerId() . ")");
                    // Continuer avec la mise à jour au lieu de créer un nouveau véhicule
                } else {
                    $errorMsg = "Ligne $rowIndex ignorée (matricule ou carte grise déjà existant) : " . ($data['matricule'] ?? '');
                    $errors[] = $errorMsg;
                    \Log::warning($errorMsg);
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                // Gestion du type de véhicule
                $type_de_vehicule = !empty($data['type_de_vehicule']) ?
                    Type_de_vehicule::where('libelle', $data['type_de_vehicule'])->first() :
                    Type_de_vehicule::first();

                if (!$type_de_vehicule && !empty($data['type_de_vehicule'])) {
                    $type_de_vehicule = new Type_de_vehicule();
                    $type_de_vehicule->libelle = $data['type_de_vehicule'];
                    // Supprimer la ligne statut car la colonne n'existe pas
                    $type_de_vehicule->save();
                    $created['type_de_vehicule'][] = $data['type_de_vehicule'];
                }

                // Gestion de la marque
                $marque = !empty($data['marque']) ?
                    Marque::where('libelle', $data['marque'])->first() :
                    Marque::first();

                if (!$marque && !empty($data['marque'])) {
                    $marque = new Marque();
                    $marque->libelle = $data['marque'];
                    // Supprimer la ligne statut car la colonne n'existe pas
                    $marque->save();
                    $created['marque'][] = $data['marque'];
                }

                // Gestion du type de carburant
                $type_de_carburant = !empty($data['type_de_carburant']) ?
                    Type_de_carburant::where('libelle', $data['type_de_carburant'])->first() :
                    Type_de_carburant::first();

                if (!$type_de_carburant && !empty($data['type_de_carburant'])) {
                    $type_de_carburant = new Type_de_carburant();
                    $type_de_carburant->libelle = $data['type_de_carburant'];
                    // Supprimer la ligne statut car la colonne n'existe pas
                    $type_de_carburant->save();
                    $created['type_de_carburant'][] = $data['type_de_carburant'];
                }

                // Gestion de la couleur
                $couleur_vehicule = !empty($data['couleur_vehicule']) ?
                    Couleur_vehicule::where('libelle', $data['couleur_vehicule'])->first() :
                    Couleur_vehicule::first();

                if (!$couleur_vehicule && !empty($data['couleur_vehicule'])) {
                    $couleur_vehicule = new Couleur_vehicule();
                    $couleur_vehicule->libelle = $data['couleur_vehicule'];
                    // Supprimer la ligne statut car la colonne n'existe pas
                    $couleur_vehicule->save();
                    $created['couleur_vehicule'][] = $data['couleur_vehicule'];
                }

                // Création ou mise à jour du véhicule
                $isUpdate = false;
                if (isset($existingVehicule) && $existingVehicule) {
                    // Mise à jour du véhicule existant
                    $vehicule = $existingVehicule;
                    $isUpdate = true;
                    \Log::info("Mise à jour du véhicule existant: " . $vehicule->matricule);
                } else {
                    // Création d'un nouveau véhicule
                    $vehicule = new Vehicule();
                    \Log::info("Création d'un nouveau véhicule: " . $data['matricule']);
                }

                $mobile = str_replace(' ', '', $data['mobile']);
                $chauffeurMobile = Chauffeur::where('mobile', $mobile)->first();

                if (empty($chauffeurMobile)) {
                    $errorMsg = "Ligne $rowIndex ignorée (aucun chauffeur trouvé avec le mobile: " . $data['mobile'] . ") - Matricule: " . ($data['matricule'] ?? '');
                    $errors[] = $errorMsg;
                    \Log::warning($errorMsg);
                    continue;
                }

                $chauffeurId = $chauffeurMobile->id;

                $vehicule->matricule = $data['matricule'];
                $vehicule->carte_grise = $data['carte_grise'];
                $vehicule->modele = $data['modele'];
                $vehicule->chauffeur_id = $chauffeurId;
                $vehicule->type_de_vehicule_id = $type_de_vehicule->id;
                $vehicule->marque_id = $marque->id;
                $vehicule->type_de_carburant_id = $type_de_carburant->id;
                $vehicule->couleur_vehicule_id = $couleur_vehicule->id;
                $vehicule->user_id = $chauffeurId; // Ajouter le champ user_id manquant
                // SÉCURITÉ: S'assurer que le véhicule appartient au gestionnaire connecté
                $vehicule->gestionnaire_de_flotte_id = $user->fleetOwnerId();
                \Log::info("Assignation du véhicule " . $data['matricule'] . " au gestionnaire " . $user->fleetOwnerId());
                
                $vehicule->photos = json_encode([]); // Pas de photos via Excel
                $vehicule->provenance_by = 1;
                $vehicule->provenance = 'flotte';
                $vehicule->save();
                
                // Vérification post-sauvegarde pour s'assurer de l'assignation
                if ($vehicule->gestionnaire_de_flotte_id != $user->fleetOwnerId()) {
                    throw new \Exception("Erreur de sécurité: Le véhicule n'a pas été assigné au bon gestionnaire");
                }

                DB::commit();
                
                if ($isUpdate) {
                    $updated++;
                    \Log::info("Véhicule mis à jour avec succès - Ligne $rowIndex: " . $data['matricule']);
                } else {
                    $imported++;
                    \Log::info("Véhicule créé avec succès - Ligne $rowIndex: " . $data['matricule']);
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $errorMsg = "Erreur lors de l'importation du véhicule ligne $rowIndex (" . ($data['matricule'] ?? '') . ") : " . $e->getMessage();
                $errors[] = $errorMsg;
                \Log::error($errorMsg);
                \Log::error("Stack trace: " . $e->getTraceAsString());
            }
        }

        $totalRows = count($rows) + 1; // +1 pour la ligne d'en-tête
        $totalProcessed = $imported + $updated;
        
        if ($imported > 0 && $updated > 0) {
            $msg = "$imported véhicule(s) créé(s) et $updated véhicule(s) mis à jour avec succès sur $totalRows ligne(s) traitées.";
        } elseif ($imported > 0) {
            $msg = "$imported véhicule(s) créé(s) avec succès sur $totalRows ligne(s) traitées.";
        } elseif ($updated > 0) {
            $msg = "$updated véhicule(s) mis à jour avec succès sur $totalRows ligne(s) traitées.";
        } else {
            $msg = "Aucun véhicule traité sur $totalRows ligne(s).";
        }
        
        if ($skipped > 0) {
            $msg .= "\n$skipped ligne(s) ignorée(s) (vides).";
        }

        // Ajouter les informations sur les éléments créés
        foreach ($created as $type => $elements) {
            if (!empty($elements)) {
                $msg .= "\n" . count($elements) . " nouveau(x) " . str_replace('_', ' ', $type) . "(s) créé(s) : " . implode(', ', $elements);
            }
        }

        if (count($errors)) {
            $msg .= "\nErreurs rencontrées (" . count($errors) . ") :\n" . implode("\n", $errors);
            
            // Compter les erreurs spécifiques de chauffeur non trouvé
            $chauffeurNotFoundErrors = array_filter($errors, function($error) {
                return strpos($error, 'aucun chauffeur trouvé') !== false;
            });
            
            if (count($chauffeurNotFoundErrors) > 0) {
                $msg .= "\n\n⚠️ ATTENTION: " . count($chauffeurNotFoundErrors) . " ligne(s) ignorée(s) car aucun chauffeur n'a été trouvé avec le numéro de mobile fourni.";
                $msg .= "\n💡 SOLUTION: Vérifiez que les numéros de mobile dans votre fichier Excel correspondent exactement à ceux des chauffeurs existants dans le système.";
            }
        }
        
        // SÉCURITÉ: Vérification finale - s'assurer que tous les véhicules importés appartiennent au bon gestionnaire
        if ($totalProcessed > 0) {
            $vehiculesImportes = Vehicule::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
                                        ->where('provenance', 'flotte')
                                        ->where('provenance_by', 1)
                                        ->orderBy('updated_at', 'desc')
                                        ->limit($totalProcessed)
                                        ->get();
            
            $vehiculesIncorrects = $vehiculesImportes->where('gestionnaire_de_flotte_id', '!=', $user->fleetOwnerId());
            
            if ($vehiculesIncorrects->count() > 0) {
                \Log::error("ERREUR DE SÉCURITÉ: " . $vehiculesIncorrects->count() . " véhicule(s) n'appartiennent pas au bon gestionnaire!");
                $msg .= "\n⚠️ ATTENTION: Erreur de sécurité détectée - certains véhicules n'appartiennent pas au bon gestionnaire.";
            } else {
                \Log::info("Vérification de sécurité OK: Tous les véhicules appartiennent au gestionnaire " . $user->fleetOwnerId());
            }
        }
        
        // Log final
        \Log::info("Import terminé - Créés: $imported, Mis à jour: $updated, Erreurs: " . count($errors) . ", Ignorées: $skipped, Total lignes: $totalRows, Gestionnaire: " . $user->fleetOwnerId());

        session()->flash('type', $totalProcessed > 0 ? 'alert-success' : 'alert-danger');
        session()->flash('message', $msg);
        return back();
    }

    /**
     * Télécharger le modèle Excel pour l'importation des véhicules
     */
    public function downloadTemplate()
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Définir les en-têtes
            $headers = [
                'A1' => 'matricule',
                'B1' => 'carte_grise',
                'C1' => 'modele',
                'D1' => 'type_de_vehicule',
                'E1' => 'marque',
                'F1' => 'type_de_carburant',
                'G1' => 'couleur_vehicule',
                'H1' => 'mobile'
            ];

            // Ajouter les en-têtes
            foreach ($headers as $cell => $value) {
                $sheet->setCellValue($cell, $value);
            }

            // Ajouter des exemples de données
            $examples = [
                ['ABC123', 'CG123456', 'Toyota Land Cruiser', '4X4', 'Toyota', 'Essence', 'Noir', '0700000000'],
                ['XYZ789', 'CG789012', 'Peugeot 3008', 'SUV', 'Peugeot', 'Diesel', 'Blanc', '0700000000'],
                ['DEF456', 'CG654321', 'Renault Talisman', 'BERLINE', 'Renault', 'Essence', 'Gris', '0700000000']
            ];

            $row = 2;
            foreach ($examples as $example) {
                $sheet->fromArray($example, null, 'A' . $row);
                $row++;
            }

            // Ajuster la largeur des colonnes
            foreach (range('A', 'H') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Créer le dossier s'il n'existe pas
            if (!file_exists(public_path('templates'))) {
                mkdir(public_path('templates'), 0777, true);
            }

            // Sauvegarder le fichier
            $writer = new Xlsx($spreadsheet);
            $filePath = public_path('templates/modele_vehicules.xlsx');
            $writer->save($filePath);

            // Vérifier si le fichier existe
            if (!file_exists($filePath)) {
                throw new \Exception('Le fichier n\'a pas pu être créé');
            }

            // Retourner le fichier pour téléchargement
            return response()->download($filePath, 'modele_vehicules.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la génération du modèle : ' . $e->getMessage());
        }
    }

}
