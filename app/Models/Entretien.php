<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entretien extends Model
{
    protected $table = 'entretiens';

    protected $fillable = [
        'gestionnaire_de_flotte_id',
        'vehicule_id',
        'chauffeur_id',
        'type_entretien',
        'titre',
        'description',
        'date_prevue',
        'date_realisation',
        'kilometrage',
        'cout',
        'prestataire',
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
