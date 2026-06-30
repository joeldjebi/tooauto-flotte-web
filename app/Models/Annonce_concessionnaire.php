<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Annonce_concessionnaire extends Model
{
    // Spécifie le nom de la table
    protected $table = 'annonce_concessionnaires';

    // Attributs pouvant être assignés en masse
    protected $fillable = [
        'type_de_demande_id',
        'type_de_vehicule_id',
        'marque_id',
        'modele',
        'user_id',
        'concessionaire_id',
        'statut',
        'gestionnaire_de_flotte_id'
    ];

    public function type_de_demande()
    {
        return $this->belongsTo(Type_de_demande::class, 'type_de_demande_id');
    }

    public function type_de_vehicule()
    {
        return $this->belongsTo(Type_de_vehicule::class, 'type_de_vehicule_id');
    }
    
    public function marque()
    {
        return $this->belongsTo(Marque::class, 'marque_id');
    }

    public function user()
    {
        return $this->belongsTo(GestionnaireDeFlotte::class, 'user_id');
    }

    public function concessionnaire()
    {
        return $this->belongsTo(UserConcessionnaire::class, 'concessionaire_id');
    }
}