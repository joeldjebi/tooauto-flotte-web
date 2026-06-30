<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carburant extends Model
{
    protected $table = 'carburants';

    protected $fillable = [
        'gestionnaire_de_flotte_id',
        'vehicule_id',
        'chauffeur_id',
        'type_de_carburant_id',
        'type_carburant',
        'date_approvisionnement',
        'kilometrage',
        'quantite_litres',
        'prix_unitaire',
        'montant_total',
        'station',
        'reference',
        'mode_paiement',
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

    public function type_de_carburant()
    {
        return $this->belongsTo(Type_de_carburant::class, 'type_de_carburant_id');
    }
}
