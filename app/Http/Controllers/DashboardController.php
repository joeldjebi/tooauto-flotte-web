<?php

namespace App\Http\Controllers;

use App\Models\GestionnaireDeFlotte;
use App\Models\UserConcessionnaire;
use App\Models\Fonction;
use App\Models\Chauffeur;
use App\Models\Categorie_piece;
use App\Models\Garage_flotte;
use App\Models\Type_de_piece;
use App\Models\Concessionnaire;
use App\Models\Type_de_vehicule;
use App\Models\Type_de_demande;
use App\Models\Vehicule_concessionnaire;
use App\Models\Annonce_concessionnaire;
use App\Models\Annonce;
use App\Models\Article;
use App\Models\Rdv_concessionnaire;
use App\Models\Sous_categorie_piece;
use App\Models\OffreConcessionnaire;
use App\Models\Marque;
use App\Models\Alert;
use App\Models\Vehicule;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function __construct(
        protected WasabiService $wasabiService
    ) {
    }


    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $data['title'] = 'Tableau de bord';
        $data['menu'] = 'dashboard';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        // Statistiques des alertes
        $data['alertes_count'] = [
            'assurance' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 1)->count(),
            'vidange' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 2)->count(),
            'visite' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 3)->count(),
            'controle' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 4)->count(),
        ];

        // Alertes par type
        $alertRelations = ['vehicule.marque', 'vehicule.type_de_vehicule', 'vehicule.type_de_carburant', 'type_alert'];
        $data['alertes_assurance'] = $this->attachVehiclePhotoUrls(Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 1)->with($alertRelations)->orderBy('date_fin', 'asc')->get());
        $data['alertes_vidange'] = $this->attachVehiclePhotoUrls(Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 2)->with($alertRelations)->orderBy('date_fin', 'asc')->get());
        $data['alertes_visite'] = $this->attachVehiclePhotoUrls(Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 3)->with($alertRelations)->orderBy('date_fin', 'asc')->get());
        $data['alertes_controle'] = $this->attachVehiclePhotoUrls(Alert::where('gestionnaire_de_flotte_id', $data['user']->id)->where('type_alert_id', 4)->with($alertRelations)->orderBy('date_fin', 'asc')->get());

        // Alertes proches de l'expiration (10 plus proches, expirées ou non)
        $data['alertes_proches'] = Alert::where('gestionnaire_de_flotte_id', $data['user']->id)
            ->orderBy('date_fin', 'asc')
            ->with($alertRelations)
            ->limit(10)
            ->get();
        $data['alertes_proches'] = $this->attachVehiclePhotoUrls($data['alertes_proches']);

        // Utiliser $data['user'] pour accéder au gestionnaire de flotte
        $data['chauffeurs'] = Chauffeur::where('gestionnaire_de_flotte_id', $data['user']->id)  // Corrigé ici
            ->where('statut', '!=', 2)
            ->with('gestionnaire_de_flotte')
            ->get();

        $today = Carbon::today();
        $in30 = Carbon::today()->addDays(30);

        $data['alertes_critique'] = [
            'assurance' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)
                ->where('type_alert_id', 1)
                ->where(function($q) use ($today, $in30) {
                    $q->where('date_fin', '<', $today)
                      ->orWhereBetween('date_fin', [$today, $in30]);
                })->count(),
            'vidange' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)
                ->where('type_alert_id', 2)
                ->where(function($q) use ($today, $in30) {
                    $q->where('date_fin', '<', $today)
                      ->orWhereBetween('date_fin', [$today, $in30]);
                })->count(),
            'visite' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)
                ->where('type_alert_id', 3)
                ->where(function($q) use ($today, $in30) {
                    $q->where('date_fin', '<', $today)
                      ->orWhereBetween('date_fin', [$today, $in30]);
                })->count(),
            'controle' => Alert::where('gestionnaire_de_flotte_id', $data['user']->id)
                ->where('type_alert_id', 4)
                ->where(function($q) use ($today, $in30) {
                    $q->where('date_fin', '<', $today)
                      ->orWhereBetween('date_fin', [$today, $in30]);
                })->count(),
        ];

        // Retourner la vue avec les données
        return view('dashboard', $data);
    }

    public function documentation()
    {
        return view('documentation.index', [
            'title' => 'Documentation',
            'menu' => 'documentation',
        ]);
    }

    protected function attachVehiclePhotoUrls($alertes)
    {
        $alertes->each(function ($alerte) {
            if (!empty($alerte->vehicule)) {
                $alerte->vehicule->photo_url_map = $this->resolveVehiclePhotoUrlMap($alerte->vehicule);
            }
        });

        return $alertes;
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


    /**
     * Show the form for creating a new resource.
     */
    public function indexPieceAuto()
    {
        $data['title'] ='Pièce auto';
        $data['menu'] ='piece-auto';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['categorie_pieces'] = Categorie_piece::orderBy('id', 'desc')->get();
        $data['type_de_pieces'] = Type_de_piece::orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['sous_categorie_pieces'] = Sous_categorie_piece::orderBy('id', 'desc')->get();

        $data['annonces'] = Annonce::whereIn('gestionnaire_de_flotte_id', $this->currentFleetAccountIds())
        ->where(function ($query) {
            $query->where('statut', 1)
                ->orWhereNull('statut');
        })
        ->with('categorie_piece', 'type_de_piece', 'sous_categorie_piece', 'marque')
        ->paginate(15);

        $data['annonces']->getCollection()->transform(function ($annonce) {
            $annonce->image_url = $this->resolvePieceImageUrl($annonce->image);

            return $annonce;
        });

        // dd($data['categorie_pieces'], $data['type_de_pieces'], $data['sous_categorie_pieces'], $data['marques']);

        return view('pieceauto.index', $data);

    }

    protected function resolvePieceImageUrl($image)
    {
        if (empty($image)) {
            return null;
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (file_exists(public_path('images/annonce/' . $image))) {
            return asset('images/annonce/' . $image);
        }

        if (file_exists(public_path($image))) {
            return asset($image);
        }

        return $this->wasabiService->temporaryUrl($image) ?: $this->wasabiService->publicUrl($image);
    }

    protected function currentFleetAccountIds(): array
    {
        $ownerId = $this->currentFleetOwnerId();

        if (!$ownerId) {
            return [];
        }

        return GestionnaireDeFlotte::where('id', $ownerId)
            ->orWhere('parent_gestionnaire_id', $ownerId)
            ->pluck('id')
            ->push($ownerId)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function addPieceAuto()
    {
        $data['title'] ='Ajouter une pièce auto';
        $data['menu'] ='piece-auto';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['categorie_pieces'] = Categorie_piece::orderBy('id', 'desc')->get();
        $data['type_de_pieces'] = Type_de_piece::orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['sous_categorie_pieces'] = Sous_categorie_piece::orderBy('id', 'desc')->get();

        $data['annonces'] = Annonce::whereIn('gestionnaire_de_flotte_id', $this->currentFleetAccountIds())
        ->where(function ($query) {
            $query->where('statut', 1)
                ->orWhereNull('statut');
        })
        ->with('categorie_piece', 'type_de_piece', 'sous_categorie_piece', 'marque')
        ->paginate(15);

        // dd($data['categorie_pieces'], $data['type_de_pieces'], $data['sous_categorie_pieces'], $data['marques']);

        return view('pieceauto.add', $data);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function editPieceAuto($id)
    {
        $data['title'] ='Modifier une pièce auto';
        $data['menu'] ='piece-auto';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['categorie_pieces'] = Categorie_piece::orderBy('id', 'desc')->get();
        $data['type_de_pieces'] = Type_de_piece::orderBy('id', 'desc')->get();
        $data['marques'] = Marque::orderBy('id', 'desc')->get();
        $data['sous_categorie_pieces'] = Sous_categorie_piece::orderBy('id', 'desc')->get();

        $data['item'] = Annonce::whereIn('gestionnaire_de_flotte_id', $this->currentFleetAccountIds())
            ->where('id', $id)
            ->first();

        if (!empty($data['item'])) {
            $data['item']->image_url = $this->resolvePieceImageUrl($data['item']->image);
        }

        // dd($data['categorie_pieces'], $data['type_de_pieces'], $data['sous_categorie_pieces'], $data['marques']);

        return view('pieceauto.edit', $data);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function storePieceAuto(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'libelle' => 'required|string', // Correction de 'non' à 'nom'
            'type_de_piece_id' => 'required|exists:type_de_pieces,id',
            'marque_id' => 'required|exists:marques,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'categorie_piece_id' => 'required|exists:categorie_pieces,id',
            'sous_categorie_piece_id' => 'required|exists:sous_categorie_pieces,id',
            'modele' => 'required|string',
        ]);

        // Récupérer l'utilisateur authentifié
        $gestionnaireDeFlotte = $this->currentFleetOwner();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $annonce = new Annonce();

        if ($request->hasFile('image')) {
            try {
                $annonce->image = $this->wasabiService->uploadPieceImage($request->file('image'));
            } catch (\Throwable $e) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "L'image n'a pas pu être envoyée sur Wasabi : " . $e->getMessage());

                return back()->withInput();
            }
        }

        $annonce->libelle = $request->libelle;
        $annonce->description = $request->description;
        $annonce->type_de_piece_id = $request->type_de_piece_id;
        $annonce->marque_id = $request->marque_id;
        $annonce->type_etablissement_id = 1;
        $annonce->modele = $request->modele;
        $annonce->gestionnaire_de_flotte_id = $gestionnaireDeFlotte->id;
        $annonce->statut = 1;
        $annonce->categorie_piece_id = $request->categorie_piece_id;
        $annonce->sous_categorie_piece_id = $request->sous_categorie_piece_id;

        // Sauvegarde du chauffeur
        if ($annonce->save()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "Annonce créé avec succès.");
            return back();
        } else {
            // Si la sauvegarde échoue
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la création de l'annonce.");
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updatePieceAuto(Request $request, $id)
    {
        // Validation des entrées
        $request->validate([
            'libelle' => 'required|string', // Correction de 'non' à 'nom'
            'type_de_piece_id' => 'required|exists:type_de_pieces,id',
            'marque_id' => 'required|exists:marques,id',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'categorie_piece_id' => 'required|exists:categorie_pieces,id',
            'sous_categorie_piece_id' => 'required|exists:sous_categorie_pieces,id',
            'modele' => 'required|string',
        ]);

        // Récupérer l'utilisateur authentifié
        $gestionnaireDeFlotte = $this->currentFleetOwner();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $annonce = Annonce::whereIn('gestionnaire_de_flotte_id', $this->currentFleetAccountIds())->find($id);

        if (!$annonce) {
            return response()->json([
                'success' => false,
                'message' => 'Annonce introuvable.',
            ], 404);
        }

        // Sauvegarde des photos
        if ($request->hasFile('image')) {
            if ($annonce->image && file_exists(public_path('images/annonce/' . $annonce->image))) {
                unlink(public_path('images/annonce/' . $annonce->image));
            } elseif ($annonce->image) {
                $this->wasabiService->deleteFile($annonce->image);
            }

            try {
                $annonce->image = $this->wasabiService->uploadPieceImage($request->file('image'));
            } catch (\Throwable $e) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "L'image n'a pas pu être envoyée sur Wasabi : " . $e->getMessage());

                return back()->withInput();
            }
        }

        $annonce->libelle = $request->libelle;
        $annonce->description = $request->description;
        $annonce->type_de_piece_id = $request->type_de_piece_id;
        $annonce->marque_id = $request->marque_id;
        $annonce->modele = $request->modele;
        $annonce->gestionnaire_de_flotte_id = $gestionnaireDeFlotte->id;
        $annonce->statut = 1;
        $annonce->categorie_piece_id = $request->categorie_piece_id;
        $annonce->sous_categorie_piece_id = $request->sous_categorie_piece_id;

        // Sauvegarde du chauffeur
        if ($annonce->save()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "Annonce mise a jour avec succès.");
            return back();
        } else {
            // Si la sauvegarde échoue
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la mise a jour de l'annonce.");
            return back();
        }
    }

    public function destroyPieceAuto($id)
    {
        $annonce = Annonce::whereIn('gestionnaire_de_flotte_id', $this->currentFleetAccountIds())->find($id);

        if (!$annonce) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        DB::beginTransaction();
        try {
            // Supprimer les images du véhicule
            if ($annonce->image && file_exists(public_path('images/annonce/' . $annonce->image))) {
                unlink(public_path('images/annonce/' . $annonce->image));
            } elseif ($annonce->image) {
                $this->wasabiService->deleteFile($annonce->image);
            }

            // Supprimer le véhicule
            $annonce->delete();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Annonce supprimé avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la suppression de l'annonce.");
            return back();
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function indexGarage()
    {
        $data['title'] ='Prestataires';
        $data['menu'] ='garage-auto';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['garage_flottes'] = Garage_flotte::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
        ->where('statut', 1)
        ->paginate(15);

        return view('garage.index', $data);

    }

    /**
     * Stocke un nouveau garage dans la base.
     */
    public function storeGarage(Request $request)
    {
        $validatedData = $request->validate([
            'name'          => 'required|string|max:200',
            // On n'inclut plus 'gestionnaire_de_flotte_id' dans la validation,
            // car on va le forcer à être l'id de l'utilisateur authentifié.
            'adresse'       => 'nullable|string|max:500',
            'adresse_map'   => 'nullable|string|max:500',
            'contact'       => 'nullable|string|max:20',
        ]);

        // Forcer l'id de l'utilisateur authentifié comme gestionnaire_de_flotte_id
        $validatedData['gestionnaire_de_flotte_id'] = $this->currentFleetOwnerId();

        Garage_flotte::create($validatedData);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Prestataire créé avec succès.");
        return back();
    }

    /**
     * Met à jour un garage existant.
     */
    public function updateGarage(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('garage_flottes')->ignore($id),
            ],
            // On n'inclut plus 'gestionnaire_de_flotte_id' dans la validation.
            'adresse'       => 'nullable|string|max:500',
            'adresse_map'   => 'nullable|string|max:500',
            'contact'       => 'nullable|string|max:20',
        ]);

        // Forcer l'id de l'utilisateur authentifié comme gestionnaire_de_flotte_id
        $validatedData['gestionnaire_de_flotte_id'] = $this->currentFleetOwnerId();

        $garage = Garage_flotte::findOrFail($id);
        $garage->update($validatedData);

        session()->flash('type', 'alert-success');
        session()->flash('message', "Prestataire mis à jour avec succès.");
        return back();
    }

    public function destroyGarage($id)
    {
        $garage = Garage_flotte::findOrFail($id);
        $garage->delete();
        session()->flash('type', 'alert-danger');
        session()->flash('message', "Prestataire supprimé avec succès.");
        return back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexArticle()
    {
        $data['title'] ='Article auto';
        $data['menu'] ='article-auto';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['articles'] = Article::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->paginate(15);

        return view('article.index', $data);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeArticle(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'libelle' => 'required|string', // Correction de 'non' à 'nom'
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'amount' => 'required',
        ]);

        // Récupérer l'utilisateur authentifié
        $gestionnaireDeFlotte = Auth::user();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $article = new Article();

        // Gestion de l'image
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = 'image-' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/article'), $imageName);
            $imagePath = $imageName;
            $article->image = $imagePath;
        }

        $article->libelle = $request->libelle;
        $article->description = $request->description;
        $article->amount = $request->amount;
        $article->gestionnaire_de_flotte_id = Auth::user()->id;
        $article->created_by = Auth::user()->id;

        // Sauvegarde du chauffeur
        if ($article->save()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "article créé avec succès.");
            return back();
        } else {
            // Si la sauvegarde échoue
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la création de l'article.");
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateArticle(Request $request, $id)
    {
        // Validation des entrées
        $request->validate([
            'libelle' => 'required|string', // Correction de 'non' à 'nom'
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'amount' => 'required',
        ]);

        // Récupérer l'utilisateur authentifié
        $gestionnaireDeFlotte = Auth::user();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'success' => false,
                'message' => 'article introuvable.',
            ], 404);
        }

        // Sauvegarde des photos
        if ($request->hasFile('image')) {
            $image = $request->file('image');

            // Supprimer l'ancienne image si elle existe
            if ($article->image && file_exists(public_path('images/article/' . $article->image))) {
                unlink(public_path('images/article/' . $article->image));
            }

            // Générer un nouveau nom pour l'image
            $imageName = 'article-' . time() . '.' . $image->getClientOriginalExtension();

            // Déplacer l'image dans le répertoire des images
            $image->move(public_path('images/article'), $imageName);

            // Mettre à jour le champ image pour l'utilisateur
            $article->image = $imageName;
        }

        $article->libelle = $request->libelle;
        $article->description = $request->description;
        $article->amount = $request->amount;

        // Sauvegarde du chauffeur
        if ($article->save()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "article mise a jour avec succès.");
            return back();
        } else {
            // Si la sauvegarde échoue
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la mise a jour de l'article.");
            return back();
        }
    }

    public function destroyArticle($id)
    {
        $article = Article::find($id);

        if (!$article) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        DB::beginTransaction();
        try {
            // Supprimer les images du véhicule
            $images = json_decode($article->image, true);
            if ($images) {
                foreach ($images as $image) {
                    if (file_exists(public_path($image))) {
                        unlink(public_path($image)); // Supprimer le fichier photo
                    }
                }
            }

            // Supprimer le véhicule
            $article->delete();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "article supprimé avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la suppression de l'article.");
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexConcessionnaire()
    {
        $data['title'] ='Liste des concessionnaire';
        $data['menu'] ='index-concessionnaire';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['type_de_demandes'] = Type_de_demande::all();
        $data['type_de_pieces'] = Type_de_piece::all();
        $data['type_de_vehicules'] = Type_de_vehicule::all();
        $data['marques'] = Marque::all();
        $data['concessionnaires'] = Concessionnaire::where('statut', 1)->paginate(15);

        return view('concessionnaire.index', $data);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexOffreConcessionnaire()
    {
        $data['title'] ='Liste des offres';
        $data['menu'] ='index-offre-concessionnaire';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['offres'] = OffreConcessionnaire::where('user_id', $this->currentFleetOwnerId())
        ->with('user', 'concessionnaire')
        ->orderBy('id', 'desc')
        ->get();

        // dd($data['offres']);

        return view('concessionnaire.offre_concessionnaire', $data);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexConcessionnaireHistoriqueRdv($id)
    {
        $data['title'] ='Liste des rdv';
        $data['menu'] ='rdv-concessionnaire';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['type_de_demandes'] = Type_de_demande::all();
        $data['type_de_pieces'] = Type_de_piece::all();
        $data['type_de_vehicules'] = Type_de_vehicule::all();
        $data['marques'] = Marque::all();
        $data['concessionnaires'] = Concessionnaire::where('statut', 1)->get();
        $data['concessionnaire'] = Concessionnaire::find($id);

        $data['rdv_concessionnaires'] = Rdv_concessionnaire::where(['gestionnaire_de_flotte_id' => $this->currentFleetOwnerId(), 'concessionnaire_id' => $id])
        ->with('gestionnaire_de_flotte')
        ->paginate(15);


        return view('concessionnaire.rdv', $data);
    }

    public function destroyRdvConcessionnaire($id)
    {
        $rdv = Rdv_concessionnaire::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);
        if (!$rdv) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Rendez-vous introuvable.");
            return back();
        }
        if ($rdv->delete()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "Rendez-vous supprimé avec succès.");
            return back();
        } else {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la suppression du rendez-vous.");
            return back();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function indexConcessionnaireVehicule($id)
    {
        $data['title'] ='Liste des concessionnaire';
        $data['menu'] ='index-concessionnaire';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['concessionnaire'] = Concessionnaire::find($id);

        $data['vehicule_concessionnaires'] = Vehicule_concessionnaire::where('concessionnaire_id', $id)
        ->with('couleur_vehicule', 'marque')
        ->get();

        // dd($data['vehicule_concessionnaires']);

        return view('concessionnaire.vehicule', $data);

    }

    public function storeDemandeConcessionnaire(Request $request)
    {
        // Validation des données envoyées depuis le formulaire
        $validatedData = $request->validate([
            'type_de_demande_id'   => 'required|integer|exists:type_de_demandes,id',
            'type_de_vehicule_id'  => 'required|integer|exists:type_de_vehicules,id',
            'marque_id'            => 'required|integer|exists:marques,id',
            'modele'               => 'required|string|max:200',
            'concessionnaire_id'   => 'required|integer|exists:userconcessionnaires,id',
        ]);

        // Mapper les champs pour correspondre à la structure de la table
        $validatedData['user_id'] = $this->currentFleetOwnerId();
        $validatedData['concessionaire_id'] = $validatedData['concessionnaire_id'];
        $validatedData['statut'] = 1; // Statut par défaut
        $validatedData['gestionnaire_de_flotte_id'] = $this->currentFleetOwnerId();

        // Création de l'enregistrement dans la table annonce_concessionnaires
        $annonce = Annonce_concessionnaire::create($validatedData);

        // Redirection avec message de succès (vous pouvez adapter la redirection)
        session()->flash('type', 'alert-success');
        session()->flash('message', "Demande enregistrée avec succès.");
        return back();
    }

    public function updateDemandeConcessionnaire(Request $request, $id)
    {
        // Validation des données envoyées depuis le formulaire
        $validatedData = $request->validate([
            'type_de_demande_id'   => 'required|integer|exists:type_de_demandes,id',
            'type_de_vehicule_id'  => 'required|integer|exists:type_de_vehicules,id',
            'marque_id'            => 'required|integer|exists:marques,id',
            'modele'               => 'required|string|max:200',
            // 'concessionnaire_id'   => 'required|integer|exists:userconcessionnaires,id',
        ]);

        // Mapper les champs pour correspondre à la structure de la table
        $validatedData['user_id'] = $this->currentFleetOwnerId();
        // $validatedData['concessionaire_id'] = $validatedData['concessionnaire_id'];
        $validatedData['statut'] = 1; // Statut par défaut
        $validatedData['gestionnaire_de_flotte_id'] = $this->currentFleetOwnerId();

        // Création de l'enregistrement dans la table annonce_concessionnaires
        $annonce = Annonce_concessionnaire::find($id);
        $annonce->update($validatedData);

        // Redirection avec message de succès (vous pouvez adapter la redirection)
        session()->flash('type', 'alert-success');
        session()->flash('message', "Demande mise à jour avec succès.");
        return back();
    }

    public function destroyDemandeConcessionnaire($id)
    {
        $annonce = Annonce_concessionnaire::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);
        if (!$annonce) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Demande introuvable.");
            return back();
        }
        DB::beginTransaction();
        try {
            $annonce->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la suppression de la demande.");
            return back();
        }
        if ($annonce->delete()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "Demande supprimée avec succès.");
            return back();
        } else {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Demande supprimée avec succès..");
            return back();
        }
    }

    public function indexDemandeConcessionnaire()
    {
        $data['title'] ='Liste des demandes';
        $data['menu'] ='demande-concessionnaire';

        $data['demandes'] = Annonce_concessionnaire::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
        ->with('type_de_demande', 'type_de_vehicule', 'marque', 'user', 'concessionnaire')
        ->get();

        $data['type_de_demandes'] = Type_de_demande::all();
        $data['type_de_vehicules'] = Type_de_vehicule::all();
        $data['marques'] = Marque::all();
        
        return view('concessionnaire.index_demandes', $data);
    }


    public function storeRdvConcessionnaire(Request $request)
    {
        // Validation des données
        $validatedData = $request->validate([
            'jour' => 'required|string|in:Lundi,Mardi,Mercredi,Jeudi,Vendredi,Samedi,Dimanche',
            'heure' => 'required|date_format:Y-m-d\TH:i',
            'concessionnaire_id' => 'required|integer|exists:userconcessionnaires,id',
        ]);

        $validatedData['gestionnaire_de_flotte_id'] = $this->currentFleetOwnerId();

        // Enregistrement des données
        if ($rdv = Rdv_concessionnaire::create($validatedData)) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "Rendez-vous enregistré avec succès.");
            return back();
        } else {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de l'enregistrement du rendez-vous.");
            return back();
        }
    }






    /**
     * Show the form for creating a new resource.
     */
    public function indexFonction()
    {
        $data['title'] ='Les fonction';
        $data['menu'] ='fonction';

        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['fonctions'] = Fonction::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->paginate(15);

        return view('fonction.index', $data);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeFonction(Request $request)
    {
        // Récupérer l'utilisateur authentifié en premier
        $gestionnaireDeFlotte = Auth::user();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Validation des entrées
        $request->validate([
            'libelle' => 'required|string',
        ]);

        // Générer un libellé unique en ajoutant un suffixe si nécessaire
        $libelleOriginal = $request->libelle;
        $libelleFinal = $libelleOriginal;
        $compteur = 1;

        // Vérifier si le libellé existe déjà globalement
        while (Fonction::where('libelle', $libelleFinal)->exists()) {
            $libelleFinal = $libelleOriginal . ' (' . $compteur . ')';
            $compteur++;
        }

        // Alternative : Ajouter l'ID du gestionnaire si vous préférez
        // $libelleFinal = $libelleOriginal . ' - G' . $gestionnaireDeFlotte->id;

        $fonction = new Fonction();
        $fonction->libelle = $libelleFinal;
        $fonction->gestionnaire_de_flotte_id = $gestionnaireDeFlotte->id;

        // Sauvegarde de la fonction avec gestion d'erreur
        try {
            if ($fonction->save()) {
                $message = "Fonction créée avec succès.";
                if ($libelleFinal !== $libelleOriginal) {
                    $message .= " Le libellé a été modifié en '{$libelleFinal}' car '{$libelleOriginal}' existait déjà.";
                }
                session()->flash('type', 'alert-success');
                session()->flash('message', $message);
                return back();
            } else {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Erreur lors de la création de la fonction.");
                return back();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Gestion spécifique des erreurs de contrainte d'intégrité
            if ($e->getCode() == 23000) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Erreur de contrainte d'unicité. Veuillez réessayer.");
            } else {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Erreur lors de la création de la fonction : " . $e->getMessage());
            }
            return back();
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function updateFonction(Request $request, $id)
    {
        // Validation des entrées
        $request->validate([
            'libelle' => 'required|string',
        ]);

        // Récupérer l'utilisateur authentifié
        $gestionnaireDeFlotte = Auth::user();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $fonction = Fonction::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);

        if (!$fonction) {
            return response()->json([
                'success' => false,
                'message' => 'Fonction introuvable.',
            ], 404);
        }

        // Générer un libellé unique en ajoutant un suffixe si nécessaire
        $libelleOriginal = $request->libelle;
        $libelleFinal = $libelleOriginal;
        $compteur = 1;

        // Vérifier si le libellé existe déjà globalement (en excluant la fonction actuelle)
        while (Fonction::where('libelle', $libelleFinal)
            ->where('id', '!=', $id)
            ->exists()) {
            $libelleFinal = $libelleOriginal . ' (' . $compteur . ')';
            $compteur++;
        }

        // Alternative : Ajouter l'ID du gestionnaire si vous préférez
        // $libelleFinal = $libelleOriginal . ' - G' . $gestionnaireDeFlotte->id;

        $fonction->libelle = $libelleFinal;
        $fonction->gestionnaire_de_flotte_id = $gestionnaireDeFlotte->id;

        // Sauvegarde de la fonction avec gestion d'erreur
        try {
            if ($fonction->save()) {
                $message = "Fonction mise à jour avec succès.";
                if ($libelleFinal !== $libelleOriginal) {
                    $message .= " Le libellé a été modifié en '{$libelleFinal}' car '{$libelleOriginal}' existait déjà.";
                }
                session()->flash('type', 'alert-success');
                session()->flash('message', $message);
                return back();
            } else {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Erreur lors de la mise à jour de la fonction.");
                return back();
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // Gestion spécifique des erreurs de contrainte d'intégrité
            if ($e->getCode() == 23000) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Erreur de contrainte d'unicité. Veuillez réessayer.");
            } else {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Erreur lors de la mise à jour de la fonction : " . $e->getMessage());
            }
            return back();
        }
    }

    public function destroyFonction($id)
    {
        $fonction = Fonction::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);

        if (!$fonction) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Fonction introuvable.");
            return back();
        }

        DB::beginTransaction();
        try {
            // Supprimer de la fonction
            $fonction->delete();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', "Fonction supprimé avec succès.");
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la suppression de la fonction.");
            return back();
        }
    }

    /**
     * Affiche le formulaire de mise à jour du profil
     */
    public function profil()
    {
        $data['title'] = 'Mon profil';
        $data['menu'] = 'profil';
        $data['user'] = auth()->user();
        $data['profile_photo_url'] = $this->resolveProfilePhotoUrl($data['user']->photo ?? null);

        return view('profil.update', $data);
    }

    /**
     * Met à jour les informations du profil
     */
    public function updateProfil(Request $request)
    {
        $user = auth()->user();

        // Vérifier que l'utilisateur existe
        if (!$user) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Utilisateur non authentifié.');
            return back();
        }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|email|unique:gestionnaire_de_flottes,email,' . $user->id,
            'telephone' => 'required|string|max:20',
            'adresse' => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Mise à jour des informations de base
            $user->nom = $request->nom;
            $user->prenoms = $request->prenom;
            $user->email = $request->email;
            $user->mobile = $request->telephone;
            $user->adresse = $request->adresse;

            // Gestion de la photo
            if ($request->hasFile('photo')) {
                if ($user->photo && file_exists(public_path($user->photo))) {
                    unlink(public_path($user->photo));
                } elseif ($user->photo) {
                    $this->wasabiService->deleteFile($user->photo);
                }

                $user->photo = $this->wasabiService->uploadAvatar($request->file('photo'));
            }

            $user->save();
            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', 'Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log de l'erreur pour le débogage
            \Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Une erreur est survenue lors de la mise à jour du profil: ' . $e->getMessage());
        }

        return back();
    }

    protected function resolveProfilePhotoUrl($photo)
    {
        if (empty($photo)) {
            return asset('assets/img/profiles/avatar-01.jpg');
        }

        if (filter_var($photo, FILTER_VALIDATE_URL)) {
            return $photo;
        }

        if (file_exists(public_path($photo))) {
            return asset($photo);
        }

        return $this->wasabiService->temporaryUrl($photo) ?: ($this->wasabiService->publicUrl($photo) ?: asset('assets/img/profiles/avatar-01.jpg'));
    }

    /**
     * Affiche le formulaire de changement de mot de passe
     */
    public function password()
    {
        $data['title'] = 'Changer le mot de passe';
        $data['menu'] = 'password';
        $data['user'] = auth()->user();

        return view('profil.password', $data);
    }

    /**
     * Met à jour le mot de passe
     */
    public function updatepassword(Request $request)
    {
        $user = auth()->user();

        // Validation des données
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|current_password',
            'new_password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            'new_password_confirmation' => 'required|same:new_password'
        ], [
            'current_password.required' => 'Le mot de passe actuel est requis.',
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
            'new_password.required' => 'Le nouveau mot de passe est requis.',
            'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères.',
            'new_password.regex' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.',
            'new_password_confirmation.required' => 'La confirmation du mot de passe est requise.',
            'new_password_confirmation.same' => 'La confirmation du mot de passe ne correspond pas.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Mise à jour du mot de passe
            $user->password = Hash::make($request->new_password);
            $user->save();

            DB::commit();

            session()->flash('type', 'alert-success');
            session()->flash('message', 'Mot de passe mis à jour avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Une erreur est survenue lors de la mise à jour du mot de passe.');
        }

        return back();
    }

    public function annonceSent()
    {
        $data['title'] = 'Annonces envoyées';
        $data['menu'] = 'annonces-envoyees';


        $data["user"] = $this->currentFleetOwner();

        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable ou rôle non autorisé.");
            return back();
        }

        $data['offre_concessionnaires'] = OffreConcessionnaire::all();

        return view('annonce.concessionnaire', $data);
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
