<?php

namespace App\Http\Controllers;

use App\Models\Chauffeur;
use App\Models\GestionnaireDeFlotte;
use App\Models\Fonction;
use App\Models\Ville;
use App\Models\Vehicule;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class ChauffeurController extends Controller
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
        $data['title'] ='Liste des utilisateurs';
        $data['menu'] ='chauffeurs';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $fleetOwnerId = $data['user']->fleetOwnerId();

        $chauffeurQuery = Chauffeur::where('gestionnaire_de_flotte_id', $fleetOwnerId)
        ->where('statut', '!=', 2)
        ->where(function ($query) {
            $query->whereHas('vehicules', function ($q) {
                $q->where('gestionnaire_de_flotte_id', '!=', 0)
                  ->whereNotNull('gestionnaire_de_flotte_id');
            })
            ->orWhereDoesntHave('vehicules'); // Inclure les chauffeurs sans véhicule
        })
        ->with('gestionnaire_de_flotte', 'vehicules', 'fonction', 'ville')
        ->distinct();  // Limite les doublons

        $data['chauffeurs'] = $chauffeurQuery->paginate(15);

        $data['chauffeurs']->getCollection()->transform(function ($chauffeur) {
            $chauffeur->image_url = $this->resolveChauffeurImageUrl($chauffeur->image);
            $chauffeur->vehicules->transform(function ($vehicule) {
                $vehicule->photo_url_map = $this->resolveVehiclePhotoUrlMap($vehicule);

                return $vehicule;
            });

            return $chauffeur;
        });

        $data['fonctions'] = Fonction::where('gestionnaire_de_flotte_id', $fleetOwnerId)->get();
        $data['villes'] = Ville::all();

        //dd($data['chauffeurs']);

        return view('chauffeur.index', $data);
    }

    protected function resolveChauffeurImageUrl($image)
    {
        if (empty($image)) {
            return asset('assets/img/profiles/avatar-01.jpg');
        }

        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        if (file_exists(public_path('images/chauffeur/' . $image))) {
            return asset('images/chauffeur/' . $image);
        }

        if (file_exists(public_path($image))) {
            return asset($image);
        }

        return $this->wasabiService->temporaryUrl($image) ?: ($this->wasabiService->publicUrl($image) ?: asset('assets/img/profiles/avatar-01.jpg'));
    }

    protected function resolveVehiclePhotoUrlMap(Vehicule $vehicule): array
    {
        $photos = json_decode($vehicule->photos, true) ?? [];
        $urls = [];

        foreach ($photos as $photo) {
            $urls[$photo] = $this->resolveVehiclePhotoUrl($photo, $vehicule);
        }

        return $urls;
    }

    protected function resolveVehiclePhotoUrl($photo, Vehicule $vehicule): string
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'nom' => 'required|string', // Correction de 'non' à 'nom'
            'prenoms' => 'required|string',
            'mobile' => 'required|string|unique:chauffeurs',
            // 'password' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'fonction_id' => 'required|exists:fonctions,id',
            'ville_id' => 'required|exists:villes,id',
        ]);

        // Récupérer l'utilisateur authentifié
        $gestionnaireDeFlotte = Auth::user();

        // Vérification de la présence de l'utilisateur
        if (!$gestionnaireDeFlotte) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            try {
                $imagePath = $this->wasabiService->uploadChauffeurImage($request->file('image'));
            } catch (\Throwable $e) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "La photo n'a pas pu être envoyée sur Wasabi : " . $e->getMessage());

                return back()->withInput();
            }
        }

        $password = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);

        // Création du chauffeur
        $chauffeur = new Chauffeur();
        $chauffeur->nom = $request->nom;
        $chauffeur->prenoms = $request->prenoms;
        $chauffeur->mobile = $request->mobile;
        $chauffeur->fonction_id = $request->fonction_id;
        $chauffeur->ville_id = $request->ville_id;
        $chauffeur->password = Hash::make($password);
        $chauffeur->image = $imagePath;
        $chauffeur->gestionnaire_de_flotte_id = $gestionnaireDeFlotte->fleetOwnerId();

        // Sauvegarde du chauffeur
        if ($chauffeur->save()) {
            // Exemple d'utilisation
            $sender = "TOOauto";
            $recipients = "225".''.$request->mobile;
            $password = $password; // Génère un mot de passe aléatoire si nécessaire

            $response = $this->sendSmsMtarget($recipients, $password);

            session()->flash('type', 'alert-success');
            session()->flash('message', "Chauffeur créé avec succès.");
            return back();
        } else {
            // Si la sauvegarde échoue
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la création du chauffeur.");
            return back();
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validation des entrées
        $request->validate([
            'nom' => 'required|string',
            'prenoms' => 'required|string',
            'mobile' => 'required|string|unique:chauffeurs,mobile,' . $id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10048',
            'fonction_id' => 'required|exists:fonctions,id',
            'ville_id' => 'required|exists:villes,id',
        ]);

        // Récupérer le chauffeur à mettre à jour
        $chauffeur = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);

        // Vérifier si le chauffeur existe
        if (!$chauffeur) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Le chauffeur n'a pas été trouvé.");
            return back();
        }

        // Mise à jour des données
        $chauffeur->nom = $request->nom;
        $chauffeur->prenoms = $request->prenoms;
        $chauffeur->mobile = $request->mobile;
        $chauffeur->fonction_id = $request->fonction_id;
        $chauffeur->ville_id = $request->ville_id;
        if ($request->hasFile('image')) {
            if ($chauffeur->image && file_exists(public_path('images/chauffeur/' . $chauffeur->image))) {
                unlink(public_path('images/chauffeur/' . $chauffeur->image));
            } elseif ($chauffeur->image) {
                $this->wasabiService->deleteFile($chauffeur->image);
            }

            try {
                $chauffeur->image = $this->wasabiService->uploadChauffeurImage($request->file('image'));
            } catch (\Throwable $e) {
                session()->flash('type', 'alert-danger');
                session()->flash('message', "La photo n'a pas pu être envoyée sur Wasabi : " . $e->getMessage());

                return back()->withInput();
            }
        }

        // Sauvegarde des modifications
        if ($chauffeur->save()) {
            session()->flash('type', 'alert-success');
            session()->flash('message', "Chauffeur mis à jour avec succès.");
            return back();
        } else {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Erreur lors de la mise à jour du chauffeur.");
            return back();
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Récupérer le chauffeur
        $chauffeur = Chauffeur::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);
    
        // Vérifier si le chauffeur existe
        if (!$chauffeur) {
            return back()->with([
                'type' => 'alert-danger',
                'message' => "Le chauffeur n'a pas été trouvé."
            ]);
        }
    
        // Supprimer l'image si elle existe
        $imagePath = public_path('images/chauffeur/' . $chauffeur->image);
        if ($chauffeur->image && file_exists($imagePath)) {
            @unlink($imagePath); // @ pour éviter une erreur si déjà supprimée
        } elseif ($chauffeur->image) {
            $this->wasabiService->deleteFile($chauffeur->image);
        }
    
        // Suppression du chauffeur
        if ($chauffeur->delete()) {
            return back()->with([
                'type' => 'alert-success',
                'message' => "Chauffeur supprimé avec succès."
            ]);
        }
    
        return back()->with([
            'type' => 'alert-danger',
            'message' => "Erreur lors de la suppression du chauffeur."
        ]);
    }

    function sendSmsMtarget($msisdn, $password, $sender = 'TOO AUTO') {
        // URL de l'API MTarget
        $url = 'https://api-public-2.mtarget.fr/messages';
        $message = "Vos accès:\nUsername: $msisdn\nMot de passe: $password";
        
        // Vérifier et ajouter le signe '+' si nécessaire
        if (strpos($msisdn, '+') !== 0) {
            $msisdn = '+' . $msisdn;
        }
        
        // Paramètres d'authentification et de message
        $postData = http_build_query([
            'username' => 'bwantech',
            'password' => 'x7jyKG0IJRNH',
            'msisdn' => $msisdn,
            'msg' => $message,
            'sender' => $sender
        ]);

        // Initialisation de cURL
        $ch = curl_init();

        // Configuration des options cURL
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Pour récupérer la réponse
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_SSL_VERIFYPEER => false, // Désactiver la vérification SSL pour les tests
            CURLOPT_TIMEOUT => 30, // Timeout de 30 secondes
        ]);

        // Exécution de la requête
        $response = curl_exec($ch);
        // dd($response);

        // Gestion des erreurs
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new \Exception("Erreur cURL : " . $error);
        }

        // Récupération du code de statut HTTP
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Fermeture de la session cURL
        curl_close($ch);

        // Vérification du code de statut HTTP
        if ($httpCode !== 200) {
            throw new \Exception("Erreur HTTP : " . $httpCode . " - Réponse : " . $response);
        }

        // dd($response);

        return $response;
    }


}
