<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FleetRole extends Model
{
    protected $table = 'fleet_roles';

    protected $fillable = [
        'gestionnaire_de_flotte_id',
        'libelle',
        'description',
        'statut',
    ];

    public function menu_features()
    {
        return $this->belongsToMany(MenuFeature::class, 'fleet_role_menu_features', 'fleet_role_id', 'menu_feature_key', 'id', 'key')
            ->withTimestamps();
    }

    public function admin_users()
    {
        return $this->hasMany(GestionnaireDeFlotte::class, 'fleet_role_id');
    }
}
