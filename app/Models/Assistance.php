<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assistance extends Model
{
    protected $table = 'assistances';

    protected $fillable = [
        'gestionnaire_de_flotte_id',
        'vehicule_id',
        'chauffeur_id',
        'type_assistance',
        'titre',
        'description',
        'lieu',
        'latitude',
        'longitude',
        'niveau_urgence',
        'prestataire_id',
        'prestataire_nom',
        'date_demande',
        'date_intervention',
        'date_cloture',
        'statut',
        'commentaire',
    ];

    public function gestionnaire_de_flotte()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'gestionnaire_de_flotte_id');
    }

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class, 'vehicule_id');
    }

    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class, 'chauffeur_id');
    }
}
