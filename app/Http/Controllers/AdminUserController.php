<?php

namespace App\Http\Controllers;

use App\Models\FleetRole;
use App\Models\GestionnaireDeFlotte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminUserController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        if (!Schema::hasColumn('gestionnaire_de_flottes', 'parent_gestionnaire_id') || !Schema::hasColumn('gestionnaire_de_flottes', 'fleet_role_id')) {
            session()->flash('type', 'alert-warning');
            session()->flash('message', "Crée d'abord les colonnes des users admin depuis le fichier SQL.");
            return back();
        }

        $ownerId = $user->fleetOwnerId();

        $adminUsers = GestionnaireDeFlotte::where('parent_gestionnaire_id', $ownerId)
            ->with('fleet_role')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $roles = Schema::hasTable('fleet_roles')
            ? FleetRole::where('gestionnaire_de_flotte_id', $ownerId)->where('statut', 1)->orderBy('libelle')->get()
            : collect();

        return view('admin-users.index', [
            'title' => 'Users admin',
            'menu' => 'admin-users',
            'adminUsers' => $adminUsers,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $ownerId = Auth::user()->fleetOwnerId();

        $validated = $request->validate([
            'nom' => 'required|string|max:160',
            'prenoms' => 'required|string|max:160',
            'mobile' => 'required|string|max:20|unique:gestionnaire_de_flottes,mobile',
            'email' => 'nullable|email|max:255|unique:gestionnaire_de_flottes,email',
            'fleet_role_id' => 'required|integer',
            'password' => 'nullable|string|min:8',
        ]);

        $role = $this->findRole($validated['fleet_role_id'], $ownerId);
        if (!$role) {
            return back()->withInput()->with(['type' => 'alert-danger', 'message' => 'Rôle invalide.']);
        }

        GestionnaireDeFlotte::create([
            'nom' => $validated['nom'],
            'prenoms' => $validated['prenoms'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password'] ?? str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT)),
            'role' => '02',
            'parent_gestionnaire_id' => $ownerId,
            'fleet_role_id' => $role->id,
            'statut' => 1,
        ]);

        return back()->with(['type' => 'alert-success', 'message' => 'User admin créé avec succès.']);
    }

    public function update(Request $request, $id)
    {
        $ownerId = Auth::user()->fleetOwnerId();
        $adminUser = GestionnaireDeFlotte::where('parent_gestionnaire_id', $ownerId)->find($id);

        if (!$adminUser) {
            return back()->with(['type' => 'alert-danger', 'message' => 'User admin introuvable.']);
        }

        $validated = $request->validate([
            'nom' => 'required|string|max:160',
            'prenoms' => 'required|string|max:160',
            'mobile' => 'required|string|max:20|unique:gestionnaire_de_flottes,mobile,' . $adminUser->id,
            'email' => 'nullable|email|max:255|unique:gestionnaire_de_flottes,email,' . $adminUser->id,
            'fleet_role_id' => 'required|integer',
            'password' => 'nullable|string|min:8',
            'statut' => 'nullable|in:0,1',
        ]);

        $role = $this->findRole($validated['fleet_role_id'], $ownerId);
        if (!$role) {
            return back()->withInput()->with(['type' => 'alert-danger', 'message' => 'Rôle invalide.']);
        }

        $adminUser->nom = $validated['nom'];
        $adminUser->prenoms = $validated['prenoms'];
        $adminUser->mobile = $validated['mobile'];
        $adminUser->email = $validated['email'] ?? null;
        $adminUser->fleet_role_id = $role->id;
        $adminUser->statut = (int) ($validated['statut'] ?? 1);

        if (!empty($validated['password'])) {
            $adminUser->password = Hash::make($validated['password']);
        }

        $adminUser->save();

        return back()->with(['type' => 'alert-success', 'message' => 'User admin mis à jour avec succès.']);
    }

    public function destroy($id)
    {
        $ownerId = Auth::user()->fleetOwnerId();
        $adminUser = GestionnaireDeFlotte::where('parent_gestionnaire_id', $ownerId)->find($id);

        if (!$adminUser) {
            return back()->with(['type' => 'alert-danger', 'message' => 'User admin introuvable.']);
        }

        $adminUser->delete();

        return back()->with(['type' => 'alert-success', 'message' => 'User admin supprimé avec succès.']);
    }

    protected function findRole($roleId, int $ownerId): ?FleetRole
    {
        if (!Schema::hasTable('fleet_roles')) {
            return null;
        }

        return FleetRole::where('gestionnaire_de_flotte_id', $ownerId)
            ->where('statut', 1)
            ->find($roleId);
    }
}
