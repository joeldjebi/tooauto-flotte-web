<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    protected $table = 'vehicules';

    protected $fillable = [
        'matricule',
        'carte_grise',
        'modele',
        'mobile',
        'chauffeur_id',
        'type_de_vehicule_id',
        'marque_id',
        'type_de_carburant_id',
        'couleur_vehicule_id',
        'user_id',
        'gestionnaire_de_flotte_id',
        'photos',
        'provenance_by',
        'provenance'
    ];

    public function marque()
    {
        return $this->belongsTo(Marque::class, 'marque_id');
    }

    public function type_de_vehicule()
    {
        return $this->belongsTo(Type_de_vehicule::class, 'type_de_vehicule_id');
    }

    public function type_de_carburant()
    {
        return $this->belongsTo(Type_de_carburant::class, 'type_de_carburant_id');
    }

    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'gestionnaire_de_flotte_id');
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'user_id');
    }

    public function couleur_vehicule()
    {
        return $this->belongsTo(Couleur_vehicule::class, 'couleur_vehicule_id');
    }
}