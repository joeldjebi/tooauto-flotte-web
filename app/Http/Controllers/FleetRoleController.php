<?php

namespace App\Http\Controllers;

use App\Models\FleetRole;
use App\Models\MenuFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class FleetRoleController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "L'utilisateur est introuvable.");
            return back();
        }

        if (!Schema::hasTable('fleet_roles') || !Schema::hasTable('fleet_role_menu_features') || !Schema::hasTable('menu_features')) {
            session()->flash('type', 'alert-warning');
            session()->flash('message', "Crée d'abord les tables des rôles depuis le fichier SQL.");

            return back();
        }

        $menuFeatures = collect(MenuFeature::items())->map(function ($feature, $key) {
            return (object) [
                'key' => $key,
                'libelle' => $feature['libelle'],
                'groupe' => $feature['groupe'],
            ];
        })->groupBy('groupe');

        $roles = FleetRole::where('gestionnaire_de_flotte_id', $user->fleetOwnerId())
            ->withCount('admin_users')
            ->orderBy('libelle')
            ->paginate(15);

        $featureLabels = collect(MenuFeature::items())->mapWithKeys(fn ($feature, $key) => [$key => $feature['libelle']]);

        $roles->getCollection()->transform(function ($role) use ($featureLabels) {
            $keys = \Illuminate\Support\Facades\DB::table('fleet_role_menu_features')
                ->where('fleet_role_id', $role->id)
                ->pluck('menu_feature_key');

            $role->feature_keys = $keys->toArray();
            $role->feature_labels = $keys->map(fn ($key) => $featureLabels[$key] ?? $key)->values();

            return $role;
        });

        return view('roles.index', [
            'title' => 'Liste des rôles',
            'menu' => 'roles',
            'roles' => $roles,
            'menu_features' => $menuFeatures,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRole($request);

        $role = FleetRole::create([
            'gestionnaire_de_flotte_id' => Auth::user()->fleetOwnerId(),
            'libelle' => $validated['libelle'],
            'description' => $validated['description'] ?? null,
            'statut' => $request->boolean('statut', true) ? 1 : 0,
        ]);

        $this->syncPermissions($role, $request->input('menu_features', []));

        session()->flash('type', 'alert-success');
        session()->flash('message', "Rôle créé avec succès.");

        return back();
    }

    public function update(Request $request, $id)
    {
        $role = FleetRole::where('gestionnaire_de_flotte_id', Auth::user()->fleetOwnerId())->find($id);

        if (!$role) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Rôle introuvable.");

            return back();
        }

        $validated = $this->validateRole($request);

        $role->update([
            'libelle' => $validated['libelle'],
            'description' => $validated['description'] ?? null,
            'statut' => $request->boolean('statut') ? 1 : 0,
        ]);

        $this->syncPermissions($role, $request->input('menu_features', []));

        session()->flash('type', 'alert-success');
        session()->flash('message', "Rôle mis à jour avec succès.");

        return back();
    }

    public function destroy($id)
    {
        $role = FleetRole::where('gestionnaire_de_flotte_id', Auth::user()->fleetOwnerId())
            ->withCount('admin_users')
            ->find($id);

        if (!$role) {
            session()->flash('type', 'alert-danger');
            session()->flash('message', "Rôle introuvable.");

            return back();
        }

        if ($role->admin_users_count > 0) {
            session()->flash('type', 'alert-warning');
            session()->flash('message', "Ce rôle est déjà utilisé par des utilisateurs.");

            return back();
        }

        $role->menu_features()->detach();
        $role->delete();

        session()->flash('type', 'alert-success');
        session()->flash('message', "Rôle supprimé avec succès.");

        return back();
    }

    protected function validateRole(Request $request): array
    {
        return $request->validate([
            'libelle' => 'required|string|max:160',
            'description' => 'nullable|string|max:500',
            'menu_features' => 'nullable|array',
            'menu_features.*' => 'string',
        ]);
    }

    protected function syncPermissions(FleetRole $role, array $features): void
    {
        $allowedFeatures = array_keys(MenuFeature::items());
        $features = array_values(array_intersect($features, $allowedFeatures));

        $role->menu_features()->sync($features);
    }
}
