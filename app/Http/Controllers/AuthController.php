<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Redirector;
use Session;
use App\Models\GestionnaireDeFlotte;
use App\Models\Etablissement;
use App\Models\Station;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showlogin()
    {
        return view('auth.login');
    }

    /**
     * connexion des utilisateurs
     * @param Request $request
     */
    public function login(Request $request)
    {
        // Validation des entrées
        $request->validate([
            'mobile' => 'required|digits_between:8,20',
            'password' => 'required|string|min:8',
        ]);

        // Tentative d'authentification
        $credentials = $request->only('mobile', 'password');

        // Option "souviens-toi de moi"
        $remember = $request->filled('remember');

        // Tentative d'authentification avec le bon guard
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $gestionnaireDeFlotte = Auth::guard('web')->user(); // Récupère l'utilisateur authentifié

            $role = (string) $gestionnaireDeFlotte->role;
            $isAllowedBackofficeUser = in_array($role, ['01', '1', '02', '2'], true);
            $isActive = !isset($gestionnaireDeFlotte->statut) || (int) $gestionnaireDeFlotte->statut === 1;

            // Vérification du rôle et de l'existence d'un établissement
            if ($isAllowedBackofficeUser && $isActive) {
                if ($gestionnaireDeFlotte) {
                    return redirect()->route('dashboard'); // Redirection vers le tableau de bord
                } else {
                    // Si aucun établissement n'est trouvé
                    session()->flash('type', 'alert-danger');
                    session()->flash('message', "Aucun établissement trouvé pour cet utilisateur.");
                    Auth::guard('web')->logout();
                    return redirect('/login');
                }
            } else {
                // Rôle non autorisé
                session()->flash('type', 'alert-danger');
                session()->flash('message', "Votre rôle n'est pas autorisé à accéder à cette application.");
                Auth::guard('web')->logout();
                return redirect('/login');
            }
        } else {
            // En cas d'informations de connexion incorrectes
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Informations de connexion incorrectes.');
            return back();
        }
    }


     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showregister()
    {
        $data['title'] ='Inscriptions';
        $data['menu'] ='register';

        return view('auth.register',$data);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ecole  $ecole
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // Validation des champs
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|unique:gestionnaire_de_flottes',
            'email' => 'nullable|string|email|max:255|unique:gestionnaire_de_flottes',
            'password' => 'required|string|min:8|confirmed',
            'cgu' => 'accepted', // Vérifie que les conditions générales sont acceptées
        ]);

        // Création du gestionnaireDeFlotte utilisateur
        $gestionnaireDeFlotte = GestionnaireDeFlotte::create([
            'nom' => html_entity_decode($request->nom),
            'prenoms' => html_entity_decode($request->prenoms),
            'role' => '01', // Rôle par défaut
            'mobile' => html_entity_decode($request->mobile),
            'password' => Hash::make($request->password), // Hash du mot de passe
        ]);

        // Vérification si l'utilisateur a bien été créé
        if (!empty($gestionnaireDeFlotte)) {
            session()->flash('type', 'alert-success');
            session()->flash('message', 'Votre inscription a été effectuée avec succès');

            return redirect('/login');
        } else {
            session()->flash('type', 'alert-danger');
            session()->flash('message', 'Une erreur est survenue');

            return back();
        }
    }
	
	
	
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showpasswordforget()
    {
        return view('auth.password_forget');
    }

    /**
     * Envoie un SMS via l'API jaimeboutik
     *
     * @param string $to Numéro du destinataire (avec indicatif)
     * @param string $message Message à envoyer
     * @param string $senderId Nom ou numéro de l'expéditeur
     * @return array Réponse de l'API
     */
    private function sendSms($to, $message, $senderId = 'TOOauto')
    {
        $url = "http://jaimeboutik.com/API/?action=compose";
        $params = [
            'username' => 'tooauto',
            'api_key' => '059c00197bc410ccc377b9c68aaef51d:H7Jy5xiF3YvYSSopBdf0B6PiMYEcjLRT',
            'sender' => $senderId,
            'to' => $to,
            'message' => $message,
            'mms' => 0,
            'unicode' => 0,
            'media' => '',
        ];

        $response = Http::get($url, $params);

        return [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json(),
        ];
    }

    /**
     * Traite la demande de mot de passe oublié et envoie un OTP par SMS
     */
    public function postPasswordForget(Request $request)
    {
        $request->validate([
            'phone' => 'required',
        ]);

        $fullPhone = '+225' . $request->phone;
        $phone = $request->phone;
        $otp = rand(100000, 999999);
        session(['otp' => $otp, 'otp_phone' => $fullPhone, 'phone' => $phone]);
        $message = "Votre code de réinitialisation est : $otp";
        $smsResult = $this->sendSms($fullPhone, $message);

        if ($smsResult['status'] == 200) {
            return redirect()->route('auth.otp')->with('success', 'Le code OTP a été envoyé par SMS.');
        } else {
            return back()->with('error', "Erreur lors de l'envoi du SMS. Veuillez réessayer.");
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $otpSaisi = $request->otp;
        $otpSession = session('otp');
        $phoneSession = session('otp_phone');
        $phone = session('phone');
        if ($otpSaisi == $otpSession) {
            session(['otp_verified' => true]);
            return redirect()->route('password.reset.form')->with('success', 'Code OTP vérifié. Veuillez choisir un nouveau mot de passe.');
        } else {
            return back()->with('error', 'Code OTP incorrect. Veuillez réessayer.');
        }
    }

    public function resetPassword(Request $request)
    {
        // Vérifier que l'utilisateur a bien validé l'OTP
        if (!session('otp_verified') || !session('otp_phone')) {
            return redirect()->route('auth.otp')->with('error', 'Veuillez valider le code OTP.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $phone = session('phone');
        $user = \App\Models\GestionnaireDeFlotte::where('mobile', $phone)->first();
        if (!$user) {
            return back()->with('error', 'Utilisateur introuvable.');
        }
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->save();

        // Nettoyer la session OTP
        session()->forget(['otp', 'otp_phone', 'otp_verified']);

        return redirect()->route('login')->with('success', 'Mot de passe réinitialisé avec succès. Vous pouvez vous connecter.');
    }


}
