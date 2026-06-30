<?php

namespace App\Http\Controllers;

use App\Models\Autodoc;
use App\Models\Vehicule;
use App\Models\Chauffeur;
use App\Models\GestionnaireDeFlotte;
use App\Models\Type_de_carburant;
use App\Models\Marque;
use App\Models\Type_de_vehicule;
use App\Models\Type_de_piece;
use App\Models\Type_docauto;
use App\Services\WasabiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class AutodocController extends Controller
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
        $data['title'] ='Liste des autodocs';
        $data['menu'] ='autodocs';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Récupérer les établissements triés par ID décroissant
        $data['autodocs'] = Autodoc::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
        ->with('type_docauto', 'gestionnaire_de_flotte', 'vehicule')
        ->orderBy('id', 'desc')->paginate(15);

        $data['autodocs']->getCollection()->transform(function ($autodoc) {
            $autodoc->file_url_map = $this->resolveAutodocFileUrlMap($autodoc);

            return $autodoc;
        });

        $data['type_docautos'] = Type_docauto::orderBy('id', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
						->orderBy('id', 'desc')->get();


        return view('autodoc.index', $data);

    }

    protected function resolveAutodocFileUrlMap(Autodoc $autodoc)
    {
        $files = json_decode($autodoc->images, true) ?? [];
        $urls = [];

        foreach ($files as $file) {
            $urls[$file] = $this->resolveAutodocFileUrl($file, $autodoc);
        }

        return $urls;
    }

    protected function resolveAutodocFileUrl($file, Autodoc $autodoc)
    {
        if (empty($file)) {
            return null;
        }

        if (filter_var($file, FILTER_VALIDATE_URL)) {
            return $file;
        }

        if (file_exists(public_path($file))) {
            return asset($file);
        }

        if (($autodoc->provenance_by ?? null) != 1 && ($autodoc->provenance ?? null) !== 'wasabi') {
            return 'https://api-usager.tooauto.com/' . ltrim($file, '/');
        }

        return $this->wasabiService->temporaryUrl($file) ?: $this->wasabiService->publicUrl($file);
    }

    /**
     * Display a listing of the resource.
     */
    public function add()
    {
        $data['title'] ='Ajouter un autodoc';
        $data['menu'] ='autodocs';

        $data['user'] = Auth::user();
        if (empty($data['user'])) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        // Récupérer les établissements triés par ID décroissant
        $data['autodocs'] = Autodoc::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
        ->with('type_docauto', 'gestionnaire_de_flotte', 'vehicule')
        ->orderBy('id', 'desc')->paginate(15);

        // dd($data['autodocs']);

        $data['type_docautos'] = Type_docauto::orderBy('id', 'desc')->get();
        $data['vehicules'] = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())
						->orderBy('id', 'desc')->get();


        return view('autodoc.add', $data);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'vehicule_id' => 'required|exists:vehicules,id',
            'type_docauto_id' => 'required|exists:type_docautos,id',
            'images' => 'required|array|min:1|max:4',
            'images.*' => 'file|mimes:jpeg,png,jpg,pdf,heic,heif|max:5048',
        ], [
            'images.required' => 'Veuillez sélectionner au moins un document.',
            'images.array' => 'Le format des documents n\'est pas valide.',
            'images.min' => 'Veuillez sélectionner au moins un document.',
            'images.max' => 'Vous ne pouvez pas sélectionner plus de 4 documents.',
            'images.*.file' => 'Le fichier n\'est pas valide.',
            'images.*.mimes' => 'Les fichiers doivent être au format : jpeg, png, jpg, pdf, heic ou heif.',
            'images.*.max' => 'La taille maximale de chaque fichier est de 5 Mo.'
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

        $vehicule = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($request->vehicule_id);
        if (!$vehicule) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }
        // dd($vehicule);

        DB::beginTransaction();
        $uploadedFiles = [];
        try {
            // Création du véhicule
            $autodoc = new Autodoc();
            $autodoc->vehicule_id = $request->vehicule_id;
            $autodoc->type_docauto_id = $request->type_docauto_id;
            $autodoc->user_id = $vehicule->user_id;
            $autodoc->gestionnaire_de_flotte_id = $user->fleetOwnerId();
            $autodoc->provenance_by = 1;

            $files = $request->file('images');
            $filesPaths = [];

            if (!empty($files)) {
                $count = 0;

                foreach ($files as $file) {
                    if ($count >= 4) {
                        break;
                    }

                    $path = $this->wasabiService->uploadAutodocFile($file);
                    $filesPaths[] = $path;
                    $uploadedFiles[] = $path;

                    $count++;
                }

                // Stocker les chemins des fichiers en format JSON
                $autodoc->images = json_encode($filesPaths);
            }

            $autodoc->save();
            DB::commit();

            $message = "📋 *Notification TooAuto*\n\n";
            $message .= "Bonjour,\n\n";
            $message .= "Votre document a été enregistré avec succès dans notre système.\n\n";
            $message .= "📄 *Détails du document :*\n";
            $message .= "• Véhicule : " . $vehicule->matricule . "\n";
            $message .= "• Type de document : " . $autodoc->type_docauto->libelle . "\n\n";
            $message .= "Merci de votre confiance.\n\n";
            $message .= "---\n";
            $message .= "🚗 *TooAuto* - Gestion de flotte automobile";
            $mobile = $this->formatPhoneNumber($vehicule->chauffeur->mobile);
            $this->sendWhatsAppMessageInternal($mobile, $message);

            session()->flash('type', 'alert-success');
            session()->flash('message', $message);
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedFiles as $file) {
                $this->wasabiService->deleteFile($file);
            }

            // Gestion de l'exception
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de l'enregistrement du document : " . $e->getMessage());
            return back();
        }
    }


    public function update(Request $request, $id)
    {
        // Validation des données d'entrée
        $validator = Validator::make($request->all(), [
            'vehicule_id' => 'required|exists:vehicules,id',
            'type_docauto_id' => 'required|exists:type_docautos,id',
            'images' => 'nullable|array|min:1|max:4',
            'images.*' => 'nullable|file|mimes:jpeg,png,jpg,pdf,heic,heif|max:5048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $autodoc = Autodoc::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);

        if (!$autodoc) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Doc auto introuvable.");
            return back();
        }

        $user = auth()->user();

        // Vérifier si l'utilisateur existe
        if (empty($user)) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Utilisateur introuvable.");
            return back();
        }
        $vehicule = Vehicule::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($request->vehicule_id);
        if (!$vehicule) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        DB::beginTransaction();
        $uploadedFiles = [];
        try {
            // Mettre à jour les champs du véhicule
            $autodoc->vehicule_id = $request->vehicule_id;
            $autodoc->type_docauto_id = $request->type_docauto_id;
            $autodoc->user_id = $vehicule->user_id;
            $autodoc->provenance_by = 1;

            // Si des images sont envoyées, on les traite
            if ($request->hasFile('images')) {
                $images = $request->file('images');
                $imagesPaths = [];

                // Supprimer les anciennes images si elles existent
                $oldimages = json_decode($autodoc->images, true) ?? [];
                foreach ($oldimages as $oldPhoto) {
                    if (file_exists(public_path($oldPhoto))) {
                        unlink(public_path($oldPhoto));
                    } else {
                        $this->wasabiService->deleteFile($oldPhoto);
                    }
                }

                // Ajouter les nouvelles images
                foreach ($images as $image) {
                    $path = $this->wasabiService->uploadAutodocFile($image);
                    $imagesPaths[] = $path;
                    $uploadedFiles[] = $path;
                }

                // Mettre à jour le champ images
                $autodoc->images = json_encode($imagesPaths);
            }

            $autodoc->save();
            DB::commit();

            $message = "📋 *Notification TooAuto*\n\n";
            $message .= "Bonjour, " . $vehicule->chauffeur->nom . " " . $vehicule->chauffeur->prenoms . "\n\n";
            $message .= "Votre document a été mis à jour avec succès dans notre système.\n\n";
            $message .= "📄 *Détails du document :*\n";
            $message .= "• Véhicule : " . $vehicule->matricule . "\n";
            $message .= "• Type de document : " . $autodoc->type_docauto->libelle . "\n\n";
            $message .= "Merci de votre confiance.\n\n";
            $message .= "---\n";
            $message .= "🚗 *TooAuto* - Gestion de flotte automobile";
            $mobile = $this->formatPhoneNumber($vehicule->chauffeur->mobile);
            $this->sendWhatsAppMessageInternal($mobile, $message);

            session()->flash('type', 'alert-success');
            session()->flash('message', $message);
            return back();
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedFiles as $file) {
                $this->wasabiService->deleteFile($file);
            }

            session()->flash('type', 'alert-danger');
            session()->flash('message', "Une erreur est survenue lors de la mise à jour de doc auto : " . $e->getMessage());
            return back();
        }
    }

    public function destroy($id)
    {
        $autodoc = Autodoc::where('gestionnaire_de_flotte_id', $this->currentFleetOwnerId())->find($id);

        if (!$autodoc) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Véhicule introuvable.");
            return back();
        }

        DB::beginTransaction();
        try {
            // Supprimer les images du véhicule
            $images = json_decode($autodoc->images, true);
            if ($images) {
                foreach ($images as $photo) {
                    if (file_exists(public_path($photo))) {
                        unlink(public_path($photo)); // Supprimer le fichier photo
                    } else {
                        $this->wasabiService->deleteFile($photo);
                    }
                }
            }

            // Supprimer le véhicule
            $autodoc->delete();
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
     * Méthode pour vérifier et formater le numéro de téléphone
     * Si le numéro fait 10 chiffres, ajoute le préfixe '225'
     */
    private function formatPhoneNumber($phone)
    {
        // Supprimer tous les espaces, tirets et autres caractères non numériques
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        
        // Vérifier si le numéro fait exactement 10 chiffres
        if (strlen($cleanPhone) === 10) {
            // Ajouter le préfixe '225' au début
            return '225' . $cleanPhone;
        }
        
        // Retourner le numéro tel quel s'il ne fait pas 10 chiffres
        return $phone;
    }

    /**
     * Méthode interne pour envoyer un message WhatsApp
     */
    private function sendWhatsAppMessageInternal($phone, $message)
    {
        // Configuration de l'API Wassenger
        $apiUrl = 'https://api.wassenger.com/v1/messages';
        $token = '11aa75a1de8f22a6c05e5b49eeb309b48329258699f05e419624bff1d0fcc9940058293b92a6fc95';
        
        // Données à envoyer
        $data = [
            'phone' => $phone,
            'message' => $message
        ];

        // Initialisation de cURL
        $curl = curl_init();

        // Configuration des options cURL
        curl_setopt_array($curl, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Token: ' . $token
            ],
        ]);

        // Exécution de la requête
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);

        // Fermeture de cURL
        curl_close($curl);

        // Retour de la réponse
        if ($error) {
            return [
                'success' => false,
                'error' => $error,
            ];
        }

        $responseData = json_decode($response, true);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'response' => $responseData,
        ];
    }

}
